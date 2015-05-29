angular.module('upont')
    .controller('Admin_Students_Ctrl', ['$scope', '$rootScope', '$http', function($scope, $rootScope, $http) {
        $scope.user = {
            first_name: '',
            last_name: '',
            username: '',
            email: ''
        };

        $scope.post = function(user, password, confirm) {
            var params = {
                firstName: user.first_name,
                lastName: user.last_name,
                username: user.username,
                email: user.email
            };

            if (!user.first_name) {
                alertify.error('Le prénom n\'a pas été renseigné');
                return;
            }

            if (!user.last_name) {
                alertify.error('Le nom n\'a pas été renseigné');
                return;
            }

            if (!user.username) {
                alertify.error('L\'identifiant DSI n\'a pas été renseigné');
                return;
            }

            if (!user.email) {
                alertify.error('L\'email n\'a pas été renseigné');
                return;
            }

            if (password !== undefined && password !== '') {
                if (password != confirm) {
                    alertify.error('Les deux mots de passe ne sont pas identiques');
                    return;
                } else {
                    params.plainPassword = {first: password, second: confirm};
                }
            } else {
                alertify.error('Le mot de passe n\'a pas été renseigné');
                return;
            }

            $http.post($rootScope.url + 'users', params).success(function(){
                alertify.success('Utilisateur créé');
            }).error(function(){
                alertify.error('Cet utilisateur existe déjà');
                return;
            });

            $scope.user = {
                first_name: '',
                last_name: '',
                username: '',
                email: ''
            };

            $scope.password = '';
            $scope.confirm = '';
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
