angular.module('upont')
    .controller('Ponthub_Requests_Ctrl', ['$rootScope', '$scope', '$http','$resource', 'requests', function($rootScope, $scope, $http, $resource, requests) {
        $scope.requests = requests;
        $scope.predicate = 'request.votes';
        // $scope.categories = ['film','série','album','jeu','logiciel','autre'];
        $scope.name ='';

        $scope.addPoint = function(request) {
            request.votes = request.votes+1 ;
            $http.patch(apiPrefix + 'requests/' + request.slug + '/upvote');
        };

        $scope.post = function(name) {
            if (name === undefined) {
                alertify.error('Au moins un des champs n\'est pas rempli');
                return;
            }

            $http.post(apiPrefix + 'requests', {name: name})
                .success(function(){
                    alertify.success('Demande ajoutée !');
                    $resource(apiPrefix + 'requests').query(function(data){
                        $scope.requests = data;
                    });
                    $scope.name = '';
                })
                .error(function(){
                    alertify.error('Erreur...');
                })
            ;
        };

    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.ponthub.requests', {
                url: '/demandes',
                controller: 'Ponthub_Requests_Ctrl',
                templateUrl: 'controllers/users/ponthub/requests.html',
                resolve: {
                    requests: ['$resource', '$stateParams', function($resource, $stateParams) {
                        return $resource(apiPrefix + 'requests').query().$promise;
                    }]
                }
            });
    }]);
