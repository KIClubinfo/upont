import alertify from 'alertifyjs';

import { API_PREFIX } from 'upont/js/config/constants';

/* @ngInject */
class Publications_List_Ctrl {
    constructor($scope, $rootScope, $resource, $http, newsItems, events, Paginate, Achievements, $location) {
        $scope.events = events;
        $scope.newsItems = newsItems;

        $scope.edit = null;

        $scope.next = function() {
            Paginate.next($scope.newsItems).then(function(response) {
                $scope.newsItems = response;
            });

            Paginate.next($scope.events).then(function(response) {
                $scope.events = response;
            });
        };

        $scope.$on('newEvent', function() {
            Paginate.first($scope.events).then(function(response) {
                $scope.events = response;
            });
        });

        $scope.$on('newNewsitem', function() {
            Paginate.first($scope.newsItems).then(function(response) {
                $scope.newsItems = response;
            });
        });

        $scope.attend = function(publication) {
            var i = $scope.events.data.indexOf(publication);
            // Si la personne attend déjà on ne fait qu'annuler le attend
            if ($scope.events.data[i].attend) {
                $http.delete(API_PREFIX + 'events/' + $scope.events.data[i].slug + '/attend').then(function() {
                    $scope.events.data[i].attend = false;
                    $scope.events.data[i].attendees--;
                });
            } else {
                $http.post(API_PREFIX + 'events/' + $scope.events.data[i].slug + '/attend').then(function() {
                    $scope.events.data[i].attend = true;
                    $scope.events.data[i].attendees++;

                    // Si la personne n'attendait pas avant
                    if ($scope.events.data[i].pookie) {
                        $scope.events.data[i].pookie = false;
                        $scope.events.data[i].pookies--;
                    }
                    Achievements.check();
                });
            }
        };

        $scope.pookie = function(publication) {
            var i = $scope.events.data.indexOf(publication);
            // Si la personne pookie déjà on ne fait qu'annuler le pookie
            if ($scope.events.data[i].pookie) {
                $http.delete(API_PREFIX + 'events/' + $scope.events.data[i].slug + '/decline').then(function() {
                    $scope.events.data[i].pookie = false;
                    $scope.events.data[i].pookies--;
                });
            } else {
                $http.post(API_PREFIX + 'events/' + $scope.events.data[i].slug + '/decline').then(function() {
                    $scope.events.data[i].pookie = true;
                    $scope.events.data[i].pookies++;
                    alertify.success('Cet événement ne sera plus affiché par la suite. Tu pourras toujours le retrouver sur la page de l\'assos.');

                    // Si la personne était pookie avant
                    if ($scope.events.data[i].attend) {
                        $scope.events.data[i].attend = false;
                        $scope.events.data[i].attendees--;
                    }
                });
            }
        };

        // On peut participer/masquer un événement via l'url
        var query = $location.search();
        if (query.action) {
            if (query.action == 'participer' && $scope.events.data[0].attend !== true) {
                $scope.attend($scope.events.data[0]);
            }
            if (query.action == 'masquer' && $scope.events.data[0].pookie !== true) {
                $scope.pookie($scope.events.data[0]);
            }
        }

        $scope.toggleAttendees = function(publication) {
            publication.displayAttendees = !publication.displayAttendees;

            if (publication.userlist === undefined) {
                $http.get(API_PREFIX + 'events/' + publication.slug + '/attendees').then(function(response) {
                    publication.userlist = response.data;
                });
            }
        };

        $scope.delete = function(post) {
            if (post.start_date) {
                const index = $scope.events.data.indexOf(post);

                // On demande confirmation
                alertify.confirm('Veux-tu vraiment supprimer cet évènement ?', function(e) {
                    if (e) {
                        $resource(API_PREFIX + 'events/' + $scope.events.data[index].slug).delete(function() {
                            $scope.events.data.splice(index, 1);
                        });
                    }
                });
            } else {
                const index = $scope.newsItems.data.indexOf(post);

                // On demande confirmation
                alertify.confirm('Veux-tu vraiment supprimer cette news ?', function(e) {
                    if (e) {
                        $resource(API_PREFIX + 'newsitems/' + $scope.newsItems.data[index].slug).delete(function() {
                            $scope.newsItems.data.splice(index, 1);
                        });
                    }
                });
            }
        };

        $scope.enableModify = function(post) {
            $scope.item = post.start_date !== undefined
                ? 'events'
                : 'newsitems';

            if ($scope.item === 'newsitems') {
                $scope.edit = post;
            } else {
                $rootScope.$broadcast('modifyEvent', post);
            }
        };

        $scope.modify = function(post) {
            $http.patch(API_PREFIX + $scope.item + '/' + post.slug, {text: post.text}).then(function() {
                alertify.success('Publication modifiée');
                $scope.edit.text = post.text;
                $scope.edit = null;
                $rootScope.$broadcast('newNewsitem');
            });
        };
    }
}

export default Publications_List_Ctrl;
