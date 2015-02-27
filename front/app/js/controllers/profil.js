angular.module('upont')
    .controller('Profil_Ctrl', ['$scope', '$resource', 'preferences', 'clubs', 'clubsSuivis', function($scope, $resource, preferences, clubs, clubsSuivis) {
        for (var i = 0; i < clubsSuivis.length; i++)
            clubsSuivis[i] = clubsSuivis[i].slug;

        for (var j = 0; j < clubs.length; j++)
            clubs[j].suivi = (clubsSuivis.indexOf(clubs[j].slug) >= 0);

        $scope.preferences = preferences;
        $scope.clubs = clubs;

        $scope.subscribe = function(slug) {
            $resource(apiPrefix + "clubs/:slug/follow", {slug: slug}).save();
            $scope.clubs.forEach(function (element, index, array){
                if(element.slug == slug)
                    element.suivi = true;
            });
        };

        $scope.unsubscribe = function(slug) {
            $resource(apiPrefix + "clubs/:slug/unfollow", {slug: slug}).save();
            $scope.clubs.forEach(function (element, index, array){
                if(element.slug == slug)
                    element.suivi = false;
            });
        };
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state("profil", {
                url: '/profil',
                templateUrl: "views/profil.html",
                controller: "Profil_Ctrl",
                resolve: {
                    preferences: ["$resource", function($resource) {
                        return $resource(apiPrefix + "own/preferences").get().$promise;
                    }],
                    clubs: ["$resource", function($resource) {
                        return $resource(apiPrefix + "clubs?sort=name").query().$promise;
                    }],
                    clubsSuivis: ["$resource", function($resource) {
                        return $resource(apiPrefix + "own/followed").query().$promise;
                    }]
                }
            });
    }]);
