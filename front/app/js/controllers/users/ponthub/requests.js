angular.module('upont')
    .controller('Ponthub_Requests_Ctrl', ['$rootScope', '$scope', 'club', 'requests', function($rootScope, $scope, club, requests) {
        $scope.requests = requests;
        // $scope.categories = ['film','série','album','jeu','logiciel','autre'];
        $scope.name ='';

        $scope.addPoint = function(request) {
            var x = null;
            // $scope.request.votes = $scope.request.votes+1 ;
            // http.patch($rootScope.url + 'request' + user.username, params).success(function(){
            //     $resource(apiPrefix + 'requests/:slug', {slug: request.slug}).get(function(data){
            //         request = data;
            //     });
        };

        $scope.post = function(name) {
            if (name === undefined) {
                alertify.error('Au moins un des champs n\'est pas rempli');
                return;
            }

            $http.post(apiPrefix + 'requests', {name: name})
                .success(function(){
                    alertify.success('Demande ajoutée !');
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
                        return $resource(apiPrefix + 'ponthub/requests').get().$promise;
                    }]
                }
            });
    }]);
