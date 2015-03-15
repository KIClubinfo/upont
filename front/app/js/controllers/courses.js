angular.module('upont')
    .controller('Cours_Ctrl', ['$scope', 'exos', function($scope, exos) {
        $scope.exos = exos;
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state("root.cours", {
                url: "cours",
                abstract: true,
                templateUrl: "views/cours/index.html",
                data: {
                    title: "uPont - Cours"
                },
            })
            .state("root.cours.section", {
                url: "/:section",
                params: {
                    section: '1a'
                },
                templateUrl: "views/cours/cours.html",
                controller: 'Cours_Ctrl',
                resolve: {
                    exos: ["$resource", "$stateParams", function($resource, $stateParams) {
                        return true;
                        // return $resource(apiPrefix + "exercices/" + $stateParams.section).query().$promise;
                    }]
                }
            });
    }]);
