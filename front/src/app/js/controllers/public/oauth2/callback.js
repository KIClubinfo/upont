import alertify from 'alertifyjs';

/* @ngInject */
class OAuth2Callback_Ctrl {
    constructor($rootScope, $state, AuthService, Achievements) {
        AuthService.completeAuthentication().then((user) => {
            // Soyons polis
            if (!user.isStudent) {
                alertify.success('Connecté avec succès !');
            }
            else {
                alertify.success('Salut ' + user.first_name + ' !');
            }

            Achievements.check();

            if (typeof $rootScope.urlRef !== 'undefined' && $rootScope.urlRef !== null && $rootScope.urlRef != '/') {
                window.location.href = $rootScope.urlRef;
                $rootScope.urlRef = null;
            } else {
                $state.go('root.users.publications.list');
            }
        }, () => {
            alertify.error('Mauvais identifiant. Soit l\'identifiant n\'existe pas, soit le mot de passe est incorrect.');
            $state.go('root.login');
        });
    }
}

export default OAuth2Callback_Ctrl;
