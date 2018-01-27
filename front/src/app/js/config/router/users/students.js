import {API_PREFIX} from 'upont/js/config/constants';

import template_students from 'upont/js/controllers/users/students/index.html';

import template_students_list from 'upont/js/controllers/users/students/list.html';
import Students_List_Ctrl from 'upont/js/controllers/users/students/list';
import template_students_simple from 'upont/js/controllers/users/students/simple.html';
import Students_Simple_Ctrl from 'upont/js/controllers/users/students/simple';
import template_students_modify from 'upont/js/controllers/users/students/modify.html';
import Students_Modify_Ctrl from 'upont/js/controllers/users/students/modify';

import template_students_game from 'upont/js/controllers/users/students/game.html';
import Students_Game_Ctrl from 'upont/js/controllers/users/students/game';

import template_students_pontlyvalent from 'upont/js/controllers/users/students/pontlyvalent.html';
import Students_Pontlyvalent_Ctrl from 'upont/js/controllers/users/students/pontlyvalent';

export const UsersStudentsRouter = $stateProvider => {
    $stateProvider.state('root.users.students', {
        url: 'eleves',
        templateUrl: template_students,
        abstract: true,
        data: {
            title: 'Élèves - uPont',
            top: true
        }
    }).state('root.users.students.game', {
        // Ces deux states ont besoin d'être enregistrés avant les suivants afin que l'URL "reponse-d" ne soit
        // pas interpretée comme un élève
        url: '/reponse-d',
        templateUrl: template_students_game,
        controller: Students_Game_Ctrl,
        data: {
            title: 'Jeu - uPont',
            top: true
        },
        resolve: {
            globalStatistics: [
                '$http',
                ($http) => $http.get(API_PREFIX + 'statistics/facegame').then(
                    (response) => response.data
                )
            ]
        }
    }).state('root.users.students.pontlyvalent', {
        url: '/pontlyvalent',
        templateUrl: template_students_pontlyvalent,
        controller: Students_Pontlyvalent_Ctrl,
        resolve: {
            comments: [
                'Paginate',
                (Paginate) => Paginate.get('users/pontlyvalent')
            ]
        },
        data: {
            title: 'Pontlyvalent - uPont',
            top: true
        }
    }).state('root.users.students.list', {
        url: '',
        templateUrl: template_students_list,
        controller: Students_List_Ctrl,
        resolve: {
            users: [
                'Paginate',
                (Paginate) => Paginate.get('users?sort=-promo,firstName,lastName', 20)
            ]
        },
        data: {
            top: true
        }
    }).state('root.users.students.simple', {
        url: '/:slug',
        templateUrl: template_students_simple,
        controller: Students_Simple_Ctrl,
        resolve: {
            user: [
                '$resource',
                '$stateParams',
                function($resource, $stateParams) {
                    return $resource(API_PREFIX + 'users/:slug').get({slug: $stateParams.slug}).$promise;
                }
            ],
            foyer: [
                '$resource',
                '$stateParams',
                function($resource, $stateParams) {
                    return $resource(API_PREFIX + 'statistics/foyer/:slug').get({slug: $stateParams.slug}).$promise;
                }
            ],
            ponthub: [
                '$resource',
                '$stateParams',
                function($resource, $stateParams) {
                    return $resource(API_PREFIX + 'statistics/ponthub/:slug').get({slug: $stateParams.slug}).$promise;
                }
            ],
            clubs: [
                '$resource',
                '$stateParams',
                function($resource, $stateParams) {
                    return $resource(API_PREFIX + 'users/:slug/clubs').query({slug: $stateParams.slug}).$promise;
                }
            ],
            achievements: [
                '$resource',
                '$stateParams',
                function($resource, $stateParams) {
                    return $resource(API_PREFIX + 'users/:slug/achievements?all').get({slug: $stateParams.slug}).$promise;
                }
            ]
        },
        data: {
            title: 'Profil - uPont',
            top: true
        }
    }).state('root.users.students.modify', {
        url: '/:slug/modifier',
        templateUrl: template_students_modify,
        controller: Students_Modify_Ctrl,
        resolve: {
            preferences: [
                '$resource',
                function($resource) {
                    return $resource(API_PREFIX + 'own/preferences').get().$promise;
                }
            ],
            token: [
                '$resource',
                function($resource) {
                    return $resource(API_PREFIX + 'own/token').get().$promise;
                }
            ],
            devices: [
                '$resource',
                function($resource) {
                    return $resource(API_PREFIX + 'own/devices').query().$promise;
                }
            ],
            user: [
                '$resource',
                '$stateParams',
                function($resource, $stateParams) {
                    return $resource(API_PREFIX + 'users/:slug').get({slug: $stateParams.slug}).$promise;
                }
            ],
            clubs: [
                '$http',
                ($http) => $http.get(API_PREFIX + 'clubs').then(
                    (response) => response.data
                )
            ],
            clubsSuivis: [
                '$resource',
                function($resource) {
                    return $resource(API_PREFIX + 'own/followed').query().$promise;
                }
            ]
        },
        data: {
            title: 'Profil - uPont',
            top: true
        }
    });
};

export default UsersStudentsRouter;
