import alertify from 'alertifyjs';

import constants, {API_PREFIX} from 'upont/js/config/constants';

/* @ngInject */
class Assos_Presentation_Ctrl {
    constructor($scope, $http, $sce) {
        $scope.PROMOS = constants.PROMOS;

        $scope.edit = false;

        $scope.editPresentation = function() {
            $scope.edit = true;
        };

        $scope.modify = function() {
            $http.patch(API_PREFIX + 'clubs/' + $scope.club.slug, {presentation: $scope.club.presentation}).then(function() {
                alertify.success('Modifications prises en compte !');
            });
            $scope.edit = false;
        };
    }
}

export default Assos_Presentation_Ctrl;
