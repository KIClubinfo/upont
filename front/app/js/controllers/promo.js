angular.module('upont')
    .controller('Jeu_Ctrl', ['$scope', '$resource', function($scope, $resource) {
        $scope.score = 0;


        function newSet(){
            trueName = Math.floor(Math.random()*3)+1;
            $resource("/v2-api/promo_game").get(function(newEleves){
                $scope.elevePhoto = newEleves.user1;
                console.log(newEleves);
                switch(trueName)
                {
                case 1:
                    $scope.nom1 = newEleves.user1;
                    $scope.nom2 = newEleves.user2;
                    $scope.nom3 = newEleves.user3;
                    break;
                case 2:
                    $scope.nom2 = newEleves.user1;
                    $scope.nom1 = newEleves.user2;
                    $scope.nom3 = newEleves.user3;
                    break;
                case 3:
                    $scope.nom3 = newEleves.user1;
                    $scope.nom2 = newEleves.user2;
                    $scope.nom1 = newEleves.user3;
                    break;
                }
            });
        }

        $scope.actionJeu = function(nb)
        {
            if(trueName == nb)
                $scope.score++;
            newSet();
        };
    }])
     .config(['$stateProvider', function ($stateProvider){
        $stateProvider
            .state("promo", {
                url : "/promo",
                templateUrl : "views/promo/index.html",
                data : { defaultChild : "trombi", parent : "promo" }
            })
            .state("promo.trombi", {
                url : "/trombi",
                templateUrl : "views/promo/trombi.html",
                controller : ['$scope', 'eleves', function($scope, eleves) {
                    $scope.eleves = eleves;
                }],
                resolve : {
                    eleves : ["$resource", function($resource){
                        return $resource(apiPrefix+"users").query().$promise;
                    }]
                }
            })
            .state("promo.jeu", {
                url : "/jeu",
                templateUrl : "views/promo/jeu.html",
                controller : 'Jeu_Ctrl'
            });
    }]);