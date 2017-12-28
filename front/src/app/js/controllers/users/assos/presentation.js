import alertify from 'alertifyjs';

import constants, { API_PREFIX } from 'upont/js/config/constants';

class Assos_Presentation_Ctrl {
    constructor($scope, $http, $sce) {
        $scope.PROMOS = constants.PROMOS;

        $scope.presentation = $sce.trustAsHtml($scope.club.presentation);
        $scope.edit = false;

        $scope.editPresentation = function() {
            $scope.edit = true;
        };

        $scope.modify = function(presentation) {
            $http.patch(API_PREFIX + 'clubs/' + $scope.club.slug, {presentation: presentation}).then(function(){
                $scope.presentation = $sce.trustAsHtml(presentation);
                alertify.success('Modifications prises en compte !');
            });
            $scope.edit = false;
        };
    }
}

export default Assos_Presentation_Ctrl;
