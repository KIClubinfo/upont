angular.module('upont')
	.controller('Students_Game_Ctrl', ['$scope', '$rootScope', '$http', function($scope, $rootScope, $http) {
		$scope.playing = false;
		$scope.end = false;
		$scope.position = 0;
		$scope.picture = '';
		$scope.name1 = '';
		$scope.name2 = '';
		$scope.name3 = '';
		$scope.answer = '';

		$scope.post = function() {
			$scope.playing = true;
			$scope.end = false;
			$scope.position = 0;

			$scope.gameData = [
				{
					answer: 'Albé',
					image_url: 'uploads/images/3.jpg',
					name1: 'Albé',
					name2: 'Deboisque',
					name3: 'Obi-Wan Kenobi'
				},
				{
					answer: 'Deboisque',
					image_url: 'uploads/images/2.jpg',
					name1: 'Albé',
					name2: 'Deboisque',
					name3: 'Obi-Wan Kenobi'
				}
			];

			$scope.answer = $scope.gameData[$scope.position].answer;
			$scope.name1 = $scope.gameData[$scope.position].name1;
			$scope.name2 = $scope.gameData[$scope.position].name2;
			$scope.name3 = $scope.gameData[$scope.position].name3;
			$scope.picture = '/api/' + $scope.gameData[$scope.position].image_url;

		};

		$scope.next = function(name) {
			if (name == $scope.answer) {
				$scope.gameData[$scope.position].result = true;
			} else {
				$scope.gameData[$scope.position].result = false;
				$scope.gameData[$scope.position].answered = name;
			}

			$scope.position++;

			if ($scope.position == $scope.gameData.length) {
				$scope.end = true;
				$scope.playing = false;
			} else {
				$scope.answer = $scope.gameData[$scope.position].answer;
				$scope.name1 = $scope.gameData[$scope.position].name1;
				$scope.name2 = $scope.gameData[$scope.position].name2;
				$scope.name3 = $scope.gameData[$scope.position].name3;
				$scope.picture = '/api/' + $scope.gameData[$scope.position].image_url;
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