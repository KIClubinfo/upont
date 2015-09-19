angular.module('upont').controller('503_Ctrl', ['$scope', 'StorageService', function($scope, StorageService) {
        $scope.until = StorageService.get('maintenance');
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.maintenance', {
                url: 'maintenance',
                controller: '503_Ctrl',
                templateUrl: 'controllers/public/errors/503.html',
            });
    }]);
