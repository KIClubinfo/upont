angular.module('upont')
    .controller('Publish_Ctrl', ['$scope', '$rootScope', '$resource', '$http', 'newsItems', 'events', 'messages', 'Paginate', function($scope, $rootScope, $resource, $http, newsItems, events, messages, Paginate) {
        $scope.events = events;
        $scope.newsItems = newsItems;
        $scope.messages = messages;

        $scope.next = function() {
            Paginate.next($scope.newsItems).then(function(data){
                $scope.newsItems = data;
            });
        };

        $scope.nextMessages = function() {
            Paginate.next($scope.messages).then(function(data){
                $scope.messages = data;
            });
        };

        $scope.attend = function(publication){
            var i = $scope.events.data.indexOf(publication);
            // Si la personne attend déjà on ne fait qu'annuler le attend
            if ($scope.events.data[i].attend) {
                $http.delete(apiPrefix + 'events/' + $scope.events.data[i].slug + '/attend').success(function(data){
                    $scope.events.data[i].attend = false;
                    $scope.events.data[i].attendees--;
                });
            } else {
                $http.post(apiPrefix + 'events/' + $scope.events.data[i].slug + '/attend').success(function(data){
                    $scope.events.data[i].attend = true;
                    $scope.events.data[i].attendees++;

                    // Si la personne n'attendait aps avant
                    if ($scope.events.data[i].pookie) {
                        $scope.events.data[i].pookie = false;
                        $scope.events.data[i].pookies--;
                    }
                });
            }
        };

        $scope.pookie = function(publication){
            var i = $scope.events.data.indexOf(publication);
            // Si la personne pookie déjà on ne fait qu'annuler le pookie
            if ($scope.events.data[i].pookie) {
                $http.delete(apiPrefix + 'events/' + $scope.events.data[i].slug + '/decline').success(function(data){
                    $scope.events.data[i].pookie = false;
                    $scope.events.data[i].pookies--;
                });
            } else {
                $http.post(apiPrefix + 'events/' + $scope.events.data[i].slug + '/decline').success(function(data){
                    $scope.events.data[i].pookie = true;
                    $scope.events.data[i].pookies++;
                    alertify.success('Cet événement ne sera plus affiché par la suite. Tu pourras toujours le retrouver sur la page de l\'assos.');

                    // Si la personne était pookie avant
                    if ($scope.events.data[i].attend) {
                        $scope.events.data[i].attend = false;
                        $scope.events.data[i].attendees--;
                    }
                });
            }
        };

        $scope.showAttendees = function(publication){
            $http.get(apiPrefix + 'events/' + publication.slug + '/attendees').success(function(data){
                $scope.attendees = data;

                var string = '<strong>Personnes participant à l\'événement :</strong><br>';
                for (var i = 0; i < data.length; i++) {
                    if (data[i].username != $rootScope.me.username)
                        string += data[i].nick + ', ';
                }
                string = string.replace(/, $/, '');

                if (publication.attend)
                    string += publication.attendees == 1 ? 'Toi !' : ', toi !';

                alertify.alert(string);
            });
        };

        // Fonctions relatives à la publication
        var club = {name: 'Assos'};
        var init = function() {
            $scope.focus = false;
            $scope.post = {
                entry_method: 'Entrée libre',
                text: '',
                start_date: '',
                end_date: '',
                shotgun_date: ''
            };
            $scope.type = 'message';
            $scope.placeholder = 'Quoi de neuf ?';
            $scope.club = club;
            $scope.toggle = false;
        };
        init();

        $scope.changeType = function(type) {
            $scope.type = type;

            switch (type) {
                case 'message':
                    $scope.placeholder = 'Quoi de neuf ?';
                    break;
                case 'news':
                    $scope.placeholder = 'Que se passe-t-il d\'interessant ?';
                    break;
                case 'event':
                    $scope.placeholder = 'Description de l\'événement';
                    break;
            }
        };

        $scope.toggleSelect = function() {
            $scope.toggle = !$scope.toggle;
        };

        $scope.changeClub = function(club) {
            $scope.club = club;
            $scope.toggle = false;
        };

        $scope.publish = function(post, image) {
            var params  = {text: nl2br(post.text)};
            if (image) {
                params.image = image.base64;
            }

            if ($scope.type != 'message') {
                if ($scope.club != club) {
                    params.authorClub = $scope.club.slug;
                } else {
                    alertify.error('Tu n\'as pas choisi avec quelle assos publier !');
                    return;
                }
            }

            switch ($scope.type) {
                case 'message':
                    params.name = 'null';

                    $http.post(apiPrefix + 'newsitems', params).success(function(data){
                        Paginate.get('newsitems?sort=-date&limit=10&filterBy=name&filterValue=null').then(function(data){
                            $scope.messages = data;
                            alertify.success('Message publié !');
                            init();
                        });
                    }).error(function(){
                        alertify.error('Formulaire vide ou mal rempli !');
                    });
                    break;
                case 'news':
                    params.name = post.name;

                    $http.post(apiPrefix + 'newsitems', params).success(function(data){
                        Paginate.get('own/newsitems?sort=-date', 10).then(function(data){
                            $scope.newsItems = data;
                            $scope.changeType('message');
                            alertify.success('News publiée !');
                            init();
                        });
                    }).error(function(){
                        alertify.error('Formulaire vide ou mal rempli !');
                    });
                    break;
                case 'event':
                    params.name = post.name;
                    params.place = post.place;
                    params.entryMethod = post.entry_method;
                    params.startDate = moment(post.start_date).unix();
                    params.endDate = moment(post.end_date).unix();

                    if (!post.start_date || !post.end_date) {
                        alertify.error('Il faut préciser une date de début et de fin !');
                        return;
                    }

                    if (params.startDate >= params.endDate) {
                        alertify.error('La date de début doit être avant la date de fin !');
                        return;
                    }

                    if (post.entry_method == 'Shotgun') {
                        params.shotgunDate = moment(post.shotgun_date).unix();
                        params.shotgunLimit = post.shotgun_limit;
                        params.shotgunText = post.shotgun_text;

                        if (!post.shotgun_date) {
                            alertify.error('Il faut préciser une date de shotgun !');
                            return;
                        }
                        if (params.shotgunDate >= params.startDate) {
                            alertify.error('La date de shotgun doit être avant la date de début !');
                            return;
                        }
                    }

                    $http.post(apiPrefix + 'events', params).success(function(data){
                        Paginate.get('own/events').then(function(data){
                            $scope.events = data;
                            $scope.changeType('message');
                            alertify.success('Événement publié !');
                            init();
                        });
                    }).error(function(){
                        alertify.error('Formulaire vide ou mal rempli !');
                    });
                    break;
                default:
                    alertify.error('Type de publication non encore pris en charge');
            }
        };

        // Modification/suppression des messages
        $scope.modifyMessage = function(message) {
            var index = $scope.messages.data.indexOf(message);

            // On demande confirmation
            alertify.prompt('Tu peux modifier ton message :', function(e, str){
                if (e) {
                    $http.patch(apiPrefix + 'newsitems/' + $scope.messages.data[index].slug, {text: str}).success(function() {
                        $scope.messages.data[index].text = str;
                        alertify.success('Message correctement édité !');
                    });
                }
            }, $scope.messages.data[index].text);
        };

        $scope.deleteMessage = function(message) {
            var index = $scope.messages.data.indexOf(message);

            // On demande confirmation
            alertify.confirm('Est-ce vraiment ce que tu veux ?', function(e){
                if (e) {
                    $resource(apiPrefix + 'newsitems/' + $scope.messages.data[index].slug).delete(function() {
                        $scope.messages.data.splice(index, 1);
                    });
                }
            });
        };

        $scope.deletePost = function(post){
            var index = null;
            if (post.start_date) {
                index = $scope.events.data.indexOf(post);

                // On demande confirmation
                alertify.confirm('Est-ce vraiment ce que tu veux ?', function(e){
                    if (e) {
                        $resource(apiPrefix + 'events/' + $scope.events.data[index].slug).delete(function() {
                            $scope.events.data.splice(index, 1);
                        });
                    }
                });
            } else {
                index = $scope.newsItems.data.indexOf(post);

                // On demande confirmation
                alertify.confirm('Est-ce vraiment ce que tu veux ?', function(e){
                    if (e) {
                        $resource(apiPrefix + 'newsitems/' + $scope.newsItems.data[index].slug).delete(function() {
                            $scope.newsItems.data.splice(index, 1);
                        });
                    }
                });
            }
        };
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state("root.home", {
                url: '',
                templateUrl: "views/home/connected.html",
                data: {
                    title: 'Accueil - uPont',
                    top: true
                },
                controller: "Publish_Ctrl",
                resolve: {
                    newsItems: ['Paginate', function(Paginate) {
                        return Paginate.get('own/newsitems?sort=-date', 10);
                    }],
                    events: ['Paginate', function(Paginate) {
                        return Paginate.get('own/events');
                    }],
                    messages: ['Paginate', function(Paginate) {
                        return Paginate.get('newsitems?sort=-date&limit=10&filterBy=name&filterValue=null');
                    }]
                }
            });
    }]);
