angular.module('upont')
    .controller('Calendrier_Ctrl', ['$scope', '$filter', 'events', function($scope, $filter, events) {
        $scope.events = [];
        for (var i = 0; i < events.length; i++) {
            $scope.events.push({
                'id': events[i].slug,
                'start_date': $filter('date')(events[i].start_date * 1000, "MM/dd/yyyy HH:mm"),
                'end_date': $filter('date')(events[i].end_date * 1000, "MM/dd/yyyy HH:mm"),
                'text': events[i].author_club.short_name + ' : ' + events[i].title
            });
        }
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state("calendrier", {
                url: '/calendrier',
                templateUrl: 'views/calendrier.html',
                controller: 'Calendrier_Ctrl',
                resolve: {
                    events: ["$resource", function($resource) {
                        return $resource(apiPrefix + "own/events").query().$promise;
                    }]
                }
            });
    }]);
