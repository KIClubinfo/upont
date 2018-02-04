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
            const paginationParams = {
                sort:'-promo,firstName,lastName',
                limit: 20,
            };

            if (criterias.promo !== 'all')
                paginationParams['promo'] = criterias.promo;
            if (criterias.department !== 'all')
                paginationParams['department'] = criterias.department;
            if (criterias.nationality !== 'all')
                paginationParams['nationality'] = criterias.nationality;
            if (criterias.origin !== 'all')
                paginationParams['origin'] = criterias.origin;
            if (criterias.gender !== 'all')
                paginationParams['gender'] = criterias.gender;

            Paginate.get('users', paginationParams).then(function(response){
                $scope.users = response;
                $scope.next();
            });
        };
    }
}

export default Students_List_Ctrl;
