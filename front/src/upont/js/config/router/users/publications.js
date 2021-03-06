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
                'Permissions',
                (Paginate, Permissions) => {
                    // Si c'est l'administration on ne charge que le seul club de l'user actuel
                    if (Permissions.hasRight('ROLE_EXTERIEUR'))
                        return Paginate.get('clubs/' + Permissions.username() + '/newsitems', {
                            sort: '-date',
                            limit: 10
                        });
                    return Paginate.get('own/newsitems', {
                        sort: '-date',
                        limit: 10
                    });
                }
            ],
            events: [
                'Paginate',
                'Permissions',
                (Paginate, Permissions) => {
                    // Si c'est l'administration on ne charge que le seul club de l'user actuel
                    if (Permissions.hasRight('ROLE_EXTERIEUR'))
                        return Paginate.get('clubs/' + Permissions.username() + '/events', {
                            sort: '-date',
                            limit: 10
                        });
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
        url: 'publications/:slug',
        templateUrl: template_publications_list,
        controller: Publications_List_Ctrl,
        data: {
            title: 'Publication - uPont',
            top: true
        },
        resolve: {
            newsItems: [
                'Paginate',
                '$stateParams',
                (Paginate, $stateParams) => Paginate.get('newsitems', { slug: $stateParams.slug }),
            ],
            events: [
                'Paginate',
                '$stateParams',
                (Paginate, $stateParams) => Paginate.get('events', { slug: $stateParams.slug }),
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
            event: ['$resource', '$stateParams', function($resource, $stateParams) {
                return $resource(API_PREFIX + 'events/:slug').get({
                    slug: $stateParams.slug
                }).$promise;
            }],
            shotgun: ['$resource', '$stateParams', function($resource, $stateParams) {
                return $resource(API_PREFIX + 'events/:slug/shotgun').get({
                    slug: $stateParams.slug
                }).$promise;
            }]
        }
    });
};

export default UsersPublicationsRouter;
