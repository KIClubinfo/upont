import alertify from 'alertifyjs';

import { API_PREFIX } from 'upont/js/config/constants';

/* @ngInject */
class Ponthub_Requests_Ctrl {
    constructor($rootScope, $scope, $http, Paginate, requests) {
        $scope.requests = requests;
        $scope.predicate = 'votes';
        $scope.reverse = true;
        $scope.name = '';

        $scope.addPoint = request => {
            request.votes = request.votes + 1;
            $http.patch(API_PREFIX + 'requests/' + request.slug + '/upvote');
        };
        $scope.delete = request => {
            $http.delete(API_PREFIX + 'requests/' + request.slug)
                .then(
                    () => {
                        alertify.success('Demande supprimée !');
                        Paginate.first($scope.requests).then(data => {
                            $scope.requests = data;
                        });
                    },
                    () => {
                        alertify.error('Erreur...');
                    },
                );
        };

        $scope.post = name => {
            if (name === undefined) {
                alertify.error('Au moins un des champs n\'est pas rempli');
                return;
            }

            $http.post(API_PREFIX + 'requests', { name: name })
                .then(() => {
                    alertify.success('Demande ajoutée !');
                    Paginate.first($scope.requests).then(data => {
                        $scope.requests = data;
                    });
                    $scope.name = '';
                }, () => {
                    alertify.error('Erreur...');
                })
            ;
        };

    }
}

export default Ponthub_Requests_Ctrl;
