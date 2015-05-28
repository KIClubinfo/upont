angular.module('upont')
    .controller('Admin_Assos_Ctrl', ['$scope', '$rootScope', '$http', function($scope, $rootScope, $http) {
        $scope.club = {
            fullname: '',
            name: '',
            administration: 'Non',
            assos: 'Club'
        };

        $scope.post = function(club) {
            var params ={
                fullName: club.fullname,
                name: club.name,
                active: 1
            };

            if (!club.fullname) {
                alertify.error('Le nom complet n\'a pas été renseigé');
                return;
            }

            if (!club.name) {
                alertify.error('Le nom court n\'a pas été renseigé');
                return;
            }

            if (club.administration == 'Oui') {
                params.administration = true;
            } else {
                params.administration = false;
            }

            if (club.assos == 'Assos') {
                params.assos = true;
            } else {
                params.assos = false;
            }

            $http.post($rootScope.url + 'clubs', params).success(function(){
                alertify.success('Assos créée');
            });
        };

    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.admin.assos', {
                url: '/assos',
                templateUrl: 'views/users/admin/assos.html',
                controller: 'Admin_Assos_Ctrl',
                data: {
                    title: 'Administration des assos - uPont',
                    top: true
                }
            });
    }]);
