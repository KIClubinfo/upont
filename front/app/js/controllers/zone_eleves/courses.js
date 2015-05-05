angular.module('upont')
    .controller('Cours_Ctrl', ['$scope', 'exos', function($scope, exos) {
        $scope.exos = exos;
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state("root.zone_eleves.cours", {
                url: "cours",
                abstract: true,
                templateUrl: "views/zone_eleves/cours/index.html",
                data: {
                    title: "Cours - uPont"
                },
            })
            .state("root.zone_eleves.cours.section", {
                url: "/:section",
                params: {
                    section: '1a'
                },
                templateUrl: "views/zone_eleves/cours/cours.html",
                controller: 'Cours_Ctrl',
                resolve: {
                    exos: ["$resource", "$stateParams", function($resource, $stateParams) {
                        return true;
                        // return $resource(apiPrefix + "exercices/" + $stateParams.section).query().$promise;
                    }]
                }
            });
    }]);
