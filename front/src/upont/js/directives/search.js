import angular from 'angular';

import { API_PREFIX } from 'upont/js/config/constants';

import template_search from './search.html';

angular.module('upont').directive('upSearch', function() {
    return {
        compile: function(element) {
            element.addClass('Search');
        },
        controller: [
            '$rootScope',
            '$scope',
            '$resource',
            function($rootScope, $scope, $resource) {
                // RECHERCHE
                var empty = {
                    posts: [],
                    clubs: [],
                    files: [],
                    users: []
                };
                $scope.searchResults = empty;

                $scope.doSearch = function(string) {
                    if (string.length > 2) {
                        $resource(API_PREFIX + 'search').save({
                            search: '/' + string
                        }, function(data) {
                            $scope.searchResults = data;

                            if (!$rootScope.isStudentNetwork)
                                $scope.searchResults.files = [];
                            }
                        );
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

                $scope.resetSearch = function() {
                    $scope.searchValue = '';
                    $scope.searchResults = empty;
                };
            }
        ],
        templateUrl: template_search
    };
});
