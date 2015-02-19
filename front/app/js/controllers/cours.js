angular.module('upont')
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state("cours", {
                url: "/cours",
                templateUrl: "views/cours/index.html",
                data: {
                    defaultChild: "section",
                    parent: "cours"
                },
            })
            .state("cours.section", {
                url: "/:section",
                params: {
                    section: '1a'
                },
                templateUrl: "views/cours/cours.html",
                controller: ['$scope', "exercices", function($scope, exercices) {
                    // var matieres = {};
                    // for(var i=0; i<exercices.length; i++)
                    // {
                    //     if(!matieres[exercices[i].matiere])
                    //         matiere[exercices[i].matiere] = [];
                    //     matiere[exercices[i].matiere].push(exercices[i]);
                    // }
                    // $scope.matieres = matieres;

                    $scope.exercices = exercices;
                }],
                resolve: {
                    exercices: ["$resource", "$stateParams", function($resource, $stateParams) {
                        return $resource(apiPrefix + "exercices?filterBy=department&filterValue=" + $stateParams.section).query().$promise;
                    }]
                }
            });
    }]);
