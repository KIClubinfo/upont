class Assos_Publications_Ctrl {
    constructor($scope, events, newsItems) {
        $scope.events = events;
        $scope.newsItems = newsItems;
        $scope.predicate = 'user.first_name';
        $scope.reverse = false;
        $scope.promo = '017';
    }
}

export default Assos_Publications_Ctrl;
