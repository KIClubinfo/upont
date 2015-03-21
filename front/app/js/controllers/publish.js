angular.module('upont')
    .controller('Publish_Ctrl', ['$scope', '$resource', '$http', 'newsItems', 'events', 'messages', 'Paginate', function($scope, $resource, $http, newsItems, events, messages, Paginate) {
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

        // Fonctions relatives à la publication
        var club = {name: 'Assos'};
        var init = function() {
            $scope.focus = false;
            $scope.post = {entry_method: 'Entrée libre'};
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
            var params  = {text: post.text};
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
                            alertify.success('Message posté !');
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
                            alertify.success('News postée !');
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

                    if (params.startDate >= params.endDate) {
                        alertify.error('La date de début doit être avant la date de fin !');
                        return;
                    }

                    if (post.entry_method == 'Shotgun') {
                        params.shotgunDate = moment(post.shotgun_date).unix();
                        params.shotgunLimit = post.shotgun_limit;
                        params.shotgunText = post.shotgun_text;

                        if (params.shotgunDate >= params.startDate) {
                            alertify.error('La date de shotgun doit être avant la date de début !');
                            return;
                        }
                    }

                    $http.post(apiPrefix + 'events', params).success(function(data){
                        Paginate.get('own/events').then(function(data){
                            $scope.events = data;
                            $scope.changeType('message');
                            alertify.success('Événement posté !');
                        });
                    }).error(function(){
                        alertify.error('Formulaire vide ou mal rempli !');
                    });
                    break;
                default:
                    alertify.error('Type de publication non encore pris en charge');
            }
        };
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state("root.home", {
                url: '',
                templateUrl: "views/home/connected.html",
                data: {
                    title: 'Accueil - uPont'
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
