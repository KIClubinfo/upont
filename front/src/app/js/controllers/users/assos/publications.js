/* @ngInject */
class Assos_Publications_Ctrl {
    constructor($scope, events, newsItems) {
        $scope.events = events;
        $scope.newsItems = newsItems;
        $scope.reverse = false;
    }
}

export default Assos_Publications_Ctrl;
