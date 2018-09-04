import alertify from 'alertifyjs';
import angular from 'angular';
import $ from 'jquery';

import { API_PREFIX } from 'upont/js/config/constants';

/* @ngInject */
class Admin_Students_Ctrl {
    constructor($scope, $rootScope, $http) {
        $scope.firstName = '';
        $scope.lastName = '';
        $scope.email = '';

        $scope.post = function(firstName, lastName, email) {
            if (firstName === undefined || lastName === undefined || email === undefined) {
                alertify.error('Au moins un des champs n\'est pas rempli');
                return;
            }

            var regex = /@(eleves\.)?enpc\.fr$/;
            if (!regex.test(email)) {
                alertify.error('Désolé, seules les adresses des Ponts sont acceptées !');
                return;
            }

            $http.post(API_PREFIX + 'users', {email: email, lastName: lastName, firstName: firstName})
                .then(
                    function(){
                        alertify.success('Mail envoyé !');
                        $scope.firstName = '';
                        $scope.lastName = '';
                        $scope.email = '';
                    },
                    function(){
                        alertify.error('Un utilisateur avec cette adresse existe déjà');
                    }
                )
            ;
        };

        $scope.fd = null;
        $scope.uploadFile = function(files) {
            $scope.fd = new FormData();
            $scope.fd.append('users', files[0]);
        };

        $scope.import = function() {
            if ($scope.fd === null) {
                alertify.error('Le fichier n\'a pas été choisi !');
            }

            $http.post(API_PREFIX + 'import/users', $scope.fd, {
                withCredentials: true,
                headers: {'Content-Type': undefined },
                transformRequest: angular.identity
            }).then(function() {
                var input = $('#fileUpload');
                input.replaceWith(input.val('').clone(true));
                alertify.success('Import effectué, un rapport a été envoyé à upont@clubinfo.enpc.fr');
            });
        };
    }
}

export default Admin_Students_Ctrl;
