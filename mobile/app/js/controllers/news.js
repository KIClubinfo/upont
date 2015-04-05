module
    .controller('NewsController', ['$scope', 'StorageService', '$http', 'Paginate', function($scope, StorageService, $http, Paginate) {
        $scope.newItem = [];
        $scope.comments = [];
        $scope.url = url;
        $scope.isLoading = false;

        $scope.init = function($done){
            Paginate.get('own/newsitems?sort=-date', 10).then(function(data){
                $scope.news = data;

                Paginate.get('newsitems?sort=-date&limit=10&filterBy=name&filterValue=null').then(function(data){
                    $scope.messages = data;

                    if ($done) {
                        $done();
                    }
                });
            });
        };

        $scope.next = function() {
            Paginate.next($scope.news).then(function(data){
                $scope.news = data;
            });

            Paginate.next($scope.messages).then(function(data){
                $scope.messages = data;
            });
        };

        // Retourne un texte de news raccourci pour faire un abstract dans la liste de news
        $scope.cut = function(string) {
            if (string) {
                return string.replace(/(<([^>]+)>)/ig,"").substring(0,140) +
                       (string.replace(/(<([^>]+)>)/ig,"").length > 140 ? '...' : '');
            }
        };

        $scope.load = function(slug){
            $http.get(url + '/newsitems/' + slug).success(function(data){
                $scope.newItem = data;

                if ($scope.newItem.name != 'null') {
                    nav.pushPage('new.html');
                } else {
                    nav.pushPage('message.html');
                }
            });
        };

        $scope.likeClick = function(){
            if ($scope.isLoading) {
                return;
            }
            $scope.isLoading = true;

            // Si la personne like déjà on ne fait qu'annuler le like
            if ($scope.newItem.like) {
                $http.delete(url + '/newsitems/' + $scope.newItem.slug + '/like').success(function(data){
                    $scope.newItem.like = false;
                    $scope.newItem.likes--;
                    $scope.isLoading = false;
                });
            } else {
                $http.post(url + '/newsitems/' + $scope.newItem.slug + '/like').success(function(data){
                    $scope.newItem.like = true;
                    $scope.newItem.likes++;
                    $scope.isLoading = false;

                    // Si la personne unlikait avant
                    if ($scope.newItem.dislike) {
                        $scope.newItem.dislike = false;
                        $scope.newItem.dislikes--;
                    }
                });
            }
        };

        $scope.dislikeClick = function(){
            if ($scope.isLoading) {
                return;
            }
            $scope.isLoading = true;

            // Si la personne like déjà on ne fait qu'annuler le like
            if ($scope.newItem.dislike) {
                $http.delete(url + '/newsitems/' + $scope.newItem.slug + '/dislike').success(function(data){
                    $scope.newItem.dislike = false;
                    $scope.newItem.dislikes--;
                    $scope.isLoading = false;
                });
            } else {
                $http.post(url + '/newsitems/' + $scope.newItem.slug + '/dislike').success(function(data){
                    $scope.newItem.dislike = true;
                    $scope.newItem.dislikes++;
                    $scope.isLoading = false;

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
