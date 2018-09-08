/* @ngInject */
class Help_Ctrl {
    constructor($scope) {
        $scope.displayedTab = "Chargement";

        $scope.setTab = function(tabId) {
            $scope.displayedTab = tabId;
        };

        $scope.isTab = function(tabId) {
            return $scope.displayedTab === tabId;
        };
    }
}

export default Help_Ctrl;
