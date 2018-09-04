import alertify from 'alertifyjs';

import { API_PREFIX } from 'upont/js/config/constants';

/* @ngInject */
class Students_Game_Ctrl {
    constructor($scope, $rootScope, $http, $timeout, Achievements, globalStatistics) {
        $scope.playing = false;
        $scope.end = false;
        $scope.position = 0;
        $scope.start = 0;
        $scope.clock = 0;
        $scope.tickInterval = 1;
        $scope.promos = $rootScope.config.promos.all.slice($rootScope.config.promos.all.length - 4).concat([
            'Toutes'
        ]);
        $scope.promo = 'Toutes';
        $scope.hardcore = false;
        $scope.firstPart = false;
        $scope.trait = '';
        $scope.traits = {
            location: 'Résidence',
            promo: 'Promo',
            department: 'Département',
            nationality: 'Nationalité',
            origin: 'Origine'
        };

        $scope.loadStats = function() {
            $scope.globalStatistics = globalStatistics;
            $http.get(API_PREFIX + 'statistics/facegame/' + $rootScope.username).then(function(response){
                $scope.userStatistics = response.data;
            });
        };

        $scope.loadStats();

        var timer;

        var tick = function() {
            $scope.clock = Date.now();
            timer = $timeout(tick, $scope.tickInterval);
        };

        $scope.switchPromo = function(promo) {
            $scope.promo = promo;
        };

        $scope.text = function(trait) {
            if (trait === 'department') {
                return 'Département';
            } else if (trait === 'promo') {
                return 'Promo';
            } else if (trait === 'location') {
                return 'Résidence';
            } else if (trait === 'origin') {
                return 'Origine';
            } else if (trait === 'nationality') {
                return 'Nationalité';
            }
        };

        $scope.post = function(promo, hardcore) {
            var params = {
                promo: promo,
                hardcore: hardcore
            };
            $scope.hardcore = hardcore;

            if (promo == 'Toutes') {
                params.promo = undefined;
            }

            $http.post(API_PREFIX + 'facegames', params).then(function(response) {
                $scope.hardcore = hardcore;
                $scope.playing = true;
                $scope.end = false;
                $scope.firstPart = true;
                $scope.numWrong = 0;
                $scope.position = 0;
                $scope.start = Date.now();
                $scope.clock = Date.now();
                timer = $timeout(tick, $scope.tickInterval);

                $scope.gameData = response.data;

                $scope.answer = $scope.gameData.list_users[$scope.position].answer;
                $scope.name = $scope.gameData.list_users[$scope.position][$scope.answer].name;
                $scope.picture = $scope.gameData.list_users[$scope.position][$scope.answer].picture;
                $scope.name1 = $scope.gameData.list_users[$scope.position][0].name;
                $scope.name2 = $scope.gameData.list_users[$scope.position][1].name;
                $scope.name3 = $scope.gameData.list_users[$scope.position][2].name;
                $scope.picture1 = $scope.gameData.list_users[$scope.position][0].picture;
                $scope.picture2 = $scope.gameData.list_users[$scope.position][1].picture;
                $scope.picture3 = $scope.gameData.list_users[$scope.position][2].picture;

                if (hardcore) {
                    $scope.trait = $scope.gameData.list_users[$scope.position].trait;

                    $scope.traitValue = $scope.gameData.list_users[$scope.position][$scope.answer].trait;
                    $scope.traitValue1 = $scope.gameData.list_users[$scope.position][0].trait;
                    $scope.traitValue2 = $scope.gameData.list_users[$scope.position][1].trait;
                    $scope.traitValue3 = $scope.gameData.list_users[$scope.position][2].trait;
                }
                $scope.loadStats();
            }, function() {
                alertify.error('La promo sélectionnée ne contient pas assez d\'élèves.');
            });
        };

        $scope.next = function(num) {
            if (num == $scope.answer) {
                $scope.gameData.list_users[$scope.position].result = true;
            } else {
                $scope.gameData.list_users[$scope.position].result = false;
                $scope.gameData.list_users[$scope.position].answered = num;
                $scope.numWrong++;
            }

            $scope.position++;

            if ($scope.position == $scope.gameData.list_users.length) {
                $timeout.cancel(timer);
                $scope.end = true;
                $scope.playing = false;

                $http.patch(API_PREFIX + 'facegames/' + $scope.gameData.id, {wrongAnswers: $scope.numWrong, duration: ($scope.clock-$scope.start) + 5000 * $scope.numWrong}).then(function(){
                    Achievements.check();
                });
            } else {
                $scope.firstPart = $scope.gameData.list_users[$scope.position].firstPart;
                $scope.answer = $scope.gameData.list_users[$scope.position].answer;
                $scope.name = $scope.gameData.list_users[$scope.position][$scope.answer].name;
                $scope.picture = $scope.gameData.list_users[$scope.position][$scope.answer].picture;
                $scope.name1 = $scope.gameData.list_users[$scope.position][0].name;
                $scope.name2 = $scope.gameData.list_users[$scope.position][1].name;
                $scope.name3 = $scope.gameData.list_users[$scope.position][2].name;
                $scope.picture1 = $scope.gameData.list_users[$scope.position][0].picture;
                $scope.picture2 = $scope.gameData.list_users[$scope.position][1].picture;
                $scope.picture3 = $scope.gameData.list_users[$scope.position][2].picture;

                if ($scope.hardcore) {
                    $scope.trait = $scope.gameData.list_users[$scope.position].trait;
                    $scope.traitValue = $scope.gameData.list_users[$scope.position][$scope.answer].trait;
                    $scope.traitValue1 = $scope.gameData.list_users[$scope.position][0].trait;
                    $scope.traitValue2 = $scope.gameData.list_users[$scope.position][1].trait;
                    $scope.traitValue3 = $scope.gameData.list_users[$scope.position][2].trait;
                }
            }
        };

    }
}

export default Students_Game_Ctrl;
