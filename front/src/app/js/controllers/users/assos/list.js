/* @ngInject */
class Assos_List_Ctrl {
    constructor($rootScope, $scope, clubs) {
        $scope.clubs = clubs;
        $scope.clubTypes = [
            {name: 'Assos', slug: 'asso'},
            {name: 'Clubs gastronomiques', slug: 'club-gastronomique'},
            {name: 'Clubs artistiques', slug: 'club-artistique'},
            {name: 'Clubs de divertissement', slug: 'club-divertissement'},
            {name: 'Clubs culturels', slug: 'club-culturel'}
        ];
        $scope.clubSlugs = $scope.clubTypes.map(function(dict) { return dict.slug; });
    }
}

export default Assos_List_Ctrl;
