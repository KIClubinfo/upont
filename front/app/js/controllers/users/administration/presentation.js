angular.module('upont')
    .controller('Administration_Presentation_Ctrl', ['$scope', '$http', '$sce', '$filter', 'club', function($scope, $http, $sce, $filter, club) {
        $scope.presentation = club.presentation;
        $scope.edit = false;

        $scope.editPresentation = function() {
            $scope.edit = true;
        };

        $scope.modify = function(presentation) {
            $http.patch(apiPrefix + 'clubs/' + club.slug, {presentation: presentation}).success(function() {
                $scope.presentation = $sce.trustAsHtml(presentation);
                alertify.success('Modifications prises en compte !');
            });
            $scope.edit = false;
        };
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.administration', {
                url: 'administration/:slug',
                abstract: true,
                templateUrl: 'views/users/administration/index.html',
                data: {
                    title: 'Administration - uPont'
                },
                resolve: {
                    club: ['$resource', '$stateParams', function($resource, $stateParams) {
                        return $resource(apiPrefix + 'clubs/:slug').get({
                            slug: $stateParams.slug
                        }).$promise;
                    }]
                }
            })
            .state('root.users.administration.presentation', {
                url: '/presentation',
                controller : 'Administration_Presentation_Ctrl',
                templateUrl: 'views/users/administration/presentation.html',
                data: {
                    title: 'Présentation - uPont',
                    top: true
                }
            });
    }]);
