angular.module('upont').controller('503_Ctrl', ['$scope', 'StorageService', function($scope, StorageService) {
        $scope.until = StorageService.get('maintenance');
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.404', {
                url: '404',
                    templateUrl: 'views/public/404.html',
            });
    }]);
