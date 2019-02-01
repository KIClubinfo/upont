import alertify from 'alertifyjs';

import { API_PREFIX } from 'upont/js/config/constants';

/* @ngInject */
class Ponthub_Requests_Ctrl {
    constructor($rootScope, $scope, $http, Paginate, requests) {
        $scope.requests = requests;
        $scope.predicate = 'votes';
        $scope.reverse = true;
        $scope.name = '';

        $scope.addPoint = function(request) {
            request.votes = request.votes + 1;
            $http.patch(API_PREFIX + 'requests/' + request.slug + '/upvote');
        };
        $scope.delete = function(request) {
            $http.delete(API_PREFIX + 'requests/' + request.slug)
                .then(
                    function() {
                        alertify.success('Demande supprimée !');
                        Paginate.first($scope.requests).then(data => {
                            $scope.requests = data;
                        });
                    },
                    function() {
                        alertify.error('Erreur...');
                    },
                );
        };

        $scope.post = function(name) {
            if (name === undefined) {
                alertify.error('Au moins un des champs n\'est pas rempli');
                return;
            }

            $http.post(API_PREFIX + 'requests', { name: name })
                .then(function() {
                    alertify.success('Demande ajoutée !');
                    Paginate.first($scope.requests).then(data => {
                        $scope.requests = data;
                    });
                    $scope.name = '';
                }, function() {
                    alertify.error('Erreur...');
                })
            ;
        };

    }
}

export default Ponthub_Requests_Ctrl;
