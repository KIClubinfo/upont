angular.module('upont')
    .controller('Admin_Students_Ctrl', ['$scope', '$rootScope', '$http', function($scope, $rootScope, $http) {
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
            }

            $http.post(apiPrefix + 'users', {email: email, lastName: lastName, firstName: firstName})
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

        $scope.import = function(name) {
            if ($scope.fd === null) {
                alertify.error('Le fichier n\'a pas été choisi !');
            }

            $http.post(apiPrefix + 'import/users', $scope.fd, {
                withCredentials: true,
                headers: {'Content-Type': undefined },
                transformRequest: angular.identity
            }).then(function() {
                var input = $('#fileUpload');
                input.replaceWith(input.val('').clone(true));
                alertify.success('Import effectué, un rapport a été envoyé à ovh@clubinfo.enpc.fr');
            });
        };
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.admin', {
                url: 'admin',
                templateUrl: 'controllers/users/admin/index.html',
                abstract: true,
                data: {
                    title: 'Administration - uPont',
                    top: true
                }
            })
            .state('root.users.admin.students', {
                url: '/eleves',
                templateUrl: 'controllers/users/admin/students.html',
                controller: 'Admin_Students_Ctrl',
                data: {
                    title: 'Administration des élèves - uPont',
                    top: true
                }
            });
    }]);
