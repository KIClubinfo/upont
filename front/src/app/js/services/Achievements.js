angular.module('upont').factory('Achievements', ['$resource', '$rootScope', 'Permissions', function($resource, $rootScope, Permissions) {
    return {
        check: function() {
            if(Permissions.hasRight('ROLE_EXTERIEUR'))
                return;

            $resource(API_PREFIX + 'own/achievements').get(function(data){
                var unlocked = data.unlocked;
                for (var key in unlocked) {
                    alertify.success('<div class="flex-row flex p-space-between s-stretch">' +
                        '<div class="flex-33pct  text-center">' +
                            '<i class="fa  fa-' + unlocked[key].image + '  huge"></i>' +
                        '</div>' +
                        '<div class="flex-66pct">' +
                            '<strong>' + unlocked[key].name + '</strong><br>' + unlocked[key].description + '<br><strong>' + unlocked[key].points + '</strong> points' +
                        '</div>' +
                    '</div>');
                }
                if (unlocked.length > 0)
                    $rootScope.$broadcast('newAchievement');
            });
        },
    };
}]);
