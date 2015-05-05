angular.module('upont')
    .controller('SingleSignOn_Ctrl', [
        '$scope',
        '$rootScope',
        '$stateParams',
        '$location',
        '$http',
        '$window',
    function($scope, $rootScope, $stateParams, $location, $http, $window) {

        // Identify the external application
        switch($stateParams.appId) {
            case '3ce745a47e998d2461ed9132dc18979c':
                // GéoPonts by Mickaël Bergem
                var baseUrl = 'http://geoponts.enpc.org';
                $scope.app = {
                    name: "GéoPonts",
                    logo: "geoponts.png",
                    urlCallback: baseUrl + "/accounts/sso/post",
                    urlRedirect: baseUrl + "/accounts/sso/redirect"
                };
                break;
            default:
                alertify.alert("Application inconnue !");
                $location.path('/');
        }

        if (!$stateParams.to) {
            alertify.alert("Token manquant !");
            $location.path('/');
        }

        $scope.me = $rootScope.me;

        $scope.acceptAuth = function () {
            // The user just clicked on "Accept"

            var payload = {
                token: $stateParams.to,
                promo: $rootScope.me.promo,
                department: $rootScope.me.department,
                origin: $rootScope.me.origin,
                username: $rootScope.me.username,
                first_name: $rootScope.me.first_name,
                last_name: $rootScope.me.last_name,
                email: $rootScope.me.email,
                avatar: $rootScope.me.image_url,
                success: true
            };

            sendResponse($scope.app.urlCallback, payload);
        };

        $scope.denyAuth = function () {
            // The user just clicked on "Cancel"
            // TODO: log it into Piwik ?
            sendResponse($scope.app.urlCallback, {token: $stateParams.to, success: false});
        };

        var sendResponse = function (url, params) {
            $http.post(url, params)
                .success(function(){
                    $window.location.href = $scope.app.urlRedirect + '/' + $stateParams.to;
                })
                .error(function(data, status){
                    switch(status) {
                        case '404': alertify.error('GéoPonts dit que vous feriez mieux de réessayer, ce token ne lui dit rien du tout...'); break;
                        case '403': alertify.alert('Le jeton de connexion a expiré, vous avez été trop lent !\nMerci de réessayer :)'); break;
                        default: alertify.error('GéoPonts dit que la requête envoyée est incorrecte (' + data.message + ') !'); break;
                    }
                    console.log("Raison du rejet : " + data.message);
                }
            );
        };

    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state("root.sso", {
                url: 'sso?appId&to',
                templateUrl: "views/sso.html",
                controller: "SingleSignOn_Ctrl",
                data: {
                    title: 'Authentification centralisée - uPont',
                    top: true
                }
            });
    }]);
