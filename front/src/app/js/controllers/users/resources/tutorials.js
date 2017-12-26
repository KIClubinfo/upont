angular.module('upont')
    .controller('Tutorials_Ctrl', ['$scope', 'tutos', '$http', '$state', function($scope, tutos, $http, $state) {
        $scope.tutos = tutos;

        $scope.post = function(name) {
            if (name === undefined || name === '') {
                alertify.error('Nom vide');
                return;
            }

            $http.post(API_PREFIX + 'tutos', {name: name}).then(function(response){
                alertify.alert('Tuto créé ! Redirection...');
                $state.go('root.users.resources.tutorials.simple', {slug: response.data.slug});
            });
        };
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.resources.tutorials', {
                url: '/tutoriels',
                template: '<div ui-view></div>',
                abstract: true,
                data: {
                    title: 'Tutoriels - uPont',
                    top: true
                },
            })
            .state('root.users.resources.tutorials.list', {
                url: '',
                templateUrl: 'controllers/users/resources/tutorials.html',
                controller: 'Tutorials_Ctrl',
                data: {
                    title: 'Tutoriels - uPont',
                    top: true
                },
                resolve: {
                    tutos: ['$resource', function($resource) {
                        return $resource(API_PREFIX + 'tutos').query().$promise;
                    }]
                },
            })
        ;
    }]);
