import {API_PREFIX} from 'upont/js/config/constants';

import template_publications from 'upont/js/controllers/users/publications/index.html';

import template_publications_list from 'upont/js/controllers/users/publications/list.html';
import Publications_List_Ctrl from 'upont/js/controllers/users/publications/list';
import template_publications_post from 'upont/js/controllers/users/publications/post.html';
import Publications_Post_Ctrl from 'upont/js/controllers/users/publications/post';
import template_publications_calendar from 'upont/js/controllers/users/publications/calendar.html';
import Publications_Calendar_Ctrl from 'upont/js/controllers/users/publications/calendar';

import template_publications_shotgun from 'upont/js/controllers/users/publications/shotgun.html';
import Publications_Shotgun_Ctrl from 'upont/js/controllers/users/publications/shotgun';

export const UsersPublicationsRouter = $stateProvider => {
    $stateProvider.state('root.users.publications', {
        url: '',
        template: '<div ui-view></div>',
        abstract: true,
        data: {
            title: 'Accueil - uPont',
            top: true
        }
    }).state('root.users.publications.list', {
        url: '',
        data: {
            title: 'Accueil - uPont',
            top: true
        },
        resolve: {
            newsItems: [
                'Paginate',
                'AuthService',
                function(Paginate, AuthService) {
                    // Si c'est l'administration on ne charge que le seul club de l'user actuel
                    if (!AuthService.getUser().isStudent) {
                        return Paginate.get('clubs/' + AuthService.getUsername() + '/newsitems', {
                            sort: '-date',
                            limit: 10
                        });
                    }
                    return Paginate.get('own/newsitems', {
                        sort: '-date',
                        limit: 10
                    });
                }
            ],
            events: [
                'Paginate',
                'AuthService',
                function(Paginate, AuthService) {
                    // Si c'est l'administration on ne charge que le seul club de l'user actuel
                    if (!AuthService.getUser().isStudent) {
                        return Paginate.get('clubs/' + AuthService.getUsername() + '/events', {
                            sort: '-date',
                            limit: 10
                        });
                    }
                    return Paginate.get('own/events', {
                        sort: '-date',
                        limit: 10
                    });
                }
            ],
            calendar: [
                '$http', 'calendarConfig',
                ($http, calendarConfig) => Publications_Calendar_Ctrl.getCalendar($http, calendarConfig, 'day')
            ],
        },
        views: {
            '': {
                templateUrl: template_publications
            },
            'post@root.users.publications.list': {
                templateUrl: template_publications_post,
                controller: Publications_Post_Ctrl,
            },
            'list@root.users.publications.list': {
                templateUrl: template_publications_list,
                controller: Publications_List_Ctrl,
            },
            'calendar@root.users.publications.list': {
                templateUrl: template_publications_calendar,
                controller: Publications_Calendar_Ctrl,
            },
            // 'administration@root.users.publications.list': {
            //     templateUrl: template_tour,
            //     controller: Tour_Ctrl
            // }
        }
    }).state('root.users.publications.simple', {
        url: '/:slug',
        templateUrl: template_publications_list,
        controller: Publications_List_Ctrl,
        data: {
            title: 'Publication - uPont',
            top: true
        },
        resolve: {
            newsItems: [
                'Paginate',
                '$transition$',
                function(Paginate, $transition$) {
                    return Paginate.get('newsitems', {slug: $transition$.params().slug});
                }
            ],
            events: [
                'Paginate',
                '$transition$',
                function(Paginate, $transition$) {
                    return Paginate.get('events', {slug: $transition$.params().slug});
                }
            ],
            courseItems: function() {
                return [];
            }
        }
    }).state('root.users.publications.shotgun', {
        url: 'shotgun/:slug',
        templateUrl: template_publications_shotgun,
        controller: Publications_Shotgun_Ctrl,
        data: {
            top: true
        },
        resolve: {
            event: ['$resource', '$transition$', function($resource, $transition$) {
                return $resource(API_PREFIX + 'events/:slug').get({
                    slug: $transition$.params().slug
                }).$promise;
            }],
            shotgun: ['$resource', '$transition$', function($resource, $transition$) {
                return $resource(API_PREFIX + 'events/:slug/shotgun').get({
                    slug: $transition$.params().slug
                }).$promise;
            }]
        }
    });
};

export default UsersPublicationsRouter;
