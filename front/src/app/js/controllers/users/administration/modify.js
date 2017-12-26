angular.module('upont')
    .controller('Administration_Modify_Ctrl', ['$rootScope', '$scope', '$http', '$state', 'club', function($rootScope, $scope, $http, $state, club) {
        $scope.club = club;
        $scope.showIcons = false;
        $scope.faIcons = faIcons;
        $scope.search = '';
        $scope.searchResults = [];
        $scope.user = $rootScope.me;
        var clubSlug = club.name;

        $scope.setIcon = function(icon) {
            $scope.club.icon = icon;
            window.scrollTo(0, 0);
        };

        $scope.submitGeneral = function(fullName, icon, image, banner) {
            var params = {
                'fullName' : fullName,
                'icon' : icon,
            };

            if (image) {
                params.image = image.base64;
            }

            if (banner) {
                params.banner = banner.base64;
            }

            $http.patch(apiPrefix + 'clubs/' + $scope.club.slug, params).then(function(){
                alertify.success('Modifications prises en compte !');
            });
        };

        $scope.submitParams = function(me, old, password, confirm) {
            if (password === undefined || confirm === undefined || old === undefined) {
                alertify.error('Champs non remplis');
                return;
            }

            if (password != confirm) {
                alertify.error('Les deux mots de passe ne sont pas identiques');
                return;
            }

            var params = {
                old: old,
                password: password,
                confirm: confirm
            };

            $http.post($rootScope.url + 'own/user', params).then(
                function(){
                    alertify.success('Compte mis à jour !');
                },
                function(){
                    alertify.error('Ancien mot de passe incorrect');
                }
            );
        };

    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.administration.modify', {
                url: '/gestion',
                controller : 'Administration_Modify_Ctrl',
                templateUrl: 'controllers/users/administration/modify.html',
                data: {
                    title: 'Gestion - uPont',
                    top: true
                }
            });
    }]);
