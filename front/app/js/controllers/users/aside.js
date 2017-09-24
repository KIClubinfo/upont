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
        refreshData = function() {
            $resource(apiPrefix + 'refresh').query(function(data){
                $scope.online = data;
            });
        };
        refreshData();
        $rootScope.updateInfo = $interval(refreshData, 60000);
    }]);
