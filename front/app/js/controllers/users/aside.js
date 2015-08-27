angular.module('upont')
    .controller('Aside_Ctrl', ['$scope', '$rootScope', '$resource', '$interval', 'Achievements', function($scope, $rootScope, $resource, $interval, Achievements) {
        // CHARGEMENT DES DONNÉES DE BASE
        // Version de uPont
        $resource(apiPrefix + 'version').get(function(data){
            $scope.version = data;
        });

        var loadAchievements = function() {
            $resource(apiPrefix + 'own/achievements?all').get(function(data) {
                $scope.level = data.current_level;
            });
        };
        loadAchievements();

        $rootScope.$on('newAchievement', function() {
            loadAchievements();
        });

        // Gens en ligne
        reloadOnline = function() {
            $resource(apiPrefix + 'online').query(function(data){
                $scope.online = data;
            });
        };
        reloadOnline();
        $rootScope.reloadOnline = $interval(reloadOnline, 60000);

        // RECHERCHE
        var empty = {posts: [], clubs: [], files: [], users: []};
        $scope.searchResults = empty;

        $scope.doSearch = function(string) {
            if (string.length > 2) {
                $resource(apiPrefix + 'search').save({search: '/' + string}, function(data){
                    $scope.searchResults = data;
                });
            } else {
                $scope.searchResults = empty;
            }
        };

        $scope.redirect = function(result) {
            switch (result.type) {
                case 'Movie':
                    return 'root.users.ponthub.category.simple({category: "films", slug: file.slug})';
                case 'Serie':
                    return 'root.users.ponthub.category.simple({category: "series", slug: file.slug})';
                case 'Episode':
                    return 'root.users.ponthub.category.simple({category: "series", slug: file.parent})';
                case 'Album':
                    return 'root.users.ponthub.category.simple({category: "musiques", slug: file.slug})';
                case 'Music':
                    return 'root.users.ponthub.category.simple({category: "musiques", slug: file.parent})';
                case 'Game':
                    return 'root.users.ponthub.category.simple({category: "jeux", slug: file.slug})';
                case 'Software':
                    return 'root.users.ponthub.category.simple({category: "logiciels", slug: file.slug})';
                case 'Other':
                    return 'root.users.ponthub.category.simple({category: "autres", slug: file.slug})';
                case 'Club':
                    return 'root.users.assos.simple.publications({slug: club.slug})';
                case 'User':
                    return 'root.users.students.simple({slug: user.slug})';
                case 'Event':
                    return 'root.users.publications.simple({slug: post.slug})';
                case 'Newsitem':
                    return 'root.users.publications.simple({slug: post.slug})';
                case 'Course':
                    return 'root.users.resources.courses.simple({slug: course.slug})';
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
            $scope.searchResults = empty;
        };
    }]);
