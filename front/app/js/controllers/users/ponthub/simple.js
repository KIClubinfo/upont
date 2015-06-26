angular.module('upont')
    .controller('Ponthub_Element_Ctrl', ['$scope', '$stateParams', '$q', 'Ponthub', 'StorageService', '$window', '$http', 'element', 'episodes', 'musics', function($scope, $stateParams, $q, Ponthub, StorageService, $window, $http, element, episodes, musics) {
        $scope.element = element;
        $scope.category = $stateParams.category;
        $scope.lastWeek = moment().subtract(7, 'days').unix();
        $scope.type = Ponthub.cat($stateParams.category);
        $scope.musics = musics;
        $scope.openSeason = -1;
        $scope.fleur = null;
        $scope.token = StorageService.get('token');

        function pingFleur() {
            var defered = $q.defer();
            var bool = false;
            ping('fleur.enpc.fr', function(status) { 
                if (status == 'timeout')
                    bool  = false;
                defered.resolve({test: bool});
            });
            return defered.promise;
        }

        if (episodes) {
            $scope.saisons = [];

            for (var i = 0; i < episodes.length; i++) {
                if (!$scope.saisons[episodes[i].season - 1]) {
                    $scope.saisons[episodes[i].season - 1] = [];
                }
                $scope.saisons[episodes[i].season - 1].push(episodes[i]);
            }
        }

        $scope.open = function(index) {
            $scope.openSeason = $scope.openSeason != index ? index : -1;
        };

        $scope.popular = function(count) {
            return Ponthub.isPopular(count, $stateParams.category);
        };

        $scope.countDownloads = function() {
            var count = 0;
            switch ($scope.category) {
                case 'series':
                    $scope.saisons.forEach(function(entry) {
                        for(var j = 0; j < entry.length; j++) {
                            count += entry[j].downloads;
                        }
                    });
                    return count;
                case 'musiques':
                    for(var k = 0; k < $scope.musics.length; k++) {
                        count += $scope.musics[k].downloads;
                    }
                    return count;
                default:
                    return $scope.element.downloads;
            }
        };
    }])
    .config(['$stateProvider', function($stateProvider) {
    }]);
