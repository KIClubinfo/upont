import {API_PREFIX} from 'upont/js/config/constants';

import './infos.html';
import './footer.html';

/* @ngInject */
class Aside_Ctrl {
    constructor($scope, $rootScope, $resource, $http, $interval, $state, AuthService) {
        // CHARGEMENT DES DONNÉES DE BASE
        // Version de uPont
        $resource(API_PREFIX + 'version').get(function (data) {
            $scope.version = data;
        });

        const loadAchievements = function () {
            $resource(API_PREFIX + 'own/achievements?all').get(function (data) {
                $scope.level = data.current_level;
            });
        };
        loadAchievements();

        $rootScope.$on('newAchievement', function () {
            loadAchievements();
        });

        $scope.toggleOpenState = function () {
            $http.patch(API_PREFIX + 'clubs/ki', {open: !$scope.open}).then(function (response) {
                $scope.open = response.data.open;
            });
        };

        // Gens en ligne
        const refreshOnlineUsers = () => {
            $resource(API_PREFIX + 'refresh').get(
                (data) => {
                    $scope.online = data.online;
                    $scope.open = data.open;
                },
                () => console.error('Failed to refresh online users'));
        };
        refreshOnlineUsers();
        $scope.updateInfo = $interval(refreshOnlineUsers, 60000);

        $scope.logout = () => {
            AuthService.logout();
            // On arrête de regarder en permanence qui est en ligne
            $interval.cancel($scope.updateInfo);
            $state.go('root.login');
        };
    }
}

export default Aside_Ctrl;
