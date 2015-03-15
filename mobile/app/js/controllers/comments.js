module
    .controller('CommentsController', ['$scope', 'StorageService', '$http', function($scope, StorageService, $http) {
        $scope.comments = nav.getCurrentPage().options.comments;
        $scope.text = '';

        $scope.likeComment = function(index) {
            // Si la personne like déjà on ne fait qu'annuler le like
            if ($scope.comments[index].like) {
                $http.delete(url + '/comments/' + $scope.comments[index].id + '/like').success(function(data){
                    $scope.comments[index].like = false;
                    $scope.comments[index].likes--;
                });
            } else {
                $http.post(url + '/comments/' + $scope.comments[index].id + '/like').success(function(data){
                    $scope.comments[index].like = true;
                    $scope.comments[index].likes++;

                    // Si la personne unlikait avant
                    if ($scope.comments[index].dislike) {
                        $scope.comments[index].dislike = false;
                        $scope.comments[index].dislikes--;
                    }
                });
            }
        };

        $scope.dislikeComment = function(index) {
            // Si la personne like déjà on ne fait qu'annuler le like
            if ($scope.comments[index].dislike) {
                $http.delete(url + '/comments/' + $scope.comments[index].id + '/dislike').success(function(data){
                    $scope.comments[index].dislike = false;
                    $scope.comments[index].dislikes--;
                });
            } else {
                $http.post(url + '/comments/' + $scope.comments[index].id + '/dislike').success(function(data){
                    $scope.comments[index].dislike = true;
                    $scope.comments[index].dislikes++;

                    // Si la personne unlikait avant
                    if ($scope.comments[index].like) {
                        $scope.comments[index].like = false;
                        $scope.comments[index].likes--;
                    }
                });
            }
        };

        $scope.comment = function(text) {
            $http.post(url + nav.getCurrentPage().options.route + '/comments', {'text' : text }).success(function(data){
                $scope.comments.push(data);
                $scope.text = '';
            });
        };
    }]);
