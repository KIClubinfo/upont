angular.module('upont').factory('Achievements', ['$resource', '$rootScope', 'Permissions', function($resource, $rootScope, Permissions) {
    return {
        check: function() {
            if(Permissions.hasRight('ROLE_EXTERIEUR'))
                return;

            $resource(apiPrefix + 'own/achievements').get(function(data){
                var unlocked = data.unlocked;
                for (var key in unlocked) {
                    alertify.success('<i class="fa fa-' + unlocked[key].image + ' up-achievement"></i><strong>' + unlocked[key].name + '</strong><br>' + unlocked[key].description + '<br><strong>+' + unlocked[key].points + '</strong> points');
                }
            });
        },
    };
}]);
