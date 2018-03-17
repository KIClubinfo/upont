import alertify from 'alertifyjs';

import {API_PREFIX} from 'upont/js/config/constants';

/* @ngInject */
class Login_Ctrl {
    constructor($scope, AuthService) {
        $scope.login = () => {
            AuthService.startAuthentication();
        };
    }
}

export default Login_Ctrl;
