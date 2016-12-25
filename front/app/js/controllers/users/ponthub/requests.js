angular.module('upont')
    .controller('Ponthub_Requests_Ctrl', ['$rootScope', '$scope', '$http','$resource', 'requests', function($rootScope, $scope, $http, $resource, requests) {
        $scope.requests = requests;
        $scope.predicate = 'votes';
        $scope.reverse = true;
        $scope.name ='';

        $scope.addPoint = function(request) {
            request.votes = request.votes+1 ;
            $http.patch(apiPrefix + 'requests/' + request.slug + '/upvote');
        };
        $scope.delete = function(request) {
            $http.delete(apiPrefix + 'requests/' + request.slug)
                .then(
                    function(){
                        alertify.success('Demande supprimée !');
                        $resource(apiPrefix + 'requests').query(function(data){
                            $scope.requests = data;
                        });
                    },
                    function(){
                        alertify.error('Erreur...');
                    }
                );
        };

        $scope.post = function(name) {
            if (name === undefined) {
                alertify.error('Au moins un des champs n\'est pas rempli');
                return;
            }

            $http.post(apiPrefix + 'requests', {name: name})
                .then(function(){
                    alertify.success('Demande ajoutée !');
                    $resource(apiPrefix + 'requests').query(function(data){
                        $scope.requests = data;
                    });
                    $scope.name = '';
                }, function(){
                    alertify.error('Erreur...');
                })
            ;
        };

    }]);
