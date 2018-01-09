import { API_PREFIX } from 'upont/js/config/constants';

import template_assos from 'upont/js/controllers/users/assos/index.html';

import template_assos_list from 'upont/js/controllers/users/assos/list.html';
import Assos_List_Ctrl from 'upont/js/controllers/users/assos/list';
import template_assos_simple from 'upont/js/controllers/users/assos/simple.html';
import Assos_Simple_Ctrl from 'upont/js/controllers/users/assos/simple';
import template_assos_modify from 'upont/js/controllers/users/assos/modify.html';
import Assos_Modify_Ctrl from 'upont/js/controllers/users/assos/modify';

import template_assos_presentation from 'upont/js/controllers/users/assos/presentation.html';
import Assos_Presentation_Ctrl from 'upont/js/controllers/users/assos/presentation';
import template_assos_publications from 'upont/js/controllers/users/assos/publications.html';
import Assos_Publications_Ctrl from 'upont/js/controllers/users/assos/publications';

import template_assos_foyer_playlist from 'upont/js/controllers/users/assos/foyer-playlist.html';
import Assos_FoyerPlaylist_Ctrl from 'upont/js/controllers/users/assos/foyer-playlist';
import template_assos_ki from 'upont/js/controllers/users/assos/ki.html';
import Assos_KI_Ctrl from 'upont/js/controllers/users/assos/ki';

export const UsersAssosRouter = $stateProvider => {
    $stateProvider.state('root.users.assos', {
        url: 'assos',
        abstract: true,
        templateUrl: template_assos,
        data: {
            title: 'Clubs & Assos - uPont'
        }
    }).state('root.users.assos.foyer-playlist', {
        url: '/c-est-ton-foyer',
        templateUrl: template_assos_foyer_playlist,
        controller: Assos_FoyerPlaylist_Ctrl,
        data: {
            title: 'Playlist foyer - uPont',
            top: true
        },
        resolve: {
            youtube: ['Paginate', function(Paginate) {
                return Paginate.get('youtubes?sort=-date', 20);
            }],
            stats: ['$resource', function($resource) {
                return $resource(API_PREFIX + 'statistics/foyer').get().$promise;
            }]
        }
    }).state('root.users.assos.ki', {
        url: '/depannage',
        templateUrl: template_assos_ki,
        controller: Assos_KI_Ctrl,
        data: {
            title: 'Dépannage - uPont',
            top: true
        },
        resolve: {
            fixs: ['Paginate', function(Paginate) {
                return Paginate.get('fixs', 50);
            }],
            ownFixs: ['Paginate', function(Paginate) {
                return Paginate.get('own/fixs', 50);
            }]
        }
    }).state('root.users.assos.list', {
        url: '',
        templateUrl: template_assos_list,
        controller: Assos_List_Ctrl,
        resolve: {
            clubs: ['$resource', function($resource) {
                return $resource(API_PREFIX + 'clubs?sort=fullName').query().$promise;
            }]
        },
        data: {
            top: true
        }
    }).state('root.users.assos.simple', {
        url: '/:slug',
        abstract: true,
        controller: Assos_Simple_Ctrl,
        templateUrl: template_assos_simple,
        resolve: {
            club: ['$resource', '$stateParams', function($resource, $stateParams) {
                return $resource(API_PREFIX + 'clubs/:slug').get({
                    slug: $stateParams.slug
                }).$promise;
            }],
            members: ['$resource', '$stateParams', function($resource, $stateParams) {
                return $resource(API_PREFIX + 'clubs/:slug/users').query({
                    slug: $stateParams.slug
                }).$promise;
            }]
        }
    }).state('root.users.assos.simple.modify', {
        url: '/gestion',
        controller: Assos_Modify_Ctrl,
        templateUrl: template_assos_modify,
        data: {
            title: 'Gestion - uPont',
            top: true
        },
    }).state('root.users.assos.simple.presentation', {
        url: '/presentation',
        templateUrl: template_assos_presentation,
        controller : Assos_Presentation_Ctrl,
        data: {
            title: 'Présentation - uPont',
            top: true
        }
    }).state('root.users.assos.simple.publications', {
        url: '',
        templateUrl: template_assos_publications,
        controller: Assos_Publications_Ctrl,
        data: {
            title: 'Activités - uPont',
            top: true
        },
        resolve: {
            events: ['$stateParams', 'Paginate', function($stateParams, Paginate) {
                return Paginate.get('clubs/' + $stateParams.slug + '/events?sort=-date', 10);
            }],
            newsItems: ['$stateParams', 'Paginate', function($stateParams, Paginate) {
                return Paginate.get('clubs/' + $stateParams.slug + '/newsitems?sort=-date', 10);
            }],
        }
    });
};

export default UsersAssosRouter;
