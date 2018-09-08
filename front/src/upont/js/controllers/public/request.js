import alertify from 'alertifyjs';

import {API_PREFIX} from 'upont/js/config/constants';

/* @ngInject */
class Request_Ctrl {
    constructor($scope, $http) {
        $scope.request = function(username) {
            if (username) {
                $http.post(API_PREFIX + 'resetting/request', {username: username}).then(function() {
                    alertify.success('Mail de réinitialisation envoyé !');
                }, function() {
                    alertify.error('Identifiant non trouvé !');
                });
            } else {
                alertify.error('Donne ton identifiant !');
            }
        };
    }
}

export default Request_Ctrl;
