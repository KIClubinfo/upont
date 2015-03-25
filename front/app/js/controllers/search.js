angular.module('upont')
    .controller('Search_Ctrl', ['$scope', '$rootScope', '$state', '$http', function($scope, $rootScope, $state, $http) {
        // $scope.showCategories = false;
        $scope.searchResults = {
            users: [],
            post: [],
            channel: [],
            downloads: []
        };

        $scope.doSearch = function(string) {

            if (string.length > 2) {
                $http.post(apiPrefix + 'search', {search: 'User/' + string}).success(function(data){
                    $scope.searchResults.users = data;
                });
                $http.post(apiPrefix + 'search', {search: 'Post/' + string}).success(function(data){
                    $scope.searchResults.posts = data;
                });
                $http.post(apiPrefix + 'search', {search: 'Club/' + string}).success(function(data){
                    $scope.searchResults.channels = data;
                });
                $http.post(apiPrefix + 'search', {search: 'Ponthub/' + string}).success(function(data){
                    $scope.searchResults.downloads = data;
                });
            } else {
                $scope.searchResults = {
                    users: [],
                    posts: [],
                    channels: [],
                    downloads: []
                };
            }
        };

        $scope.redirect = function(result) {
            var slug = result.parent ? result.parent : result.slug;

            switch (result.type) {
                case 'Movie':
                    return 'root.ponthub.simple({category: "films", slug: slug})';
                    break;
                // case 'Serie':
                //     $state.go('root.ponthub.simple', {category: 'series', slug: slug});
                //     break;
                // case 'Episode':
                //     $state.go('root.ponthub.simple', {category: 'series', slug: slug});
                //     break;
                // case 'Album':
                //     $state.go('root.ponthub.simple', {category: 'musiques', slug: slug});
                //     break;
                // case 'Music':
                //     $state.go('root.ponthub.simple', {category: 'musiques', slug: slug});
                //     break;
                // case 'Game':
                //     $state.go('root.ponthub.simple', {category: 'jeux', slug: slug});
                //     break;
                // case 'Software':
                //     $state.go('root.ponthub.simple', {category: 'logiciels', slug: slug});
                //     break;
                // case 'Other':
                //     $state.go('root.ponthub.simple', {category: 'autres', slug: slug});
                //     break;
                // case 'Club':
                //     $state.go('root.channels.simple.publications', {slug: slug});
                //     break;
            }
        };

        $scope.resetSearch = function(){
            $scope.searchValue = '';
            $scope.searchResults = {
                    users: [],
                    posts: [],
                    channels: [],
                    downloads: []
            };
        }
    }]);
