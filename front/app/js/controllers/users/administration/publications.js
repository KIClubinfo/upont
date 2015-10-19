angular.module('upont')
    .controller('Administration_Publications_Ctrl', ['$scope', 'events', 'newsItems', function($scope, events, newsItems) {
        $scope.events = events;
        $scope.newsItems = newsItems;
        $scope.predicate = 'user.firstname';
        $scope.reverse = false;
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.administration.publications', {
                url: '',
                controller : 'Administration_Publications_Ctrl',
                templateUrl: 'controllers/users/administration/publications.html',
                data: {
                    title: 'Activité - uPont',
                    top: true
                },
                resolve: {
                    events: ['$stateParams', 'Paginate', function($stateParams, Paginate) {
                        return Paginate.get('clubs/' + $stateParams.slug + '/events?sort=-date', 10);
                    }],
                    newsItems: ['$stateParams', 'Paginate', function($stateParams, Paginate) {
                        return Paginate.get('clubs/' + $stateParams.slug + '/newsitems?sort=-date', 10);
                    }],
                }
            });
    }]);
