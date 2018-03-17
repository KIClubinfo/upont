import alertify from 'alertifyjs';
import angular from 'angular';

import {API_PREFIX} from 'upont/js/config/constants';

angular.module('upont').factory('Achievements', ['$http', '$rootScope', 'AuthService', function ($http, $rootScope, AuthService) {
    return {
        check: function () {
            if (!AuthService.getUser().isStudent)
                return;

            $http.get(API_PREFIX + 'own/achievements').then((response) => {
                const data = response.data;
                const unlocked = data.unlocked;
                for (const achievement of unlocked) {
                    alertify.success(`
                        <div class="flex-row flex p-space-between s-stretch">
                            <div class="flex-33pct  text-center">
                                <i class="fa  fa-${achievement.image}  huge"></i>
                            </div>
                            <div class="flex-66pct">
                                <strong>${achievement.name}</strong><br> 
                                ${achievement.description} <br>
                                <strong>${achievement.points}</strong> points
                            </div>
                        </div>
                    `);
                }
                if (unlocked.length > 0)
                    $rootScope.$broadcast('newAchievement');
            });
        },
    };
}]);
