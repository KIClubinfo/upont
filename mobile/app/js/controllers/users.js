var tabbar;
module
    .controller('UsersController', ['$scope', 'StorageService', '$http', function($scope, StorageService, $http) {
	    $scope.users = [];
	    $scope.chars = [];
	    $scope.userItem = [];
	    $scope.clubs = [];
	    $scope.url = url;
	    $scope.promo = 0;

	    $scope.init = function() {
	        var user = JSON.parse(StorageService.get('user'));
	        $scope.promo = parseInt(user.promo);

		    $scope.loadPromo();

		    // Sale, à corriger
		    setTimeout(function () {
                tabbar.on('postchange', function() {
                    $scope.loadPromo();
                });
		    }, 50);
	    };

	    $scope.loadPromo = function(){
	        var src = url + '/users?limit=1000&filterBy=promo&filterValue=0' + $scope.promo;
	        if (tabbar) {
	            var index = tabbar.getActiveTabIndex() - 1;
	            src = url + '/users?limit=1000&filterBy=promo&filterValue=0' + ($scope.promo + index);
	        }
		    $http.get(src).success(function(data){
			    var sorted = [];
			    var first;
			    $scope.chars = [];

			    for(var key in data) {
			        if(!data[key].first_name) {
			            continue;
			        }
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
