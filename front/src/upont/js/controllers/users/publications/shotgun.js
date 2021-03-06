import alertify from 'alertifyjs';

import { API_PREFIX } from 'upont/js/config/constants';

/* @ngInject */
class Publications_Shotgun_Ctrl {
    constructor($scope, $resource, $http, event, shotgun) {
        $scope.event = event;
        $scope.shotgun = shotgun;
        $scope.shotgunned = false;
        $scope.motivation = '';
        $scope.isLoading = false;

        $scope.computeDelay = (entry) => {
          const delay = moment.duration(moment(entry.date).diff(moment(event.shotgun_date)));
          return delay.asSeconds();
        };

        $scope.shotgunEvent = function(){
            if(!$scope.isLoading) {
                if ($scope.motivation === '') {
                    $scope.motivation = 'Shotgun !';
                }

                $scope.isLoading = true;
                $http.post(API_PREFIX + 'events/' + $scope.event.slug + '/shotgun', {motivation: $scope.motivation})
                    .then(function(){
                        $resource(API_PREFIX + 'events/' + $scope.event.slug + '/shotgun').get(function(data){
                            $scope.shotgun = data;
                            $scope.shotgunned = true;
                        });
                        $scope.isLoading = false;
                    }, function(){
                        $scope.isLoading = false;
                    });
            }
        };

        $scope.deleteShotgun = function(){
            alertify.confirm('Attention c\'est définitif !', () => {
                $http.delete(API_PREFIX + 'events/' + $scope.event.slug + '/shotgun').then((response) => {
                    $scope.shotgun = response.data;
                    $scope.shotgunned = false;
                    alertify.success('Nickel ! Ta place sera redistribuée aux prochains sur la liste d\'attente ;)');
                });
            });
        };
    }
}

export default Publications_Shotgun_Ctrl;
