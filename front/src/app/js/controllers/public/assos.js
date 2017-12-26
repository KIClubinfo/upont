import './assos-simple.html';

class Assos_Public_Ctrl {
    constructor($scope, clubs) {
        $scope.clubs = [];
        $scope.assos = [];

        angular.forEach(clubs, function(value, key) {
            if (value.hasOwnProperty('category') && value.category === 'asso')
                $scope.assos.push(value);
            else
                $scope.clubs.push(value);
        });
    }
}

export default Assos_Public_Ctrl;
