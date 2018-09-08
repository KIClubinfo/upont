import {API_PREFIX} from 'upont/js/config/constants';

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
                return Paginate.get('youtubes', {
                    sort: '-date',
                    limit: 20
                });
            }],
            stats: ['$http', function($http) {
                return $http.get(API_PREFIX + 'statistics/foyer').then(
                    (response) => response.data,
                    () => console.error('Failed to retrieve foyer statistics')
                );
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
                return Paginate.get('fixs');
            }],
            ownFixs: ['Paginate', function(Paginate) {
                return Paginate.get('own/fixs');
            }]
        }
    }).state('root.users.assos.list', {
        url: '',
        templateUrl: template_assos_list,
        controller: Assos_List_Ctrl,
        resolve: {
            clubs: [
                '$http',
                ($http) => $http.get(API_PREFIX + 'clubs').then(
                    (response) => response.data,
                    () => console.error('Failed to retrieve clubs')
                )
            ]
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
            club: ['$http', '$stateParams',
                ($http, $stateParams) => {
                return $http.get(API_PREFIX + 'clubs/' + $stateParams.slug)
                .then(
                    (response) => response.data,
                    () => console.error('Failed to retrieve club')
                );
            }],
            members: ['$http', '$stateParams', function($http, $stateParams) {
                return $http.get(API_PREFIX + 'clubs/' + $stateParams.slug + '/users')
                .then(
                    (response) => response.data,
                    () => console.error('Failed to retrieve club members')
                );
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
        controller: Assos_Presentation_Ctrl,
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
                return Paginate.get('clubs/' + $stateParams.slug + '/events', {
                    sort: '-date',
                    limit: 10
                });
            }],
            newsItems: ['$stateParams', 'Paginate', function($stateParams, Paginate) {
                return Paginate.get('clubs/' + $stateParams.slug + '/newsitems', {
                    sort: '-date',
                    limit: 10
                });
            }],
        }
    });
};

export default UsersAssosRouter;
