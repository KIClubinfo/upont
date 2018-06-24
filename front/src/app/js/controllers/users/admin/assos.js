import alertify from 'alertifyjs';
import { API_PREFIX } from 'upont/js/config/constants';

/* @ngInject */
class Admin_Assos_Ctrl {
    constructor($scope, $rootScope, $http) {
        $scope.club = {
            fullname: '',
            name: '',
            category: ''
        };

        $scope.post = function(club) {
            const params = {
                fullName: club.fullname,
                name: club.name,
                category: club.category,
                active: true,
            };

            if (!club.fullname) {
                alertify.error('Le nom complet n\'a pas été renseigné');
                return;
            }

            if (!club.name) {
                alertify.error('Le nom court n\'a pas été renseigné');
                return;
            }

            $http.post(API_PREFIX + 'clubs', params).then(function(){
                alertify.success('Assos créée');
            });

            $scope.club = {
                fullname: '',
                name: '',
                category: ''
            };
        };

    }
}

export default Admin_Assos_Ctrl;
