import alertify from 'alertifyjs';

import { API_PREFIX } from 'upont/js/config/constants';

class Reset_Ctrl {
    constructor($scope, $http, $state, $stateParams) {
        $scope.reset = function(password, check) {
            if (password && check) {
                if (password == check) {
                    $http.post(API_PREFIX + 'resetting/token/' + $stateParams.token, {password: password, check: check}).then(
                        function(){
                            alertify.success('Mot de passe réinitialisé !');
                            $state.go('root.login');
                        }
                    );
                } else {
                    alertify.error('Les deux mots de passe ne sont pas identiques.');
                }
            } else {
                alertify.error('Au moins un des deux champs est vide.');
            }
        };
    }
}

export default Reset_Ctrl;
