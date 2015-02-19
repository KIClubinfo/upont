angular.module('upont').controller('503_Controller', ['$scope', 'StorageService', function($scope, StorageService) {
    $scope.until = StorageService.get('maintenance');
}]);
