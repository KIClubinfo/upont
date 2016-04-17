angular.module('upont')
    .controller('Assos_Modify_Ctrl', ['$scope', '$controller', '$http', '$state', function($scope, $controller, $http, $state) {
        $scope.showIcons = false;
        $scope.faIcons = faIcons;
        $scope.search = '';
        $scope.searchResults = [];
        var clubSlug = $scope.club.name;

        $scope.reloadMembers = function() {
            $http.get(apiPrefix + 'clubs/' + $scope.club.slug + '/users').success(function(data){
                $scope.members = data;
            });
        };

        $scope.submitClub = function(name, fullName, icon, image, banner) {
            var params = {
                'name' : name,
                'fullName' : fullName,
                'icon' : icon,
            };

            if (image) {
                params.image = image.base64;
            }

            if (banner) {
                params.banner = banner.base64;
            }

            $http.patch(apiPrefix + 'clubs/' + $scope.club.slug, params).success(function(){
                // On recharge le club pour être sûr d'avoir la nouvelle photo
                if (clubSlug == name) {
                    $http.get(apiPrefix + 'clubs/' + $scope.club.slug).success(function(data){
                        $scope.club = data;
                    });
                } else {
                    alertify.alert('Le nom court du club ayant changé, il est nécessaire de recharger la page du club...');
                    $state.go('root.users.assos.list');
                }
                alertify.success('Modifications prises en compte !');
            });
        };

        $scope.setIcon = function(icon) {
            $scope.club.icon = icon;
            window.scrollTo(0, 0);
        };

        $scope.searchUser = function(string) {
            if (string === '') {
                $scope.searchResults = [];
            } else {
                $http.post(apiPrefix + 'search', {search: 'User/' + string}).success(function(data){
                    $scope.searchResults = data.users;
                });
            }
        };

        $scope.addMember = function(user) {
            // On vérifie que la personne n'est pas déjà membre
            for (var i = 0; i < $scope.members.length; i++) {
                if ($scope.members[i].user.username == user.slug) {
                    alertify.error('Déjà membre du club !');
                    return;
                }
            }

            alertify.prompt('Rôle de ' + user.name + ' :', function(e, role){
                if (e) {
                    $http.post(apiPrefix + 'clubs/' + $scope.club.slug + '/users/' + user.slug, {role: role}).success(function(data){
                        alertify.success(user.name + ' a été ajouté(e) !');
                        $scope.reloadMembers();
                    });
                }
            });
        };

        $scope.editMember = function(user) {
            // On vérifie que la personne est déjà membre
            var found = false;
            for (var i = 0; i < $scope.members.length; i++) {
                if ($scope.members[i].user.username == user.username) {
                    found = true;
                    break;
                }
            }
            if(!found) {
                alertify.error('Pas membre du club !');
                return;
            }

            alertify.prompt('Nouveau rôle de ' + user.first_name + ' ' + user.last_name + ' :', function(e, role){
                if (e) {
                    $http.patch(apiPrefix + 'clubs/' + $scope.club.slug + '/users/' + user.username, {role: role}).success(function(data){
                        alertify.success(user.first_name + ' ' + user.last_name + ' a été modifié(e) !');
                        $scope.reloadMembers();
                    });
                }
            });
        };

        $scope.removeMember = function(user) {
            $http.delete(apiPrefix + 'clubs/' + $scope.club.slug + '/users/' + user.username).success(function(data){
                alertify.success('Membre supprimé !');
                $scope.reloadMembers();
            });
        };
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.assos.simple.modify', {
                url: '/gestion',
                controller: 'Assos_Modify_Ctrl',
                templateUrl: 'controllers/users/assos/modify.html',
                data: {
                    title: 'Gestion - uPont',
                    top: true
                },
            });
    }]);
