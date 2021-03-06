import alertify from 'alertifyjs';
import * as moment from 'moment';

import { API_PREFIX } from 'upont/js/config/constants';

import { nl2br } from 'upont/js/php';

/* @ngInject */
class Publications_Post_Ctrl {
    constructor($scope, $rootScope, $http, $stateParams, Achievements, Upload) {
        // Fonctions relatives à la publication
        const clubDummy = {
            name: 'Au nom de...',
        };
        let club = clubDummy;
        $scope.display = true;
        $scope.isLoading = false;

        // Si on est sur une page d'assos
        if ($stateParams.slug !== null && $stateParams.slug !== undefined) {
            // Par défaut on n'affiche pas le module
            $scope.display = false;
            for (const key in $rootScope.clubs) {
                // Si on appartient au club, on affiche avec le club préséléctionné
                if ($rootScope.clubs[key].club !== undefined && $rootScope.clubs[key].club.slug === $stateParams.slug) {
                    club = $rootScope.clubs[key].club;
                    $scope.display = true;
                }
            }
        }

        // Si l'utilisateur est un exterieur de l'administration
        if ($rootScope.hasRight('ROLE_EXTERIEUR'))
            club = $rootScope.clubs[0].club;

        const init = function() {
            $scope.focus = false;
            $scope.post = {
                entry_method: 'Entrée libre',
                text: '',
                start_date: '',
                end_date: '',
                shotgun_date: '',
            };
            $scope.type = 'news';
            $scope.club = club;
            $scope.toggle = false;

            if ($rootScope.hasRight('ROLE_EXTERIEUR'))
                $scope.placeholder = 'Texte de la news';
            else
                $scope.placeholder = 'Que se passe-t-il d\'intéressant dans ton asso ?';

            $scope.postFiles = {};
        };
        init();

        const postNews = (params) => {
            Upload.upload({
                method: 'POST',
                url: API_PREFIX + 'newsitems',
                data: params,
            }).then(() => {
                $rootScope.$broadcast('newNewsitem');
                Achievements.check();
                alertify.success('News publiée');
                init();
                $scope.isLoading = false;
            }, () => {
                alertify.error('Formulaire vide ou mal rempli');
                $scope.isLoading = false;
            });
        };

        const postEvent = (params) => {
            Upload.upload({
                method: 'POST',
                url: API_PREFIX + 'events',
                data: params,
            }).then(function() {
                $rootScope.$broadcast('newEvent');
                Achievements.check();
                init();
                alertify.success('Événement publié');
                $scope.isLoading = false;
            }, function() {
                alertify.error('Formulaire vide ou mal rempli');
                $scope.isLoading = false;
            });
        };

        $scope.changeType = function(type) {
            $scope.type = type;

            switch (type) {
            case 'news':
                if ($rootScope.hasRight('ROLE_EXTERIEUR'))
                    $scope.placeholder = 'Texte de la news';
                else
                    $scope.placeholder = 'Que se passe-t-il d\'intéressant dans ton asso ?';
                break;
            case 'event':
                $scope.placeholder = 'Description de l\'événement';
                break;
            }
        };

        $scope.toggleSelect = function() {
            $scope.toggle = !$scope.toggle;
        };

        $scope.changeClub = function(club) {
            $scope.club = club;
            $scope.toggle = false;
        };

        $scope.selectFiles = function(files) {
            $scope.postFiles = files;
        };

        $scope.publish = function(post) {
            const params = {
                text: nl2br(post.text),
                name: post.name,
            };

            if (!$scope.modify) {
                if ($scope.club !== clubDummy) {
                    params.authorClub = $scope.club.slug;
                } else {
                    if ($rootScope.hasRight('ROLE_EXTERIEUR')) {
                        params.authorClub = $rootScope.clubs[0].club.slug;
                    } else {
                        alertify.error('Tu n\'as pas choisi avec quelle assos publier');
                        return;
                    }
                }
            }

            if ($scope.postFiles && !$scope.modify) {
                params.uploadedFiles = $scope.postFiles;
            }

            switch ($scope.type) {
            case 'news':
                if (!$scope.isLoading) {
                    $scope.isLoading = true;

                    // On demande si on envoie un mail
                    alertify.confirm(
                        'Veux-tu envoyer un mail pour cette news ?',
                        () => {
                            params.sendMail = true;
                            postNews(params);
                        },
                        () => {
                            params.sendMail = false;
                            postNews(params);
                        },
                    ).set('labels', {
                        ok: 'Oui',
                        cancel: 'Non',
                    });
                }
                break;
            case 'event':
                params.place = post.place;
                params.entryMethod = post.entry_method;
                params.startDate = post.start_date.seconds(0).toISOString();
                params.endDate = post.end_date.seconds(0).toISOString();

                if (!post.start_date || !post.end_date) {
                    alertify.error('Il faut préciser une date de début et de fin');
                    return;
                }

                if (post.start_date.isAfter(post.end_date)) {
                    alertify.error('La date de début doit être avant la date de fin');
                    return;
                }

                if (post.entry_method === 'Shotgun') {
                    params.shotgunDate = post.shotgun_date.seconds(0).toISOString();
                    params.shotgunLimit = post.shotgun_limit;
                    params.shotgunText = post.shotgun_text;

                    if (!post.shotgun_date) {
                        alertify.error('Il faut préciser une date de shotgun');
                        return;
                    }
                    if (post.shotgun_date.isAfter(post.start_date)) {
                        alertify.error('La date de shotgun doit être avant la date de début');
                        return;
                    }
                }

                if (!$scope.isLoading) {
                    $scope.isLoading = true;

                    if (!$scope.modify) {

                        // On demande si on envoie un mail
                        alertify.confirm(
                            'Veux-tu envoyer un mail pour cet événement ?',
                            () => {
                                params.sendMail = true;
                                postEvent(params);
                            },
                            () => {
                                params.sendMail = false;
                                postEvent(params);
                            },
                        ).set('labels', {
                            ok: 'Oui',
                            cancel: 'Non',
                        });
                    } else {
                        $http.patch(API_PREFIX + 'events/' + post.slug, params).then(function() {
                            $rootScope.$broadcast('newEvent');
                            alertify.success('Événement modifié');
                            init();
                            $scope.modify = false;
                            $scope.isLoading = false;
                        }, function() {
                            alertify.error('Formulaire vide ou mal rempli');
                            $scope.isLoading = false;
                        });
                    }
                }
                break;
            default:
                alertify.error('Type de publication non encore pris en charge');
            }
        };

        $scope.modify = false;
        $scope.$on('modifyEvent', (event, post) => {
            $scope.modify = true;
            $scope.changeType('event');
            $rootScope.$broadcast('newEvent');

            $scope.post = post;
            $scope.post.start_date = moment($scope.post.start_date);
            $scope.post.end_date = moment($scope.post.end_date);
            $scope.post.shotgun_date = moment($scope.post.shotgun_date);
            window.scrollTo(0, 0);
        });
    }
}

export default Publications_Post_Ctrl;
