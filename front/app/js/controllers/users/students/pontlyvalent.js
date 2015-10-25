angular.module('upont')
	.controller('Students_Pontlyvalent_Ctrl', ['$scope', '$rootScope', '$resource', '$http', function($scope, $rootScope, $resource, $http) {
		$scope.searchResult = [];
		$scope.search = '';

        $scope.searchUser = function(string) {
            if (string === '') {
                $scope.searchResults = [];
            } else {
                $http.post(apiPrefix + 'search', {search: 'User/' + string}).success(function(data){
                    $scope.searchResults = data.users;
                });
            }
        };


	}])
	.config(['$stateProvider', function($stateProvider) {
		$stateProvider
			.state('root.users.students.pontlyvalent', {
				url: '/pontlyvalent',
				templateUrl: 'controllers/users/students/pontlyvalent.html',
				controller: 'Students_Pontlyvalent_Ctrl',
				data: {
					title: 'Pontlyvalent - uPont',
					top: true
				},
			});
	}]);
