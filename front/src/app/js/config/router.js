import {API_PREFIX} from './constants';

import template_403 from '../controllers/public/errors/403.html';
import template_404 from '../controllers/public/errors/404.html';
import template_418 from '../controllers/public/errors/418.html';
import template_500 from '../controllers/public/errors/500.html';
import controller_503 from '../controllers/public/errors/503';
import template_503 from '../controllers/public/errors/503.html';

import template_public_assos from '../controllers/public/assos.html';
import Assos_Public_Ctrl from '../controllers/public/assos';
import template_help from '../controllers/public/help.html';
import Help_Ctrl from '../controllers/public/help';
import template_login from '../controllers/public/login.html';
import Login_Ctrl from '../controllers/public/login';
import template_request from '../controllers/public/request.html';
import Request_Ctrl from '../controllers/public/request';
import template_reset from '../controllers/public/reset.html';
import Reset_Ctrl from '../controllers/public/reset';

import template_container from '../controllers/users/container.html';
import template_top_bar from '../controllers/users/top-bar.html';
import template_aside from '../controllers/users/aside.html';
import Aside_Ctrl from '../controllers/users/aside';
import template_tour from '../controllers/users/tour.html';
import Tour_Ctrl from '../controllers/users/tour';

import template_calendar from '../controllers/users/calendar.html';
import Calendar_Ctrl from '../controllers/users/calendar';
import template_dashboard from '../controllers/users/dashboard.html';
import template_sso from '../controllers/users/sso.html';
import SingleSignOn_Ctrl from '../controllers/users/sso';

import template_students from '../controllers/users/students/index.html';
import template_students_list from '../controllers/users/students/list.html';
import Students_List_Ctrl from '../controllers/users/students/list';
import template_students_game from '../controllers/users/students/game.html';
import Students_Game_Ctrl from '../controllers/users/students/game';

export const Router = [
    '$stateProvider', $stateProvider => {
        $stateProvider.state('root', {
            abstract: true,
            url: '/',
            template: '<div ui-view></div>'
        }).state('root.403', {
            url: '403',
            templateUrl: template_403
        }).state('root.404', {
            url: '404',
            templateUrl: template_404
        }).state('root.418', {
            url: '418',
            templateUrl: template_418
        }).state('root.500', {
            url: '500',
            templateUrl: template_500
        }).state('root.maintenance', {
            url: 'maintenance',
            templateUrl: template_503,
            controller: controller_503
        }).state('root.public', {
            url: 'public',
            abstract: true,
            template: '<div ui-view></div>'
        }).state('root.public.assos', {
            url: '/assos',
            templateUrl: template_public_assos,
            controller: Assos_Public_Ctrl,
            data: {
                title: 'Clubs & Assos - uPont',
                top: true
            },
            resolve: {
                clubs: [
                    '$resource', ($resource) => {
                        return $resource(API_PREFIX + 'clubs?sort=name').query().$promise;
                    }
                ]
            }
        }).state('root.public.help', {
            url: '/help',
            templateUrl: template_help,
            controller: Help_Ctrl,
            data: {
                title: 'Aide - uPont',
                top: true
            }
        }).state('root.login', {
            url: '',
            templateUrl: template_login,
            controller: Login_Ctrl
        }).state('root.request', {
            url: 'mot-de-passe-oublie',
            templateUrl: template_request,
            controller: Request_Ctrl
        }).state('root.reset', {
            url: 'reset/:token',
            templateUrl: template_reset,
            controller: Reset_Ctrl
        }).state('root.users', {
            url: '',
            abstract: true,
            resolve: {
                user: [
                    '$http',
                    '$rootScope',
                    ($http, $rootScope) => {
                        return $http.get(API_PREFIX + 'own/user').then(function(response) {
                            $rootScope.me = response.data;
                            return response.data;
                        });
                    }
                ],
                userClubs: [
                    '$http',
                    '$rootScope',
                    ($http, $rootScope) => {
                        // On récupère les clubs de l'utilisateurs pour déterminer ses roles de publication
                        return $http.get(API_PREFIX + 'own/clubs').then(function(response) {
                            $rootScope.clubs = response.data;
                            return response.data;
                        });
                    }
                ]
            },
            data: {
                needLogin: true
            },
            views: {
                '': {
                    templateUrl: template_container
                },
                'topbar@root.users': {
                    templateUrl: template_top_bar
                },
                'aside@root.users': {
                    templateUrl: template_aside,
                    controller: Aside_Ctrl
                },
                'tour@root.users': {
                    templateUrl: template_tour,
                    controller: Tour_Ctrl
                }
            }
        }).state('root.users.calendar', {
            url: 'calendrier',
            templateUrl: template_calendar,
            controller: Calendar_Ctrl,
            data: {
                title: 'Calendrier - uPont'
            },
            resolve: {
                events: [
                    '$resource',
                    function($resource) {
                        return $resource(API_PREFIX + 'own/events').query().$promise;
                    }
                ],
                courseitems: [
                    '$resource',
                    function($resource) {
                        return $resource(API_PREFIX + 'own/courseitems').query().$promise;
                    }
                ]
            },
            onEnter: [
                '$rootScope',
                function($rootScope) {
                    $rootScope.hideFooter = true;
                }
            ],
            onExit: [
                '$rootScope',
                function($rootScope) {
                    $rootScope.hideFooter = false;
                }
            ]
        }).state('root.users.dashboard', {
            url: 'dashboard',
            templateUrl: template_dashboard,
            controller: Aside_Ctrl,
            data: {
                title: 'Tableau de bord - uPont',
                top: true
            }
        }).state('root.users.sso', {
            url: 'sso?appId&to',
            templateUrl: template_sso,
            controller: SingleSignOn_Ctrl,
            data: {
                title: 'Authentification centralisée - uPont',
                top: true
            }
        }).state('root.users.students', {
            url: 'eleves',
            templateUrl: template_students,
            abstract: true,
            data: {
                title: 'Élèves - uPont',
                top: true
            }
        }).state('root.users.students.list', {
            url: '',
            templateUrl: template_students_list,
            controller: Students_List_Ctrl,
            resolve: {
                users: [
                    'Paginate',
                    function(Paginate) {
                        return Paginate.get('users?sort=-promo,firstName,lastName', 20);
                    }
                ]
            },
            data: {
                top: true
            }
        }).state('root.users.students.game', {
            url: '/reponse-d',
            templateUrl: template_students_game,
            controller: Students_Game_Ctrl,
            data: {
                title: 'Jeu - uPont',
                top: true
            },
            resolve: {
                globalStatistics: [
                    '$resource',
                    function($resource) {
                        return $resource(API_PREFIX + 'statistics/facegame').get().$promise;
                    }
                ]
            }
        });
    }
];

export default Router;
