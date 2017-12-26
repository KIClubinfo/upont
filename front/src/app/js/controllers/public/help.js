angular.module('upont')
    .controller('Help_Ctrl', ['$scope', function ($scope) {
            $scope.displayedTab = "Chargement";

            $scope.setTab = function (tabId) {
                $scope.displayedTab = tabId;
            };

            $scope.isTab = function (tabId) {
                return $scope.displayedTab === tabId;
            };
        }]
    )
    .config(['$stateProvider', function ($stateProvider) {
        $stateProvider
            .state('root.public.help', {
                url: '/help',
                templateUrl: 'controllers/public/help.html',
                controller: 'Help_Ctrl',
                data: {
                    title: 'Aide - uPont',
                    top: true
                }
            });
    }]);
