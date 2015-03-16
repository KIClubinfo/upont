angular.module('upont')
    .controller('Profil_Ctrl', ['$scope', '$rootScope', '$resource', '$http', 'preferences', 'clubs', 'clubsSuivis', 'token', 'devices', function($scope, $rootScope, $resource, $http, preferences, clubs, clubsSuivis, token, devices) {
        console.log(clubsSuivis);
        for (var i = 0; i < clubsSuivis.length; i++)
            clubsSuivis[i] = clubsSuivis[i].slug;

        for (var j = 0; j < clubs.length; j++)
            clubs[j].suivi = (clubsSuivis.indexOf(clubs[j].slug) >= 0);

        $scope.preferences = preferences;
        $scope.clubs = clubs;
        $scope.user = $rootScope.me;
        $scope.profilePicture = null;
        $scope.token = token.token;
        $scope.devices = devices;

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

        $scope.submitUser = function(promo, nationality, phone, location, department, origin, skype, nickname, image) {
            var params = {
                'promo' : promo,
                'nationality' : nationality,
                'phone' : phone,
                'location' : location,
                'department' : department,
                'origin' : origin,
                'skype' : skype,
                'nickname' : nickname
            };

            if (image) {
                params.image = image.base64;
            }

            $http.patch($rootScope.url + 'users/' + $rootScope.me.username, params).success(function(){
                // On recharge 'user pour être sûr d'avoir la nouvelle photo
                $http.get(apiPrefix + 'users/' + $rootScope.me.username).success(function(data){
                    $rootScope.me = data;
                });
            });
        };
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state("root.profil", {
                url: 'profil',
                templateUrl: "views/profile.html",
                controller: "Profil_Ctrl",
                resolve: {
                    preferences: ["$resource", function($resource) {
                        return $resource(apiPrefix + "own/preferences").get().$promise;
                    }],
                    token: ['$resource', function($resource) {
                        return $resource(apiPrefix + 'own/token').get().$promise;
                    }],
                    devices: ['$resource', function($resource) {
                        return $resource(apiPrefix + 'own/devices').query().$promise;
                    }],
                    clubs: ["$resource", function($resource) {
                        return $resource(apiPrefix + "clubs?sort=name").query().$promise;
                    }],
                    clubsSuivis: ["$resource", function($resource) {
                        return $resource(apiPrefix + "own/followed").query().$promise;
                    }]
                },
                data: {
                    title: 'uPont - Profil'
                }
            });
    }]);
