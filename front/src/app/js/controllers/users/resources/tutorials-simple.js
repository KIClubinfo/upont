import alertify from 'alertifyjs';

import { API_PREFIX, FA_ICONS } from 'upont/js/config/constants';

class Resource_Tutorials_Simple_Ctrl {
    constructor($scope, tuto, $http, $sce, $state) {
        $scope.tuto = tuto;
        $scope.edit = false;
        var tutoSlug = tuto.name;
        $scope.showIcons = false;
        $scope.faIcons = FA_ICONS;

        $scope.editTuto = function() {
            $scope.edit = true;
        };

        $scope.switchIcons = function() {
            $scope.showIcons = !$scope.showIcons;
        };

        $scope.setIcon = function(icon) {
            $scope.tuto.icon = icon;
            window.scrollTo(0, 0);
        };

        $scope.modify = function() {
            var params = {
                name: $scope.tuto.name,
                icon: $scope.tuto.icon,
                text: $scope.tuto.text,
            };

            $http.patch(API_PREFIX + 'tutos/' + $scope.tuto.slug, params).then(function(){
                if (tutoSlug != tuto.name) {
                    alertify.alert('Le nom du tuto ayant changé, il est nécessaire de recharger la page du tuto...');
                    $state.go('root.users.resources.tutorials.list');
                }
                $scope.showIcons = false;
                alertify.success('Modifications prises en compte !');
            });
            $scope.edit = false;
        };

        $scope.delete = function() {
            alertify.confirm('Veux-tu vraiment supprimer ce tuto ?', function(e){
                if (e) {
                    $http.delete(API_PREFIX + 'tutos/' + $scope.tuto.slug).then(function(){
                        alertify.success('Tuto supprimé !');
                        $state.go('root.users.resources.tutorials.list');
                    });
                }
            });
        };
    }
}

export default Resource_Tutorials_Simple_Ctrl;
