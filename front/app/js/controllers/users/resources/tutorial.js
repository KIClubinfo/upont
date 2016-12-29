angular.module('upont')
    .controller('Tutorial_Ctrl', ['$scope', 'tuto', '$http', '$sce', '$state', function($scope, tuto, $http, $sce, $state) {
        $scope.tuto = tuto;
        $scope.presentation = $sce.trustAsHtml($scope.tuto.text);
        $scope.edit = false;
        var tutoSlug = tuto.name;
        $scope.showIcons = false;
        $scope.faIcons = faIcons;

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

        $scope.modify = function(tuto, presentation) {
            var params = {
                name: tuto.name,
                icon: tuto.icon
            };

            if (typeof presentation == 'string')
                params.text = presentation;

            $http.patch(apiPrefix + 'tutos/' + $scope.tuto.slug, params).then(function(){
                if (tutoSlug != tuto.name) {
                    alertify.alert('Le nom du tuto ayant changé, il est nécessaire de recharger la page du tuto...');
                    $state.go('root.users.resources.tutorials.list');
                }
                $scope.presentation = $sce.trustAsHtml(presentation);
                $scope.showIcons = false;
                alertify.success('Modifications prises en compte !');
            });
            $scope.edit = false;
        };

        $scope.delete = function() {
            alertify.confirm('Veux-tu vraiment supprimer ce tuto ?', function(e){
                if (e) {
                    $http.delete(apiPrefix + 'tutos/' + $scope.tuto.slug).then(function(){
                        alertify.success('Tuto supprimé !');
                        $state.go('root.users.resources.tutorials.list');
                    });
                }
            });
        };
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.resources.tutorials.simple', {
                url: '/:slug',
                templateUrl: 'controllers/users/resources/tutorial.html',
                controller: 'Tutorial_Ctrl',
                data: {
                    title: 'Tutoriels - uPont',
                    top: true
                },
                resolve: {
                    tuto: ['$resource', '$stateParams', function($resource, $stateParams) {
                        return $resource(apiPrefix + 'tutos/:slug').get({
                            slug: $stateParams.slug
                        }).$promise;
                    }],
                },
            })
        ;
    }]);
