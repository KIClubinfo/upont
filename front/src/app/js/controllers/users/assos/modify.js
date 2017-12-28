import alertify from 'alertifyjs';

import constants, { API_PREFIX } from 'upont/js/config/constants';

class Assos_Modify_Ctrl {
    constructor($scope, $controller, $http, $state) {
        $scope.PROMOS = constants.PROMOS;

        $scope.showIcons = false;
        $scope.isLoading = false;
        $scope.faIcons = constants.FA_ICONS;
        $scope.search = '';
        $scope.searchResults = [];
        var clubSlug = $scope.club.name;

        $scope.reloadMembers = function() {
            $http.get(API_PREFIX + 'clubs/' + $scope.club.slug + '/users').then(function(response) {
                $scope.members = response.data;
            });
        };

        $scope.submitClub = function(name, fullName, icon, category, image, banner, active, place) {
            var params = {
                'name': name,
                'fullName': fullName,
                'icon': icon,
                'category': category,
                'active': active,
                'place': place
            };

            if (image) {
                params.image = image.base64;
            }

            if (banner) {
                params.banner = banner.base64;
            }

            $http.patch(API_PREFIX + 'clubs/' + $scope.club.slug, params).then(function() {
                // On recharge le club pour être sûr d'avoir la nouvelle photo
                if (clubSlug == name) {
                    $http.get(API_PREFIX + 'clubs/' + $scope.club.slug).then(function(response) {
                        $scope.club = response.data;
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
                $http.post(API_PREFIX + 'search', {search: 'User/' + string}).then(function(response) {
                    $scope.searchResults = response.data.users;
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

            alertify.prompt('Rôle de ' + user.name + ' :', function(e, role) {
                if (e) {
                    $http.post(API_PREFIX + 'clubs/' + $scope.club.slug + '/users/' + user.slug, {role: role}).then(function() {
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
            if (!found) {
                alertify.error('Pas membre du club !');
                return;
            }

            alertify.prompt('Nouveau rôle de ' + user.first_name + ' ' + user.last_name + ' :', function(e, role) {
                if (e) {
                    $http.patch(API_PREFIX + 'clubs/' + $scope.club.slug + '/users/' + user.username, {role: role}).then(function() {
                        alertify.success(user.first_name + ' ' + user.last_name + ' a été modifié(e) !');
                        $scope.reloadMembers();
                    });
                }
            });
        };

        $scope.moveMember = function(user, direction) {
            // On vérifie qu'une requête n'est pas déjà en cours
            if ($scope.isLoading === false) {
                $scope.isLoading = true;

                // On vérifie que la personne est déjà membre
                var found = false;
                for (var i = 0; i < $scope.members.length; i++) {
                    if ($scope.members[i].user.username == user.username) {
                        found = true;
                        break;
                    }
                }
                if (!found) {
                    alertify.error('Pas membre du club !');
                    return;
                }

                $http.patch(API_PREFIX + 'clubs/' + $scope.club.slug + '/users/' + user.username + '/' + direction).then(
                    function() {
                        $scope.isLoading = false;
                        $scope.reloadMembers();
                    },
                    function() {
                        $scope.isLoading = false;
                    }
                );
            }
        };

        $scope.removeMember = function(user) {
            $http.delete(API_PREFIX + 'clubs/' + $scope.club.slug + '/users/' + user.username).then(function() {
                alertify.success('Membre supprimé !');
                $scope.reloadMembers();
            });
        };
    }
}

export default Assos_Modify_Ctrl;
