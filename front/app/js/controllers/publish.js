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
        $scope.focus = false;
        $scope.post = [];
        $scope.type = 'message';
        $scope.placeholder = 'Quoi de neuf ?';

        $scope.changeType = function(type) {
            $scope.type = type;

            switch (type) {
                case 'message':
                    $scope.placeholder = 'Quoi de neuf ?';
                    break;
                case 'news':
                    $scope.placeholder = 'Quoi d\'interessant ?';
                    break;
                case 'event':
                    $scope.placeholder = 'Description de l\'événement';
                    break;
            }
        };

        $scope.publish = function(post, image) {
            var params  = {text: post.text};
            if (image) {
                params.image = image.base64;
            }

            switch ($scope.type) {
                case 'message':
                    params.name = 'null';

                    $http.post(apiPrefix + 'newsitems', params).success(function(data){
                        Paginate.get('newsitems?sort=-date&filterBy=name&filterValue=null').then(function(data){
                            $scope.messages = data;
                        });
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
                        return Paginate.get('newsitems?sort=-date&filterBy=name&filterValue=null');
                    }]
                }
            });
    }]);
