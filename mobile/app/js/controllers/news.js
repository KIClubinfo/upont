module
    .controller('NewsController', ['$scope', 'StorageService', '$http', function($scope, StorageService, $http) {
	    $scope.news = [];
	    $scope.newItem = [];
	    $scope.likes = [];
	    $scope.url = url;

	    $scope.init = function(){
		    $http.get(url + '/own/newsitems').success(function(data){
			    $scope.news = data;
		    });
	    };
	    
	    $scope.load = function(slug){
		    $http.get(url + '/newsitems/' + slug).success(function(data){
			    $scope.newItem = data;
			    nav.pushPage('new.html');
		    });
	    };
	    
	    $scope.likeClick = function(){
	        // Si la personne like déjà on ne fait qu'annuler le like
	        if ($scope.newItem.like) {
		        $http.delete(url + '/newsitems/' + $scope.newItem.slug + '/like').success(function(data){
			        $scope.newItem.like = false;
			        $scope.newItem.likes--;
		        });
		    } else {
		        $http.post(url + '/newsitems/' + $scope.newItem.slug + '/like').success(function(data){
			        $scope.newItem.like = true;
			        $scope.newItem.likes++;
			        
			        // Si la personne unlikait avant
			        if ($scope.newItem.unlike) {
			            $scope.newItem.unlike = false;
			            $scope.newItem.unlikes--;
			        }
		        });
		    }
	    };
	    
	    $scope.unlikeClick = function(){
	        // Si la personne like déjà on ne fait qu'annuler le like
	        if ($scope.newItem.unlike) {
		        $http.delete(url + '/newsitems/' + $scope.newItem.slug + '/unlike').success(function(data){
			        $scope.newItem.unlike = false;
			        $scope.newItem.unlikes--;
		        });
		    } else {
		        $http.post(url + '/newsitems/' + $scope.newItem.slug + '/unlike').success(function(data){
			        $scope.newItem.unlike = true;
			        $scope.newItem.unlikes++;
			        
			        // Si la personne unlikait avant
			        if ($scope.newItem.like) {
			            $scope.newItem.like = false;
			            $scope.newItem.likes--;
			        }
		        });
		    }
	    };
	    
	    $scope.loadLikes = function(){
		    $http.get(url + '/newsitems/' + $scope.newItem.slug + '/like').success(function(data){
			    $scope.likes = data;
			    nav.pushPage('likes.html');
		    });
	    };
    }]);
