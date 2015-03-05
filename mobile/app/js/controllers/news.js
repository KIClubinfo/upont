module
    .controller('NewsController', ['$scope', 'StorageService', '$http', function($scope, StorageService, $http) {
	    $scope.news = [];
	    $scope.newItem = [];
	    $scope.comments = [];
	    $scope.url = url;

	    $scope.init = function($done){
		    $http.get(url + '/own/newsitems?limit=20').success(function(data){
			    $scope.news = data;
		    })
	        .finally(function() {
                if ($done) {
                    $done();
                }
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
			        if ($scope.newItem.dislike) {
			            $scope.newItem.dislike = false;
			            $scope.newItem.dislikes--;
			        }
		        });
		    }
	    };

	    $scope.dislikeClick = function(){
	        // Si la personne like déjà on ne fait qu'annuler le like
	        if ($scope.newItem.dislike) {
		        $http.delete(url + '/newsitems/' + $scope.newItem.slug + '/dislike').success(function(data){
			        $scope.newItem.dislike = false;
			        $scope.newItem.dislikes--;
		        });
		    } else {
		        $http.post(url + '/newsitems/' + $scope.newItem.slug + '/dislike').success(function(data){
			        $scope.newItem.dislike = true;
			        $scope.newItem.dislikes++;

			        // Si la personne unlikait avant
			        if ($scope.newItem.like) {
			            $scope.newItem.like = false;
			            $scope.newItem.likes--;
			        }
		        });
		    }
	    };

	    $scope.loadComments = function(){
		    $http.get(url + '/newsitems/' + $scope.newItem.slug + '/comments').success(function(data){
			    nav.pushPage('views/comments.html', {comments: data, route: '/newsitems/' + $scope.newItem.slug});
		    });
	    };
    }]);
