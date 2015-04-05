module
    .controller('SettingsController', ['$scope', '$rootScope', 'StorageService', '$http', 'PushNotifications', function($scope, $rootScope, StorageService, $http, PushNotifications) {
        $scope.logout = function(){
            ons.notification.confirm({
                title: 'Déconnexion',
                message: 'Es-tu sûr(e) ?',
                buttonLabels: ['Oui', 'Je veux rester !'],
                animation: 'default',
                primaryButtonIndex: 1,
                callback: function(index) {
                    if(index === 0) {
                        StorageService.remove('token');
                        StorageService.remove('token_exp');
                        onsAlert('Déconnexion', 'Tu as été correctement déconnecté');
                        menu.setSwipeable(false);
                        menu.setMainPage('views/login.html', {closeMenu: true});
                    }
                }
            });
        };

        $scope.clubs = [];
        $scope.clubsNames = [];
        $scope.clubsFollowed = [];
        $scope.balance = null;
        $scope.token = null;
        $scope.pushable = false;
        $scope.isLoading = false;
        $scope.url = url;
        $scope.notifs = [];
        $scope.notifTexts = {
            'notif_followed_event' : 'Nouvel événement',
            'notif_followed_news'  : 'Nouvelle news',
            'notif_followed_poll'  : 'Nouveau sondage',
            'notif_ponthub'        : 'Nouveaux fichiers Ponthub',
            'notif_ki_answer'      : 'Réponse à une requête de dépannage',
            'notif_shotgun_h-1'    : 'Avertissement Shotgun H-1',
            'notif_shotgun_m-5'    : 'Avertissement Shotgun M-5',
            'notif_followed_annal' : 'Ajout d\'une annale',
            'notif_next_class'     : 'Un cours va bientôt commencer',
            'notif_achievement'    : 'Obtention d\'un achievement',
            'notif_next_level'     : 'Passage au niveau suivant'
        };

        $scope.init = function() {
            $http.get(url + '/foyer/balance').success(function(data){
                $scope.balance = data.balance;
            });
            $http.get(url + '/own/token').success(function(data){
                $scope.token = data.token;
            });
            $scope.pushable = /Android/i.test(navigator.userAgent);
        };

        $scope.loadClubsFollowed = function() {
            $http.get(url + '/own/followed').success(function(data){
                for(var key in data) {
                    if (data.hasOwnProperty(key)) {
                        $scope.clubsFollowed.push(data[key].slug);
                    }
                }
            });
            $http.get(url + '/clubs?sort=name').success(function(data){
                for(var key in data) {
                    if (data.hasOwnProperty(key)) {
                        $scope.clubs.push(data[key].slug);
                        $scope.clubsNames.push(data[key].name);
                    }
                }
            });

            nav.pushPage('clubsFollowed.html');
        };

        $scope.changeFollowed = function(slug) {
            if ($scope.isLoading) {
                return;
            }
            $scope.isLoading = true;

            if ($scope.clubsFollowed.indexOf(slug) > -1) {
                $http.post(url + '/clubs/' + slug + '/unfollow').success(function(){
                    $scope.clubsFollowed.splice($scope.clubsFollowed.indexOf(slug), 1);
                    $scope.isLoading = false;
                });
            } else {
                $http.post(url + '/clubs/' + slug + '/follow').success(function(){
                    $scope.clubsFollowed.push(slug);
                    $scope.isLoading = false;
                });
            }
        };

        $scope.register = function() {
            PushNotifications.initialize();
        };

        $scope.unregister = function() {
            PushNotifications.unregister();
        };

        $scope.switchTheme = function() {
            $rootScope.dark = !$rootScope.dark;
            if ($rootScope.dark) {
                StorageService.set('dark', true);
            } else {
                StorageService.remove('dark');
            }
        };

        $scope.configNotifs = function() {
            $http.get(url + '/own/preferences').success(function(data){
                $scope.notifs = data;
                nav.pushPage('notifications.html');
            });
        };

        $scope.changeNotif = function(notif) {
            $http.patch(url + '/own/preferences', {key: notif, value: !$scope.notifs[notif]}).success(function(data){
                $scope.notifs[notif] = !$scope.notifs[notif];
            });
        };
    }]);
