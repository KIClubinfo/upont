/* @ngInject */
class Assos_Simple_Ctrl {
    constructor($rootScope, $scope, club, members) {
        $scope.club = club;
        $scope.members = members;
        $scope.promo = $rootScope.config.promos.assos;
    }
}

export default Assos_Simple_Ctrl;
