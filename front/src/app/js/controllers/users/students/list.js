import constants from 'upont/js/config/constants';

/* @ngInject */
class Students_List_Ctrl {
    constructor($scope, users, Paginate) {
        $scope.PROMOS = constants.PROMOS;

        $scope.users = users;
        $scope.search = {
            promo: 'all',
            department: 'all',
            nationality: 'all',
            origin: 'all',
            gender: 'all',
        };

        $scope.next = function() {
            Paginate.next($scope.users).then(function(response){
                $scope.users = response;
            });
        };

        $scope.reload = function(criterias) {
            var url = 'users?sort=-promo,firstName,lastName';

            if (criterias.promo != 'all')
                url += '&promo=' + criterias.promo;
            if (criterias.department != 'all')
                url += '&department=' + criterias.department;
            if (criterias.nationality != 'all')
                url += '&nationality=' + criterias.nationality;
            if (criterias.origin != 'all')
                url += '&origin=' + criterias.origin;
            if (criterias.gender != 'all')
                url += '&promo=' + criterias.gender;

            Paginate.get(url, 20).then(function(response){
                $scope.users = response;
                $scope.next();
            });
        };
    }
}

export default Students_List_Ctrl;
