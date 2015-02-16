angular.module('upont')
    .controller('Profil_Ctrl', ['$scope', '$resource', 'profil', 'clubs', 'clubsSuivis', function($scope, $resource, profil, clubs, clubsSuivis) {
        for(var i=0; i<clubsSuivis.length; i++)
            clubsSuivis[i] = clubsSuivis[i].slug;

        for(var j=0; j<clubs.length; j++)
            clubs[j].suivi = (clubsSuivis.indexOf(clubs[j].slug) >= 0);

        $scope.profil = profil;
        $scope.clubs = clubs;

        $scope.subscribe = function (slug)
        {
            return $resource(apiPrefix+"clubs/"+slug+"/follow").save();
        };

        $scope.unsubscribe = function(slug)
        {
            return $resource(apiPrefix+"clubs/"+slug+"/unfollow").save();
        };
    }])
    .config(['$stateProvider', function ($stateProvider){
        $stateProvider
            .state("profil", {
                url : '/profil',
                templateUrl : "views/profil.html",
                controller : "Profil_Ctrl",
                resolve : {
                    profil : ["$resource", function($resource){
                        return $resource(apiPrefix+"own/preferences").get().$promise;
                    }],
                    clubs : ["$resource", function($resource){
                        return $resource(apiPrefix+"clubs?sort=name").query().$promise;
                    }],
                    clubsSuivis : ["$resource", function($resource){
                        return $resource(apiPrefix+"own/followed").query().$promise;
                    }]
                }
            });
    }]);