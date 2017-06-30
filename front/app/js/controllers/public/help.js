angular.module('upont')
    .controller('Help_Ctrl', ['$scope', function ($scope) {
            $scope.displayedTab = "Chargement";

            $scope.switchDisplayedTab = function (tabId) {
                $scope.displayedTab = tabId;
                tablinks = $('.Navbar__link');
                for (i = 0; i < tablinks.length; i++) {
                    if (tablinks[i].id === tabId) {
                        tablinks[i].className += " active";
                    }
                    else {
                        tablinks[i].className = tablinks[i].className.replace(" active", "");   
                    }
                }
            };

            $scope.displayTab = function (tabId) {
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
