import { API_PREFIX } from 'upont/js/config/constants';

// Layout
import template_container from 'upont/js/controllers/users/container.html';
import template_top_bar from 'upont/js/controllers/users/top-bar.html';

import template_aside from 'upont/js/controllers/users/aside.html';
import Aside_Ctrl from 'upont/js/controllers/users/aside';

import template_tour from 'upont/js/controllers/users/tour.html';
import Tour_Ctrl from 'upont/js/controllers/users/tour';

// Unclassified
import template_calendar from 'upont/js/controllers/users/calendar.html';
import Calendar_Ctrl from 'upont/js/controllers/users/calendar';

import template_dashboard from 'upont/js/controllers/users/dashboard.html';

import template_sso from 'upont/js/controllers/users/sso.html';
import SingleSignOn_Ctrl from 'upont/js/controllers/users/sso';

import UsersAdminRouter from './admin';
import UsersAssosRouter from './assos';
import UsersPonthubRouter from './ponthub';
import UsersPublicationsRouter from './publications';
import UsersResourcesRouter from './resources';
import UsersStudentsRouter from './students';

export const UsersRouter = $stateProvider => {
    $stateProvider.state('root.users', {
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
            courseItems: [
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
    });

    UsersAdminRouter($stateProvider);
    UsersAssosRouter($stateProvider);
    UsersPonthubRouter($stateProvider);
    UsersPublicationsRouter($stateProvider);
    UsersResourcesRouter($stateProvider);
    UsersStudentsRouter($stateProvider);
};

export default UsersRouter;
