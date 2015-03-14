angular.module('upont')
    .controller('Calendrier_Ctrl', ['$rootScope', '$scope', '$filter', 'events', function($rootScope, $scope, $filter, events) {
        $scope.events = [];
        for (var i = 0; i < events.length; i++) {
            $scope.events.push({
                'id': events[i].slug,
                'start_date': $filter('date')(events[i].start_date * 1000, "MM/dd/yyyy HH:mm"),
                'end_date': $filter('date')(events[i].end_date * 1000, "MM/dd/yyyy HH:mm"),
                'text': events[i].author_club.name + ' : ' + events[i].name
            });
        }
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state("root.calendrier", {
                url: 'calendrier',
                templateUrl: 'views/calendrier.html',
                controller: 'Calendrier_Ctrl',
                data: {
                    title: "uPont - Calendrier"
                },
                resolve: {
                    events: ["$resource", function($resource) {
                        return $resource(apiPrefix + "events").query();
                    }]
                },
                onEnter: ['$rootScope', function($rootScope) {
                    $rootScope.hideFooter = true;
                }],
                onExit: ['$rootScope', function($rootScope) {
                    $rootScope.hideFooter = false;
                }]
            });
    }]);
