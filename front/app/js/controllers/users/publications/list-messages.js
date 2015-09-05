angular.module('upont')
    .controller('Messages_Ctrl', ['$scope', 'messages', function($scope, messages) {
        $scope.messages = messages;
    }])
    .controller('Publications_List_Messages_Ctrl', ['$scope', '$resource', '$http', 'messages', 'Paginate', function($scope, $resource, $http, messages, Paginate) {
        $scope.messages = messages;
        $scope.next = function() {
            Paginate.next($scope.messages).then(function(data){
                $scope.messages = data;
            });
        };

        // Modification/suppression des messages
        $scope.modify = function(message) {
            var index = $scope.messages.data.indexOf(message);

            // On demande confirmation
            alertify.prompt('Tu peux modifier ton message :', function(e, str){
                if (e) {
                    $http.patch(apiPrefix + 'newsitems/' + $scope.messages.data[index].slug, {text: str}).success(function() {
                        $scope.messages.data[index].text = str;
                        alertify.success('Message correctement édité');
                    });
                }
            }, $scope.messages.data[index].text);
        };

        $scope.delete = function(message) {
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

        $scope.$on('newMessage', function(event, args) {
            Paginate.first($scope.messages).then(function(data){
                $scope.messages = data;
            });
        });
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.messages', {
                url: 'messages',
                templateUrl: 'controllers/users/messages.html',
                data: {
                    title: 'Messages - uPont',
                    top: true
                },
                controller: 'Messages_Ctrl',
                resolve: {
                    messages: ['Paginate', function(Paginate) {
                        return Paginate.get('newsitems?sort=-date&limit=10&name=message');
                    }]
                }
            })
            .state('root.users.publications.message', {
                url: 'publications-messages/:slug',
                templateUrl: 'controllers/users/publications/list-messages.html',
                data: {
                    title: 'Publication - uPont',
                    top: true
                },
                controller: 'Messages_Ctrl',
                resolve: {
                    messages: ['Paginate', '$stateParams', function(Paginate, $stateParams) {
                        return Paginate.get('newsitems?slug=' + $stateParams.slug);
                    }]
                }
            });
    }]);
