angular.module('upont')
	.controller('Students_Game_Ctrl', ['$scope', '$rootScope', '$http', '$timeout', function($scope, $rootScope, $http, $timeout) {
		$scope.playing = false;
		$scope.end = false;
		$scope.position = 0;
		$scope.start = 0;
		$scope.clock = 0;
	    $scope.tickInterval = 1000;
	    $scope.promos = $rootScope.promos;
	    $scope.promos.pop();
	    $scope.promos.push('Toutes');
	    $scope.promo = 'Toutes';
	    $scope.hardcore = false;
	    $scope.change = false;
	    $scope.trait = '';

	    var timer;

	    var tick = function() {
	        $scope.clock = Date.now();
	        timer = $timeout(tick, $scope.tickInterval);
	    };

		$scope.switchPromo = function(promo) {
			$scope.promo = promo;
		};

		$scope.text = function(trait) {
			var text = '';

			if (trait === 'department') {
				return 'Département';
			} else if (trait === 'promo') {
				return 'Promo';
			} else if (trait === 'location') {
				return 'Résidence';
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

			$http.post($rootScope.url + 'facegames', params).success(function(data) {
				$scope.playing = true;
				$scope.end = false;
				$scope.change = true;
				$scope.numWrong = 0;
				$scope.position = 0;
				$scope.start = Date.now();
				$scope.clock = Date.now();
				timer = $timeout(tick, $scope.tickInterval);

				$scope.gameData = data;

				$scope.answer = $scope.gameData.list_users[$scope.position].answer;
				$scope.name = $scope.gameData.list_users[$scope.position][$scope.answer][0];
				$scope.picture = '/api/' + $scope.gameData.list_users[$scope.position][$scope.answer][1].replace(/images/, 'thumbnails');
				$scope.name1 = $scope.gameData.list_users[$scope.position][0][0];
				$scope.name2 = $scope.gameData.list_users[$scope.position][1][0];
				$scope.name3 = $scope.gameData.list_users[$scope.position][2][0];
				$scope.picture1 = '/api/' + $scope.gameData.list_users[$scope.position][0][1].replace(/images/, 'thumbnails');
				$scope.picture2 = '/api/' + $scope.gameData.list_users[$scope.position][1][1].replace(/images/, 'thumbnails');
				$scope.picture3 = '/api/' + $scope.gameData.list_users[$scope.position][2][1].replace(/images/, 'thumbnails');

				if (hardcore) {
					$scope.trait = $scope.gameData.list_users[$scope.position].trait;

					$scope.traitValue = $scope.gameData.list_users[$scope.position][$scope.answer][2];
					$scope.traitValue1 = $scope.gameData.list_users[$scope.position][0][2];
					$scope.traitValue2 = $scope.gameData.list_users[$scope.position][1][2];
					$scope.traitValue3 = $scope.gameData.list_users[$scope.position][2][2];
				}
			}).error(function() {
				alertify.error('La promo sélectionnée ne contient pas assez d\'élèves.');
				return;
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

				$http.delete($rootScope.url + 'facegames/' + $scope.gameData.id);
			} else {
				$scope.change = $scope.position < ($scope.gameData.list_users.length)/2;
				$scope.answer = $scope.gameData.list_users[$scope.position].answer;
				$scope.name = $scope.gameData.list_users[$scope.position][$scope.answer][0];
				$scope.picture = '/api/' + $scope.gameData.list_users[$scope.position][$scope.answer][1].replace(/images/, 'thumbnails');
				$scope.name1 = $scope.gameData.list_users[$scope.position][0][0];
				$scope.name2 = $scope.gameData.list_users[$scope.position][1][0];
				$scope.name3 = $scope.gameData.list_users[$scope.position][2][0];
				$scope.picture1 = '/api/' + $scope.gameData.list_users[$scope.position][0][1].replace(/images/, 'thumbnails');
				$scope.picture2 = '/api/' + $scope.gameData.list_users[$scope.position][1][1].replace(/images/, 'thumbnails');
				$scope.picture3 = '/api/' + $scope.gameData.list_users[$scope.position][2][1].replace(/images/, 'thumbnails');

				if ($scope.hardcore) {
					$scope.trait = $scope.gameData.list_users[$scope.position].trait;
					$scope.traitValue = $scope.gameData.list_users[$scope.position][$scope.answer][2];
					$scope.traitValue1 = $scope.gameData.list_users[$scope.position][0][2];
					$scope.traitValue2 = $scope.gameData.list_users[$scope.position][1][2];
					$scope.traitValue3 = $scope.gameData.list_users[$scope.position][2][2];
				}
			}
		};

	}])
	.config(['$stateProvider', function($stateProvider) {
		$stateProvider
			.state('root.users.students.game', {
                url: '/game',
                templateUrl: 'views/users/students/game.html',
                controller: 'Students_Game_Ctrl',
                data: {
                    title: 'Jeu - uPont',
                    top: true
                }
            });
    }]);
