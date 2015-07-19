angular.module('upont')
    .controller('Students_Modify_Ctrl', ['$scope', '$rootScope', '$resource', '$http', 'preferences', 'clubs', 'clubsSuivis', 'token', 'devices', 'Achievements', function($scope, $rootScope, $resource, $http, preferences, clubs, clubsSuivis, token, devices, Achievements) {
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
            $resource(apiPrefix + 'clubs/:slug/follow', {slug: slug}).save();
            $scope.clubs.forEach(function (element, index, array){
                if(element.slug == slug)
                    element.suivi = true;
            });
        };

        $scope.unsubscribe = function(slug) {
            $resource(apiPrefix + 'clubs/:slug/unfollow', {slug: slug}).save();
            $scope.clubs.forEach(function (element, index, array){
                if(element.slug == slug)
                    element.suivi = false;
            });
        };

        $scope.submitUser = function(me, image) {
            var params = {
                promo: me.promo,
                gender: me.gender,
                nationality: me.nationality,
                phone: me.phone,
                location: me.location,
                department: me.department,
                origin: me.origin,
                skype: me.skype,
                nickname: me.nick,
                statsFoyer: me.stats_foyer,
                statsPonthub: me.stats_ponthub
            };

            if (image) {
                params.image = image.base64;
            }

            $http.patch($rootScope.url + 'users/' + $rootScope.me.username, params).success(function(){
                $resource(apiPrefix + 'users/:slug', {slug: $rootScope.me.username}).get(function(data){
                    $rootScope.me = data;
                    Achievements.check();
                });
                alertify.success('Profil mis à jour !');
            });
        };

        $scope.submitAccount = function(me, old, password, confirm) {
            if (password === undefined || confirm === undefined || old === undefined) {
                alertify.error('Champs non remplis');
                return;
            }

            if (password != confirm) {
                alertify.error('Les deux mots de passe ne sont pas identiques');
                return;
            }

            var params = {
                // email: me.email,
                old: old,
                password: password,
                confirm: confirm
            };

            $http.post($rootScope.url + 'own/user', params)
                .success(function(){
                    $resource(apiPrefix + 'users/:slug', {slug: $rootScope.me.username}).get(function(data){
                        $rootScope.me = data;
                        Achievements.check();
                    });
                    alertify.success('Compte mis à jour !');
                })
                .error(function(){
                    alertify.error('Ancien mot de passe incorrect');
                })
            ;
        };

        // Gère l'accordéon du tuto ICS
        $scope.openICS = -1;
        $scope.open = function(index) {
            $scope.openICS = $scope.openICS != index ? index : -1;
        };

        $scope.achievementCheck = function() {
            Achievements.check();
        };

        $scope.enableTour = function(e) {
            e.preventDefault();
            if (!$rootScope.me.tour)
                return;
            $http.patch($rootScope.url + 'users/' + $rootScope.me.username, {tour: false}).success(function(){
                $rootScope.me.tour = false;
                $rootScope.$broadcast('tourEnabled');
                alertify.success('Tutoriel réactivé !');
            });
        };
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.students.modify', {
                url: '/profil',
                templateUrl: 'views/users/students/modify.html',
                controller: 'Students_Modify_Ctrl',
                resolve: {
                    preferences: ['$resource', function($resource) {
                        return $resource(apiPrefix + 'own/preferences').get().$promise;
                    }],
                    token: ['$resource', function($resource) {
                        return $resource(apiPrefix + 'own/token').get().$promise;
                    }],
                    devices: ['$resource', function($resource) {
                        return $resource(apiPrefix + 'own/devices').query().$promise;
                    }],
                    clubs: ['$resource', function($resource) {
                        return $resource(apiPrefix + 'clubs?sort=name').query().$promise;
                    }],
                    clubsSuivis: ['$resource', function($resource) {
                        return $resource(apiPrefix + 'own/followed').query().$promise;
                    }]
                },
                data: {
                    title: 'Profil - uPont',
                    top: true
                }
            });
    }]);
