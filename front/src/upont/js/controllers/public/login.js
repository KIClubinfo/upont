import alertify from 'alertifyjs';

import {API_PREFIX} from 'upont/js/config/constants';

/* @ngInject */
class Login_Ctrl {
    constructor($scope, $rootScope, $state, $window, $location, $http, Permissions, Achievements) {
        $scope.formLogin = true;
        $scope.switchLoginMethod = function() {
            $scope.formLogin = !$scope.formLogin;
        };

        function getParameterByName(name, url) {
            if (!url)
                url = window.location.href;
            name = name.replace(/[\[\]]/g, '\\$&');
            const regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)');
            const results = regex.exec(url);
            if (!results)
                return null;
            if (!results[2])
                return '';
            return decodeURIComponent(results[2].replace(/\+/g, ' '));
        }

        function attachPostLogin(promise) {
            promise.then(function(response) {
                Permissions.set(response.data.token, response.data.data.roles);

                // Soyons polis
                if (Permissions.hasRight('ROLE_EXTERIEUR'))
                    alertify.success('Connecté avec succès !');
                else
                    alertify.success('Salut ' + response.data.data.first_name + ' !');
                Achievements.check();

                if (typeof $rootScope.urlRef !== 'undefined' && $rootScope.urlRef !== null && $rootScope.urlRef != '/') {
                    window.location.href = $rootScope.urlRef;
                    $rootScope.urlRef = null;
                } else {
                    $state.go('root.users.publications.list');
                }
            }, function() {
                // Supprime tout token en cas de mauvaise identification
                Permissions.remove();
                alertify.error('Mauvais identifiant. Soit l\'identifiant n\'existe pas, soit le mot de passe est incorrect.');
            });
        }

        const ticket = getParameterByName('ticket', $window.location.href);
        if (ticket) {
            attachPostLogin($http.get(API_PREFIX + 'login/sso?ticket=' + ticket));
        }

        $scope.loginSSO = function() {
            $window.location.href = 'https://cas.enpc.fr/cas/login?service=' + encodeURI(location.origin);
        };

        $scope.login = function(pseudo, password) {
            if (pseudo && password) {
                const promise = $http.post(API_PREFIX + 'login', {
                    username: pseudo,
                    password: password
                });

                attachPostLogin(promise);
            }
        };
    }
}

export default Login_Ctrl;
