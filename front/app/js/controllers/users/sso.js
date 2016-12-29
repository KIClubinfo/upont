angular.module('upont')
    .controller('SingleSignOn_Ctrl', [
        '$scope',
        '$rootScope',
        '$stateParams',
        '$location',
        '$http',
        '$window',
        'StorageService',
    function($scope, $rootScope, $stateParams, $location, $http, $window, StorageService) {
        // Identify the external application
        switch($stateParams.appId) {
            case '3ce745a47e998d2461ed9132dc18979c':
                // GéoPonts by Mickaël Bergem
                var baseUrl = 'https://geoponts.enpc.fr';
                $scope.app = {
                    name: 'GéoPonts',
                    logo: 'geoponts.png',
                    urlCallback: baseUrl + '/accounts/sso/post',
                    urlRedirect: baseUrl + '/accounts/sso/redirect'
                };
                break;
            default:
                alertify.alert('Application inconnue !');
                $location.path('/');
        }

        if (!$stateParams.to) {
            alertify.alert('Token manquant !');
            $location.path('/');
        }

        $scope.me = $rootScope.me;

        $scope.acceptAuth = function () {
            // The user just clicked on 'Accept'

            var payload = {
                token: $stateParams.to,
                auth: StorageService.get('token'),
                username: $rootScope.username,
                success: true
            };

            sendResponse($scope.app.urlCallback, payload);
        };

        $scope.denyAuth = function () {
            // The user just clicked on 'Cancel'
            sendResponse($scope.app.urlCallback, {token: $stateParams.to, success: false});
        };

        var sendResponse = function (url, params) {
            $http.post(url, params, {
                    skipAuthorization: true,
                })
                .then(
                    function(){
                        $window.location.href = $scope.app.urlRedirect + '/' + $stateParams.to;
                    },
                    function(response){
                        switch(response.status) {
                            case '404': alertify.error('GéoPonts dit que vous feriez mieux de réessayer, ce token ne lui dit rien du tout...'); break;
                            case '403': alertify.alert('Le jeton de connexion a expiré, vous avez été trop lent !\nMerci de réessayer :)'); break;
                            default: alertify.error('GéoPonts dit que la requête envoyée est incorrecte (' + response.data.message + ') !'); break;
                        }
                        console.log('Raison du rejet : ' + response.data.message);
                    }
                );
        };

    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.sso', {
                url: 'sso?appId&to',
                templateUrl: 'controllers/users/sso.html',
                controller: 'SingleSignOn_Ctrl',
                data: {
                    title: 'Authentification centralisée - uPont',
                    top: true
                },
                // Déclenchement de l'erreur 401 si non connecté
                resolve: {
                    online: ['$resource', function($resource) {
                        return $resource(apiPrefix + 'online').query().$promise;
                    }],
                }
            });
    }]);
