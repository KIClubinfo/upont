angular.module('upont').controller('503_Ctrl', ['$scope', 'StorageService', function($scope, StorageService) {
    $scope.until = StorageService.get('maintenance');
}]);
