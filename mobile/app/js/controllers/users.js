module
    .controller('UsersController', ['$scope', 'StorageService', '$http', function($scope, StorageService, $http) {
	    $scope.users = [];
	    $scope.chars = [];
	    $scope.userItem = [];
	    $scope.clubs = [];
	    $scope.url = url;

	    $scope.init = function(){
		    $http.get(url + '/users').success(function(data){
			    var sorted = [];
			    var first;
			    $scope.chars = [];
			    
			    for(var key in data) {
			        if(!data[key].first_name)
			            continue;
			        first = data[key].first_name[0];
			        
			        if(!sorted[first]) {
			            sorted[first] = [];
			            $scope.chars.push(first);
			        }
			        sorted[first].push(data[key]);
			    }
			    $scope.users = sorted;
		    });
	    };
	    
	    $scope.load = function(slug){
		    $http.get(url + '/users/' + slug).success(function(data){
			    $scope.userItem = data;
		    });
		    
		    $http.get(url + '/users/' + slug + '/clubs').success(function(data){
		        $scope.clubs = data;
		        nav.pushPage('user.html');
	        });
	    };
    }]); 
