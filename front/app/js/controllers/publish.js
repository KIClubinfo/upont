angular.module('upont')
    .controller('Publish_Ctrl', ['$scope', '$rootScope', '$state', '$http', function($scope, $rootScope, $state, $http) {
        $scope.focus = false;
        $scope.type = 'message';
        $scope.post = [];

        $scope.publish = function(post, image) {
            switch ($scope.type) {
                case 'message':
                    $http.post(apiPrefix + 'newsitems', {title: ' ', message: post.text}).success(function(data){
                        alertify.success('ok');
                    });
                    break;
                default:
                    alertify.error('Type de publication non encore pris en charge');
            }
        }

        $scope.toggleCategories = function() {
            $scope.showCategories = !$scope.showCategories;
        };

        $scope.changeCategory = function(category, searchValue) {
            $rootScope.searchCategory = category;
            $scope.doSearch(searchValue);
        };

        $rootScope.$on('$stateChangeSuccess', function(event, toState, toParams, fromState, fromParams) {
            // Changement de la catégorie de recherche
            switch (toState.name) {
                case 'root.ponthub.liste':
                case 'root.ponthub.simple':
                    $rootScope.searchCategory = 'Ponthub';
                    break;
                default:
                    $rootScope.searchCategory = 'Assos';
            }

            // On réinitialise la barre de recherche
            $scope.showCategories = false;
            $scope.searchResults = [];
            $('.search').val('');
        });

        $scope.doSearch = function(string) {
            var category = 'User';

            switch ($rootScope.searchCategory) {
                case 'Ponthub':
                    category = 'Ponthub';
                    break;
                case 'Publications':
                    category = 'Post';
                    break;
                case 'Assos':
                    category = 'Club';
                    break;
            }

            if (string === '') {
                $scope.searchResults = [];
            } else {
                $http.post(apiPrefix + 'search', {search: category + '/' + string}).success(function(data){
                    $scope.searchResults = data;
                });
            }
        };

        $scope.redirect = function(result) {
            var slug = result.parent ? result.parent : result.slug;

            switch (result.type) {
                case 'Movie':
                    $state.go('root.ponthub.simple', {category: 'films', slug: slug});
                    break;
                case 'Serie':
                    $state.go('root.ponthub.simple', {category: 'series', slug: slug});
                    break;
                case 'Episode':
                    $state.go('root.ponthub.simple', {category: 'series', slug: slug});
                    break;
                case 'Album':
                    $state.go('root.ponthub.simple', {category: 'musiques', slug: slug});
                    break;
                case 'Music':
                    $state.go('root.ponthub.simple', {category: 'musiques', slug: slug});
                    break;
                case 'Game':
                    $state.go('root.ponthub.simple', {category: 'jeux', slug: slug});
                    break;
                case 'Software':
                    $state.go('root.ponthub.simple', {category: 'logiciels', slug: slug});
                    break;
                case 'Other':
                    $state.go('root.ponthub.simple', {category: 'autres', slug: slug});
                    break;
                case 'Club':
                    $state.go('root.channels.simple.publications', {slug: slug});
                    break;
            }
            $scope.searchResults = [];
        };
    }]);
