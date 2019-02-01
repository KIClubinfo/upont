import moment from 'moment';

/* @ngInject */
class Ponthub_List_Ctrl {
    constructor($scope, $stateParams, elements, Paginate, Ponthub, StorageService) {
        $scope.elements = elements;
        $scope.category = $stateParams.category;
        $scope.type = Ponthub.cat($stateParams.category);
        $scope.lastWeek = moment().subtract(7, 'days').unix();
        $scope.token = StorageService.get('token');

        $scope.reload = function() {
            Paginate.get(Ponthub.cat($stateParams.category), {
                sort: '-added,id',
                limit: 20
            }).then(data => {
                $scope.elements = data;
            });
        };

        $scope.faIcon = function(category) {
            switch (category) {
            case 'jeux':
                return 'fa-gamepad';
            case 'films':
            case 'series':
                return 'fa-film';
            case 'autres':
                return 'fa-file-o';
            case 'logiciels':
                return 'fa-desktop';
            default:
                return '';
            }
        };
        $scope.icon = $scope.faIcon($stateParams.category);

        $scope.next = function() {
            Paginate.next($scope.elements).then(data => {
                $scope.elements = data;
            });
        };

        $scope.popular = function(count) {
            return Ponthub.isPopular(count, $stateParams.category);
        };
    }
}

export default Ponthub_List_Ctrl;
