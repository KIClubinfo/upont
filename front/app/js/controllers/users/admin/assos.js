angular.module('upont')
    .controller('Admin_Assos_Ctrl', ['$scope', '$rootScope', '$http', function($scope, $rootScope, $http) {
        $scope.club = {
            fullname: '',
            name: '',
            administration: false,
            isClub: true
        };

        $scope.post = function(club) {
            var params ={
                fullName: club.fullname,
                name: club.name,
                administration: club.administration,
                category: club.category,
                active: true
            };

            if (!club.fullname) {
                alertify.error('Le nom complet n\'a pas été renseigné');
                return;
            }

            if (!club.name) {
                alertify.error('Le nom court n\'a pas été renseigné');
                return;
            }

            $http.post($rootScope.url + 'clubs', params).then(function(){
                alertify.success('Assos créée');
            });

            $scope.club = {
                fullname: '',
                name: '',
                administration: false,
                category: true
            };
        };

    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.admin.assos', {
                url: '/assos',
                templateUrl: 'controllers/users/admin/assos.html',
                controller: 'Admin_Assos_Ctrl',
                data: {
                    title: 'Administration des assos - uPont',
                    top: true
                }
            });
    }]);
