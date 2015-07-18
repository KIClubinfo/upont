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
                .success(function(){
                    alertify.success('Mail envoyé !');
                    $scope.firstName = '';
                    $scope.lastName = '';
                    $scope.email = '';
                })
                .error(function(){
                    alertify.error('Un utilisateur avec cette adresse existe déjà');
                })
            ;
        };

    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.admin', {
                url: 'admin',
                templateUrl: 'views/users/admin/index.html',
                abstract: true,
                data: {
                    title: 'Administration - uPont',
                    top: true
                }
            })
            .state('root.users.admin.students', {
                url: '/eleves',
                templateUrl: 'views/users/admin/students.html',
                controller: 'Admin_Students_Ctrl',
                data: {
                    title: 'Administration des élèves - uPont',
                    top: true
                }
            });
    }]);
