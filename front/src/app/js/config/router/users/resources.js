import {API_PREFIX} from 'upont/js/config/constants';

import template_resources from 'upont/js/controllers/users/resources/index.html';

import template_resources_administration from 'upont/js/controllers/users/resources/administration.html';

import template_resources_courses_list from 'upont/js/controllers/users/resources/courses-list.html';
import Resources_Courses_List_Ctrl from 'upont/js/controllers/users/resources/courses-list';
import template_resources_courses_simple from 'upont/js/controllers/users/resources/courses-simple.html';
import Resources_Courses_Simple_Ctrl from 'upont/js/controllers/users/resources/courses-simple';

import template_resources_moderation from 'upont/js/controllers/users/resources/moderation.html';

import template_resources_tutorials_list from 'upont/js/controllers/users/resources/tutorials-list.html';
import Resources_Tutorials_List_Ctrl from 'upont/js/controllers/users/resources/tutorials-list';
import template_resources_tutorials_simple from 'upont/js/controllers/users/resources/tutorials-simple.html';
import Resources_Tutorials_Simple_Ctrl from 'upont/js/controllers/users/resources/tutorials-simple';

import template_resources_upont from 'upont/js/controllers/users/resources/upont.html';

export const UsersResourcesRouter = $stateProvider => {
    $stateProvider.state('root.users.resources', {
        url: 'ressources',
        templateUrl: template_resources,
        abstract: true,
        data: {
            title: 'Ressources - uPont',
            top: true
        }
    }).state('root.users.resources.administration', {
        url: '/administration',
        templateUrl: template_resources_administration,
        data: {
            title: 'Infos Administration - uPont',
            top: true
        },
    }).state('root.users.resources.courses', {
        url: '/cours',
        abstract: true,
        template: '<div ui-view></div>',
        data: {
            title: 'Cours - uPont',
            top: true
        },
    }).state('root.users.resources.courses.list', {
        url: '',
        templateUrl: template_resources_courses_list,
        controller: Resources_Courses_List_Ctrl,
        data: {
            title: 'Liste des cours - uPont',
            top: true
        },
        resolve: {
            courses: ['Paginate', function(Paginate) {
                return Paginate.get('courses', {
                    sort: 'name',
                    limit: 50
                });
            }],
            followed: ['$resource', function($resource) {
                return $resource(API_PREFIX + 'own/courses').query().$promise;
            }]
        },
    }).state('root.users.resources.courses.simple', {
        url: '/{slug}',
        templateUrl: template_resources_courses_simple,
        controller: Resources_Courses_Simple_Ctrl,
        data: {
            title: 'Cours - uPont',
            top: true
        },
        resolve: {
            course: ['$resource', '$transition$', function($resource, $transition$) {
                return $resource(API_PREFIX + 'courses/:slug').get({
                    slug: $transition$.params().slug
                }).$promise;
            }],
            exercices: ['$resource', '$transition$', function($resource, $transition$) {
                return $resource(API_PREFIX + 'courses/:slug/exercices').query({
                    slug: $transition$.params().slug
                }).$promise;
            }]
        }
    }).state('root.users.resources.moderation', {
        url: '/moderation',
        templateUrl: template_resources_moderation,
        data: {
            title: 'Règles de modération - uPont',
            top: true
        },
    }).state('root.users.resources.tutorials', {
        url: '/tutoriels',
        template: '<div ui-view></div>',
        abstract: true,
        data: {
            title: 'Tutoriels - uPont',
            top: true
        }
    }).state('root.users.resources.tutorials.list', {
        url: '',
        templateUrl: template_resources_tutorials_list,
        controller: Resources_Tutorials_List_Ctrl,
        data: {
            title: 'Tutoriels - uPont',
            top: true
        },
        resolve: {
            tutos: [
                'Paginate',
                (Paginate) => Paginate.get('tutos')
            ]
        }
    }).state('root.users.resources.tutorials.simple', {
        url: '/:slug',
        templateUrl: template_resources_tutorials_simple,
        controller: Resources_Tutorials_Simple_Ctrl,
        data: {
            title: 'Tutoriels - uPont',
            top: true
        },
        resolve: {
            tuto: [
                '$resource',
                '$transition$',
                function($resource, $transition$) {
                    return $resource(API_PREFIX + 'tutos/:slug').get({slug: $transition$.params().slug}).$promise;
                }
            ]
        }
    }).state('root.users.resources.upont', {
        url: '/upont',
        templateUrl: template_resources_upont,
        data: {
            title: 'uPont - uPont',
            top: true
        }
    });
};

export default UsersResourcesRouter;
