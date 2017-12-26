class Maintenance_Ctrl {
  constructor($scope, StorageService) {
    $scope.until = StorageService.get('maintenance');
  }
}

export default Maintenance_Ctrl;
