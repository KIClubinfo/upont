angular.module('upont')
    .controller('Jeu_Ctrl', ['$scope', '$resource', function($scope, $resource) {
        // $scope.score = 0;


        // function newSet() {
        //     trueName = Math.floor(Math.random() * 3) + 1;
        //     $resource("/v2-api/promo_game").get(function(newEleves) {
        //         $scope.elevePhoto = newEleves.user1;
        //         switch (trueName) {
        //             case 1:
        //                 $scope.nom1 = newEleves.user1;
        //                 $scope.nom2 = newEleves.user2;
        //                 $scope.nom3 = newEleves.user3;
        //                 break;
        //             case 2:
        //                 $scope.nom2 = newEleves.user1;
        //                 $scope.nom1 = newEleves.user2;
        //                 $scope.nom3 = newEleves.user3;
        //                 break;
        //             case 3:
        //                 $scope.nom3 = newEleves.user1;
        //                 $scope.nom2 = newEleves.user2;
        //                 $scope.nom1 = newEleves.user3;
        //                 break;
        //         }
        //     });
        // }

        // $scope.actionJeu = function(nb) {
        //     if (trueName == nb)
        //         $scope.score++;
        //     newSet();
        // };
    }])
    .controller('Trombi_Ctrl', ['$scope', 'eleves', '$filter', function($scope, eleves, $filter){
        $scope.eleves = eleves;
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state("root.zone_eleves.promo", {
                url: "promo",
                abstract: true,
                templateUrl: "views/zone_eleves/promo/index.html"
            })
            .state("root.zone_eleves.promo.trombi", {
                url: "/trombi",
                templateUrl: "views/zone_eleves/promo/trombi.html",
                controller: 'Trombi_Ctrl',
                resolve: {
                    eleves: ["$resource", function($resource) {
                        return $resource(apiPrefix + "users").query().$promise;
                    }]
                }
            })
            .state("root.zone_eleves.promo.jeu", {
                url: "/jeu",
                templateUrl: "views/zone_eleves/promo/jeu.html",
                controller: 'Jeu_Ctrl'
            });
    }]);
