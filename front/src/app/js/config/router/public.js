import { API_PREFIX } from 'upont/js/config/constants';

import template_403 from 'upont/js/controllers/public/errors/403.html';
import template_404 from 'upont/js/controllers/public/errors/404.html';
import template_418 from 'upont/js/controllers/public/errors/418.html';
import template_500 from 'upont/js/controllers/public/errors/500.html';
import controller_503 from 'upont/js/controllers/public/errors/503';
import template_503 from 'upont/js/controllers/public/errors/503.html';

import template_public_assos from 'upont/js/controllers/public/assos.html';
import Assos_Public_Ctrl from 'upont/js/controllers/public/assos';
import template_help from 'upont/js/controllers/public/help.html';
import Help_Ctrl from 'upont/js/controllers/public/help';
import template_login from 'upont/js/controllers/public/login.html';
import Login_Ctrl from 'upont/js/controllers/public/login';
import OAuth2Callback_Ctrl from 'upont/js/controllers/public/oauth2/callback';

export const PublicRouter = $stateProvider => {
    $stateProvider.state('root.403', {
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
                '$http',
                ($http) => $http.get(API_PREFIX + 'clubs').then(
                    (response) => response.data,
                    () => console.error('Failed to retrieve clubs'),
                )
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
    }).state('root.oauth2', {
        url: 'oauth2',
        abstract: true,
        template: '<div ui-view></div>'
    }).state('root.oauth2.callback', {
        url: '/callback',
        controller: OAuth2Callback_Ctrl,
        template: '<div></div>'
    });
};

export default PublicRouter;
