module
    .controller('EventsController', ['$scope', 'StorageService', '$http', function($scope, StorageService, $http) {
        $scope.events = [];
        $scope.eventItem = [];
        $scope.likes = [];
        $scope.attendees = [];
        $scope.eventToday = 'true';
        $scope.url = url;
        $scope.shotgun = [];
        $scope.motivation = '';
        $scope.shotgunned = false;
        $scope.isLoading = false;

        var startDay = new Date();
        startDay.setHours(0, 0, 0, 0);
        var endDay = new Date();
        endDay.setHours(23, 59, 59, 999);
        $scope.startDay = Math.floor(startDay.getTime() / 1000);
        $scope.endDay = Math.floor(endDay.getTime() / 1000);
        var now = new Date();
        now = Math.floor(now.getTime() / 1000);

        $scope.init = function($done){
            var events;
            $http.get(url + '/own/events').success(function(data) {
                events = data;

                // On charge aussi les cours et on les fait passer pour des events
                $http.get(url + '/own/courseitems').success(function(data) {
                    var count = 0;

                    for (var key in data) {
                        if (data.hasOwnProperty(key)) {
                            // On ejecte les cours du matin déjà passés et ceux du lendemain
                            if (data[key].start_date > $scope.endDay || data[key].end_date < now) {
                                continue;
                            }
                            // On en prodite pour compter les events qui restent aujourd'hui
                            if (data[key].start_date <= endDay) {
                                count++;
                            }

                            events.push(data[key]);
                        }
                    }
                    $scope.events = events;

                    // S'il n'y a rien à afficher aujourd'hui, on affiche demain
                    if (count === 0) {
                        $scope.eventToday = 'false';
                    }
                })
                .finally(function() {
                    if ($done) {
                        $done();
                    }
                });
            });
        };

        $scope.load = function(slug){
            $http.get(url + '/events/' + slug).success(function(data){
                $scope.eventItem = data;
                nav.pushPage('event.html');
            });
        };

        $scope.likeClick = function(){
            if ($scope.isLoading) {
                return;
            }
            $scope.isLoading = true;

            // Si la personne like déjà on ne fait qu'annuler le like
            if ($scope.eventItem.like) {
                $http.delete(url + '/events/' + $scope.eventItem.slug + '/like').success(function(data){
                    $scope.eventItem.like = false;
                    $scope.eventItem.likes--;
                    $scope.isLoading = false;
                });
            } else {
                $http.post(url + '/events/' + $scope.eventItem.slug + '/like').success(function(data){
                    $scope.eventItem.like = true;
                    $scope.eventItem.likes++;
                    $scope.isLoading = false;

                    // Si la personne unlikait avant
                    if ($scope.eventItem.dislike) {
                        $scope.eventItem.dislike = false;
                        $scope.eventItem.dislikes--;
                    }
                });
            }
        };

        $scope.dislikeClick = function(){
            if ($scope.isLoading) {
                return;
            }
            $scope.isLoading = true;

            // Si la personne like déjà on ne fait qu'annuler le like
            if ($scope.eventItem.dislike) {
                $http.delete(url + '/events/' + $scope.eventItem.slug + '/dislike').success(function(data){
                    $scope.eventItem.dislike = false;
                    $scope.eventItem.dislikes--;
                    $scope.isLoading = false;
                });
            } else {
                $http.post(url + '/events/' + $scope.eventItem.slug + '/dislike').success(function(data){
                    $scope.eventItem.dislike = true;
                    $scope.eventItem.dislikes++;
                    $scope.isLoading = false;

                    // Si la personne unlikait avant
                    if ($scope.eventItem.like) {
                        $scope.eventItem.like = false;
                        $scope.eventItem.likes--;
                    }
                });
            }
        };

        $scope.attendClick = function(){
            // Si la personne like déjà on ne fait qu'annuler le like
            if ($scope.eventItem.attend) {
                $http.delete(url + '/events/' + $scope.eventItem.slug + '/attend').success(function(data){
                    $scope.eventItem.attend = false;
                    $scope.eventItem.attendees--;
                });
            } else {
                $http.post(url + '/events/' + $scope.eventItem.slug + '/attend').success(function(data){
                    $scope.eventItem.attend = true;
                    $scope.eventItem.attendees++;

                    // Si la personne unlikait avant
                    if ($scope.eventItem.pookie) {
                        $scope.eventItem.pookie = false;
                        $scope.eventItem.pookies--;
                    }
                });
            }
        };

        $scope.declineClick = function(){
            // Si la personne like déjà on ne fait qu'annuler le like
            if ($scope.eventItem.pookie) {
                $http.delete(url + '/events/' + $scope.eventItem.slug + '/decline').success(function(data){
                    $scope.eventItem.pookie = false;
                    $scope.eventItem.pookies--;
                });
            } else {
                $http.post(url + '/events/' + $scope.eventItem.slug + '/decline').success(function(data){
                    $scope.eventItem.pookie = true;
                    $scope.eventItem.pookies++;

                    // Si la personne unlikait avant
                    if ($scope.eventItem.attend) {
                        $scope.eventItem.attend = false;
                        $scope.eventItem.attendees--;
                    }
                });
            }
        };

        $scope.loadComments = function(){
            $http.get(url + '/events/' + $scope.eventItem.slug + '/comments').success(function(data){
                nav.pushPage('views/comments.html', {comments: data, route: '/events/' + $scope.eventItem.slug});
            });
        };

        $scope.loadAttendees = function(){
            $http.get(url + '/events/' + $scope.eventItem.slug + '/attendees').success(function(data){
                $scope.attendees = data;
                nav.pushPage('attendees.html');
            });
        };

        $scope.loadShotgun = function(){
            $http.get(url + '/events/' + $scope.eventItem.slug + '/shotgun').success(function(data){
                $scope.shotgun = data;

                // On regarde si le shotgun a déjà eu lieu ou non
                if (typeof data.position != 'undefined' || typeof data.waitingList != 'undefined') {
                    $scope.shotgunned = true;
                }
                nav.pushPage('shotgun.html');
            });
        };

        $scope.shotgunEvent = function(){
            if ($scope.motivation === '') {
                $scope.motivation = ' ';
            }

            $http.post(url + '/events/' + $scope.eventItem.slug + '/shotgun', {motivation: $scope.motivation}).success(function(data){
                $http.get(url + '/events/' + $scope.eventItem.slug + '/shotgun').success(function(data){
                    $scope.shotgun = data;
                    $scope.shotgunned = true;
                });
            });
        };

        $scope.deleteShotgun = function(){
            ons.notification.confirm({
                title: 'Annuler le shotgun',
                message: 'Est-tu sûr(e) ? Aucun retour en arrière n\'est possible !',
                buttonLabels: ['Oui, je laisse', 'Non, je garde !'],
                animation: 'default',
                cancelable: true,
                primaryButtonIndex: 1,
                callback: function(index) {
                    if(index === 0) {
                        $http.delete(url + '/events/' + $scope.eventItem.slug + '/shotgun').success(function(data){
                            $scope.shotgun = data;
                            $scope.shotgunned = false;
                        });
                    }
                }
            });
        };
    }]);
