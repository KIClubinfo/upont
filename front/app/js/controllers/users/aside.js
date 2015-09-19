angular.module('upont')
    .controller('Aside_Ctrl', ['$scope', '$rootScope', '$resource', '$interval', 'Achievements', function($scope, $rootScope, $resource, $interval, Achievements) {
        // CHARGEMENT DES DONNÉES DE BASE
        // Version de uPont
        $resource(apiPrefix + 'version').get(function(data){
            $scope.version = data;
        });

        var loadAchievements = function() {
            $resource(apiPrefix + 'own/achievements?all').get(function(data) {
                $scope.level = data.current_level;
            });
        };
        loadAchievements();

        $rootScope.$on('newAchievement', function() {
            loadAchievements();
        });

        // Gens en ligne
        reloadOnline = function() {
            $resource(apiPrefix + 'online').query(function(data){
                $scope.online = data;
            });
        };
        reloadOnline();
        $rootScope.reloadOnline = $interval(reloadOnline, 60000);
    }]);
