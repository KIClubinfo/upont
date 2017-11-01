angular.module('upont')
    .controller('Aside_Ctrl', ['$scope', '$rootScope', '$resource', '$http', '$interval', 'Achievements', function($scope, $rootScope, $resource, $http, $interval, Achievements) {
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

        $scope.toggleOpenState = function() {
            $http.patch(apiPrefix + 'clubs/ki', {open: !$scope.open}).then(function(response){
                    $scope.open = response.data.open;
                });
        }
        // Gens en ligne
        refreshData = function() {
            $resource(apiPrefix + 'refresh').get(function(data){
                $scope.online = data.online;
                $scope.open = data.open;
            });
        };
        refreshData();
        $rootScope.updateInfo = $interval(refreshData, 60000);
    }]);
