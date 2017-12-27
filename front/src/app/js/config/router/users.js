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

// Admin
import template_admin from 'upont/js/controllers/users/admin/index.html';

import template_admin_assos from 'upont/js/controllers/users/admin/assos.html';
import Admin_Assos_Ctrl from 'upont/js/controllers/users/admin/assos';
import template_admin_students from 'upont/js/controllers/users/admin/students.html';
import Admin_Students_Ctrl from 'upont/js/controllers/users/admin/students';

// Administration

// Assos

// Ponthub
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

// Publications
import template_publications from 'upont/js/controllers/users/publications/index.html';

import template_publications_list from 'upont/js/controllers/users/publications/list.html';
import Publications_List_Ctrl from 'upont/js/controllers/users/publications/list';
import template_publications_post from 'upont/js/controllers/users/publications/post.html';
import Publications_Post_Ctrl from 'upont/js/controllers/users/publications/post';

// Resources
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

// Students
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
    }).state('root.users.admin', {
        url: 'admin',
        templateUrl: template_admin,
        abstract: true,
        data: {
            title: 'Administration - uPont',
            top: true
        }
    }).state('root.users.admin.assos', {
        url: '/assos',
        templateUrl: template_admin_assos,
        controller: Admin_Assos_Ctrl,
        data: {
            title: 'Administration des assos - uPont',
            top: true
        }
    }).state('root.users.admin.students', {
        url: '/eleves',
        templateUrl: template_admin_students,
        controller: Admin_Students_Ctrl,
        data: {
            title: 'Administration des élèves - uPont',
            top: true
        }
    }).state('root.users.ponthub', {
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
            requests: ['$resource', '$stateParams', function($resource, $stateParams) {
                return $resource(API_PREFIX + 'requests').query().$promise;
            }]
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
                if(Ponthub.cat($stateParams.category) != 'series')
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
    }).state('root.users.publications', {
        url: '',
        template: '<div ui-view></div>',
        abstract: true,
        data: {
            title: 'Accueil - uPont',
            top: true
        }
    }).state('root.users.publications.index', {
        url: '',
        data: {
            title: 'Accueil - uPont',
            top: true
        },
        resolve: {
            newsItems: [
                'Paginate',
                'Permissions',
                '$rootScope',
                function(Paginate, Permissions, $rootScope) {
                    // Si c'est l'administration on ne charge que le seul club de l'user actuel
                    if (Permissions.hasRight('ROLE_EXTERIEUR'))
                        return Paginate.get('clubs/' + Permissions.username() + '/newsitems?sort=-date', 10);
                    return Paginate.get('own/newsitems?sort=-date', 10);
                }
            ],
            events: [
                'Paginate',
                'Permissions',
                '$rootScope',
                function(Paginate, Permissions, $rootScope) {
                    // Si c'est l'administration on ne charge que le seul club de l'user actuel
                    if (Permissions.hasRight('ROLE_EXTERIEUR'))
                        return Paginate.get('clubs/' + Permissions.username() + '/events?sort=-date', 10);
                    return Paginate.get('own/events', 10);
                }
            ],
            courseItems: [
                '$resource',
                function($resource) {
                    return $resource(API_PREFIX + 'own/courseitems').query().$promise;
                }
            ]
        },
        views: {
            '': {
                templateUrl: template_publications
            },
            'post@root.users.publications.index': {
                templateUrl: template_publications_post,
                controller: Publications_Post_Ctrl
            },
            'list@root.users.publications.index': {
                templateUrl: template_publications_list,
                controller: Publications_List_Ctrl
            },
            // 'administration@root.users.publications.index': {
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
                function(Paginate, $stateParams) {
                    return Paginate.get('newsitems?slug=' + $stateParams.slug);
                }
            ],
            events: [
                'Paginate',
                '$stateParams',
                function(Paginate, $stateParams) {
                    return Paginate.get('events?slug=' + $stateParams.slug);
                }
            ],
            messages: [
                'Paginate',
                '$stateParams',
                function(Paginate, $stateParams) {
                    return Paginate.get('newsitems?slug=' + $stateParams.slug);
                }
            ],
            courseItems: function($resource) {
                return [];
            }
        }
    }).state('root.users.resources', {
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
    })
    .state('root.users.resources.courses.list', {
        url: '',
        templateUrl: template_resources_courses_list,
        controller: Resources_Courses_List_Ctrl,
        data: {
            title: 'Liste des cours - uPont',
            top: true
        },
        resolve: {
            courses: ['Paginate', function(Paginate) {
                return Paginate.get('courses?sort=name', 50);
            }],
            followed: ['$resource', function($resource) {
                return $resource(API_PREFIX + 'own/courses').query().$promise;
            }]
        },
    }).state('root.users.resources.courses.simple', {
        url: '/:slug',
        templateUrl: template_resources_courses_simple,
        controller: Resources_Courses_Simple_Ctrl,
        data: {
            title: 'Cours - uPont',
            top: true
        },
        resolve: {
            course: ['$resource', '$stateParams', function($resource, $stateParams) {
                return $resource(API_PREFIX + 'courses/:slug').get({
                    slug: $stateParams.slug
                }).$promise;
            }],
            exercices: ['$resource', '$stateParams', function($resource, $stateParams) {
                return $resource(API_PREFIX + 'courses/:slug/exercices').query({
                    slug: $stateParams.slug
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
                '$resource',
                function($resource) {
                    return $resource(API_PREFIX + 'tutos').query().$promise;
                }
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
                '$stateParams',
                function($resource, $stateParams) {
                    return $resource(API_PREFIX + 'tutos/:slug').get({slug: $stateParams.slug}).$promise;
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
    }).state('root.users.students', {
        url: 'eleves',
        templateUrl: template_students,
        abstract: true,
        data: {
            title: 'Élèves - uPont',
            top: true
        }
    })
    // Ces deux states ont besoin d'être enregistrés avant les suivants afin que l'URL "reponse-d" ne soit
    // pas interpretée comme un élève
    .state('root.users.students.game', {
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
    }).state('root.users.students.pontlyvalent', {
        url: '/pontlyvalent',
        templateUrl: template_students_pontlyvalent,
        controller: Students_Pontlyvalent_Ctrl,
        resolve: {
            comments: [
                '$resource',
                function($resource) {
                    return $resource(API_PREFIX + 'users/pontlyvalent').query().$promise;
                }
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
                function(Paginate) {
                    return Paginate.get('users?sort=-promo,firstName,lastName', 20);
                }
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
                '$resource',
                function($resource) {
                    return $resource(API_PREFIX + 'clubs?sort=name').query().$promise;
                }
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

export default UsersRouter;
