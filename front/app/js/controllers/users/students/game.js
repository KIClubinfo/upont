angular.module('upont')
	.controller('Students_Game_Ctrl', ['$scope', '$rootScope', '$http', function($scope, $rootScope, $http) {
		$scope.playing = false;
		$scope.end = false;
		$scope.position = 0;
		$scope.gameData = [];

		$scope.post = function() {
			$scope.playing = true;
			$scope.end = false;
			$scope.position = 0;

			var params = {
				mode: 'Normal'
			};

			$http.post($rootScope.url + 'facegames', params).success(function(data) {
				$scope.gameData = data;

				$scope.answer = $scope.gameData.list_users[$scope.position].answer;
				$scope.name1 = $scope.gameData.list_users[$scope.position][0];
				$scope.name2 = $scope.gameData.list_users[$scope.position][1];
				$scope.name3 = $scope.gameData.list_users[$scope.position][2];
				$scope.picture = '/api/' + $scope.gameData.list_users[$scope.position].image;
			});
		};

		$scope.next = function(num) {
			if (num == $scope.answer) {
				$scope.gameData.list_users[$scope.position].result = true;
			} else {
				$scope.gameData.list_users[$scope.position].result = false;
				$scope.gameData.list_users[$scope.position].answered = num;
			}

			$scope.position++;

			if ($scope.position == $scope.gameData.list_users.length) {
				$scope.end = true;
				$scope.playing = false;

				$http.delete($rootScope.url + 'facegames/' + $scope.gameData.id);
			} else {
				$scope.answer = $scope.gameData.list_users[$scope.position].answer;
				$scope.name1 = $scope.gameData.list_users[$scope.position][0];
				$scope.name2 = $scope.gameData.list_users[$scope.position][1];
				$scope.name3 = $scope.gameData.list_users[$scope.position][2];
				$scope.picture = '/api/' + $scope.gameData.list_users[$scope.position].image;
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