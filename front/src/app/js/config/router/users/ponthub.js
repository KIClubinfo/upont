import {API_PREFIX} from 'upont/js/config/constants';

import template_ponthub from 'upont/js/controllers/users/ponthub/index.html';

import template_ponthub_list from 'upont/js/controllers/users/ponthub/list.html';
import Ponthub_List_Ctrl from 'upont/js/controllers/users/ponthub/list';
import template_ponthub_simple from 'upont/js/controllers/users/ponthub/simple.html';
import Ponthub_Simple_Ctrl from 'upont/js/controllers/users/ponthub/simple';
import template_ponthub_modify from 'upont/js/controllers/users/ponthub/modify.html';
import Ponthub_Modify_Ctrl from 'upont/js/controllers/users/ponthub/modify';

import template_ponthub_requests from 'upont/js/controllers/users/ponthub/requests.html';
import Ponthub_Requests_Ctrl from 'upont/js/controllers/users/ponthub/requests';

import template_ponthub_statistics from 'upont/js/controllers/users/ponthub/statistics.html';
import Ponthub_Statistics_Ctrl from 'upont/js/controllers/users/ponthub/statistics';

export const UsersPonthubRouter = $stateProvider => {
    $stateProvider.state('root.users.ponthub', {
        url: 'ponthub',
        templateUrl: template_ponthub,
        abstract: true,
        data: {
            title: 'PontHub - uPont',
            top: true
        },
        params: {
            category: 'films'
        }
    })
    // Ce state a besoin d'être enregistré avant le suivant afin que venant de l'exterieur, l'URL "statistiques" ne soit pas interpreté comme une catégorie.
        .state('root.users.ponthub.statistics', {
            url: '/statistiques',
            templateUrl: template_ponthub_statistics,
            controller: Ponthub_Statistics_Ctrl,
            data: {
                top: true
            },
            resolve: {
                ponthub: ['$resource', function($resource) {
                    return $resource(API_PREFIX + 'statistics/ponthub').get().$promise;
                }]
            }
        })
        .state('root.users.ponthub.requests', {
            url: '/demandes',
            templateUrl: template_ponthub_requests,
            controller: Ponthub_Requests_Ctrl,
            resolve: {
                requests: [
                    'Paginate',
                    (Paginate) => Paginate.get('requests')
                ]
            }
        })
        .state('root.users.ponthub.category', {
            url: '/:category',
            template: '<div ui-view></div>',
            abstract: true,
            params: {
                category: 'films'
            }
        })
        // Idem, le state simple doit être enregistré avant le state de list
        .state('root.users.ponthub.category.simple', {
            url: '/:slug',
            templateUrl: template_ponthub_simple,
            controller: Ponthub_Simple_Ctrl,
            data: {
                top: true
            },
            resolve: {
                element: ['$resource', '$stateParams', 'Ponthub', function($resource, $stateParams, Ponthub) {
                    return $resource(API_PREFIX + ':cat/:slug').get({
                        cat: Ponthub.cat($stateParams.category),
                        slug: $stateParams.slug
                    }).$promise;
                }],
                episodes: ['$resource', '$stateParams', 'Ponthub', function($resource, $stateParams, Ponthub) {
                    if (Ponthub.cat($stateParams.category) != 'series')
                        return false;
                    return $resource(API_PREFIX + ':cat/:slug/episodes').query({
                        cat: 'series',
                        slug: $stateParams.slug
                    }).$promise;
                }],
            }
        })
        .state('root.users.ponthub.category.modify', {
            url: '/:slug/rangement',
            templateUrl: template_ponthub_modify,
            controller: Ponthub_Modify_Ctrl,
            data: {
                top: true
            },
            resolve: {
                element: ['$resource', '$stateParams', 'Ponthub', function($resource, $stateParams, Ponthub) {
                    return $resource(API_PREFIX + ':cat/:slug').get({
                        cat: Ponthub.cat($stateParams.category),
                        slug: $stateParams.slug
                    }).$promise;
                }]
            }
        })
        .state('root.users.ponthub.category.list', {
            url: '',
            templateUrl: template_ponthub_list,
            controller: Ponthub_List_Ctrl,
            resolve: {
                elements: ['Paginate', '$stateParams', 'Ponthub', function(Paginate, $stateParams, Ponthub) {
                    return Paginate.get(Ponthub.cat($stateParams.category) + '?sort=-added,id', 20);
                }]
            },
        });
};

export default UsersPonthubRouter;
