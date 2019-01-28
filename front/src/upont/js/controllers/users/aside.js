import {API_PREFIX, DOOR_SERVICE_API} from '../../config/constants';

import './infos.html';
import './footer.html';

/* @ngInject */
class Aside_Ctrl {
    constructor($scope, $rootScope, $resource, $http, $interval) {
        $scope.doorServiceUp = true;

        // CHARGEMENT DES DONNÉES DE BASE
        // Version de uPont
        $resource(API_PREFIX + 'version').get(function (data) {
            $scope.version = data;
        });

        const loadAchievements = () => {
            $resource(API_PREFIX + 'own/achievements?all').get(function (data) {
                $scope.level = data.current_level;
            });
        };
        loadAchievements();

        $rootScope.$on('newAchievement', function () {
            loadAchievements();
        });

        // Gens en ligne
        const refreshOnlineUsers = () => {
            $resource(API_PREFIX + 'refresh').get(
                (data) => {
                    $scope.online = data.online;
                },
                () => console.error('Failed to refresh online users'));
        };

        // KI-Door microservice
        const refreshDoorState = () => {
            $resource(DOOR_SERVICE_API + 'state').get(
                (data) => {
                    $scope.open = data.is_open;
                    $scope.doorServiceUp = true;
                },
                () => {
                    $scope.doorServiceUp = false;
                    console.error('Failed to retrieve KI door state')
                });
        };

        const refreshInfos = () => {
            refreshOnlineUsers();
            refreshDoorState();
        };

        refreshInfos();

        $rootScope.updateInfo = $interval(refreshInfos, 30000);
    }
}

export default Aside_Ctrl;
