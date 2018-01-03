import alertify from 'alertifyjs';

import { API_PREFIX } from 'upont/js/config/constants';

/* @ngInject */
class Resources_Tutorials_List_Ctrl {
    constructor($scope, tutos, $http, $state) {
        $scope.tutos = tutos;

        $scope.post = function(name) {
            if (name === undefined || name === '') {
                alertify.error('Nom vide');
                return;
            }

            $http.post(API_PREFIX + 'tutos', {name: name}).then(function(response){
                alertify.alert('Tuto créé ! Redirection...');
                $state.go('root.users.resources.tutorials.simple', {slug: response.data.slug});
            });
        };
    }
}

export default Resources_Tutorials_List_Ctrl;
