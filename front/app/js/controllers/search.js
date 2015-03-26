angular.module('upont')
    .controller('Search_Ctrl', ['$scope', '$rootScope', '$state', '$http', function($scope, $rootScope, $state, $http) {
        // $scope.showCategories = false;
        $scope.searchResults = [];

        $scope.doSearch = function(string) {

            if (string.length > 2) {
                $http.post(apiPrefix + 'search', {search: '/' + string}).success(function(data){
                    $scope.searchResults = data;
                });
            } else {
                $scope.searchResults = [];
            }
        };

        $scope.redirect = function(result) {
            switch (result.type) {
                case 'Movie':
                    return 'root.ponthub.simple({category: "films", slug: file.slug})';
                case 'Serie':
                    return 'root.ponthub.simple({category: "series", slug: file.slug})';
                case 'Episode':
                    return 'root.ponthub.simple({category: "series", slug: file.parent})';
                case 'Album':
                    return 'root.ponthub.simple({category: "musiques", slug: file.slug})';
                case 'Music':
                    return 'root.ponthub.simple({category: "musiques", slug: file.parent})';
                case 'Game':
                    return 'root.ponthub.simple({category: "jeux", slug: file.slug})';
                case 'Software':
                    return 'root.ponthub.simple({category: "logiciels", slug: file.slug})';
                case 'Other':
                    return 'root.ponthub.simple({category: "autres", slug: file.slug})';
                case 'Club':
                    return 'root.channels.simple.publications({slug: club.slug})';
            }
        };

        $scope.icon = function(result) {
            switch (result.type) {
                case 'Movie':
                case 'Serie':
                case 'Episode':
                    return 'film';
                case 'Album':
                case 'Music':
                    return 'music';
                case 'Game':
                    return 'gamepad';
                case 'Software':
                    return 'desktop';
                case 'Other':
                    return 'file-o';
                case 'Club':
                    return 'users';
            }
        };

        $scope.resetSearch = function(){
            $scope.searchValue = '';
            $scope.searchResults = [];
        };
    }]);
