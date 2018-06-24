/* @ngInject */
class Login_Ctrl {
    constructor($scope, OAuth2Service) {
        $scope.login = () => {
            OAuth2Service.startAuthentication();
        };
    }
}

export default Login_Ctrl;
