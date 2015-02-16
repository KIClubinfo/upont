angular.module('upont')
    .controller('Disconnected_Ctrl', ['$scope', '$state', 'StorageService', '$http', function ($scope, $state, StorageService, $http) {
        $scope.login = function(pseudo, mdp){
            $http
                .post(apiPrefix+"login", {username: pseudo, password: mdp})
                .success(function (data, status, headers, config) {
                    var tokenData = (data.token).split('.')[1].replace(/-/g, '+').replace(/_/g, '/');
                    switch (tokenData.length % 4) {
                        case 0: { break; }
                        case 2: { tokenData += '=='; break; }
                        case 3: { tokenData += '='; break; }
                        default: return false;
                    }
                    tokenData = JSON.parse(Base64.decode(tokenData));
                    StorageService.set('token_exp', tokenData.exp);
                    StorageService.set('token', data.token);
                    StorageService.set('droits', data.data.roles);
                    $state.go("home.connected");
                })
                .error(function (data, status, headers, config) {
                    // Supprime tout token en cas de mauvaise identification
                    if(StorageService.get('token')){
                        StorageService.remove('token');
                        StorageService.remove('token_exp');
                        StorageService.remove('droits');
                    }
                });
        };
    }])
    .controller('Publis_Ctrl', ['$scope', '$resource', 'newsItems', 'events', function ($scope,$resource, newsItems, events) {
        $scope.publications = events.concat(newsItems).sort(function(a,b){ return a.date < b.date; });
    }])
    .controller('Event_Ctrl', ['$scope', '$resource', "$stateParams", 'evenement', function ($scope, $resource, $stateParams, evenement) {
        $scope.evenement = evenement;
        $scope.url = 'events/'+$stateParams.slug;
    }])
	.config(['$stateProvider', function ($stateProvider){
        $stateProvider
            .state("home", {
                url : "/",
                template : "<div ui-view></div>",
                data : { parent : "home", defaultChild : "connected" },
            })
            .state("home.connected", {
                url : "",
                templateUrl : "views/home/connected.html",
                data : { parent : "home.connected", defaultChild : "liste" },
            })
            .state("home.disconnected", {
                url : "",
                templateUrl : "views/home/disconnected.html",
                controller : "Disconnected_Ctrl"
            })
            .state("home.connected.liste", {
                url : "publications",
                templateUrl : "views/home/publiListe.html",
                controller : "Publis_Ctrl",
                resolve : {
                    newsItems : ["$resource", function($resource){
                        return $resource(apiPrefix+"own/newsitems").query().$promise;
                    }],
                    events : ["$resource", function($resource){
                        return $resource(apiPrefix+"own/events").query().$promise;
                    }]
                }
            });
            // .state("home.connected.event", {
            //     url : "events/:slug",
            //     templateUrl : "views/home/event.html",
            //     controller : "Event_Ctrl",
            //     data : { toParent : true },
            //     resolve : {
            //         evenement : ["$resource", "$stateParams", function($resource, $stateParams){
            //             return $resource(apiPrefix+"events/:slug").get({ slug : $stateParams.slug }).$promise;
            //         }]
            //     }
            // });
    }]);
