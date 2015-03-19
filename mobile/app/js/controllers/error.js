module.controller('ErrorController', ['$scope', 'StorageService', '$http', function($scope, StorageService, $http) {
        $scope.refresh = function() {
            $http({method: 'HEAD', url: url + '/ping'})
                .success(function (data, status, headers, config) {
                    menu.setMainPage('views/events.html', {closeMenu: true});
                    menu.setSwipeable(true);
                });
        };
    }
]);
