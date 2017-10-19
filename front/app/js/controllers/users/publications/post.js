angular.module('upont')
    .controller('Publications_Post_Ctrl', ['$scope', '$rootScope', '$http', '$stateParams', 'Achievements', 'Upload', function($scope, $rootScope, $http, $stateParams, Achievements, Upload) {
        // Fonctions relatives à la publication
        var clubDummy = {name: 'Au nom de...'};
        var club = clubDummy;
        $scope.display = true;
        $scope.isLoading = false;
        $scope.pub_info = {
            'Draft': {
                description: "Seul les membres du club ont accès aux brouillons dans la liste des publications du club.",
                action: "Créer le brouillon",
                label: 'Brouillon',
                number: 1,
            },
            'Scheduled': {
                description: "Cette publication apparaîtra seulement sur le calendrier uPont.",
                action: "Planifier",
                label: "Planification",
                number: 2,
            },
            'Published': {
                description: "Cette publication sera publique sur uPont, vous pourrez envoyer un mail plus tard.",
                action: "Publier",
                label: "Publication",
                number: 3
            },
            'Emailed': {
                description: "La publication sera publiée et envoyée par mail à tous les utilisateurs de uPont qui suivent le club.",
                action: "Envoyer par mail",
                label: "Email",
                number: 4
            }
        };

        // Si on est sur une page d'assos
        if ($stateParams.slug !== null && $stateParams.slug !== undefined) {
            // Par défaut on n'affiche pas le module
            $scope.display = false;
            for (var key in $rootScope.clubs) {
                // Si on appartient au club, on affiche avec le club préséléctionné
                if ($rootScope.clubs[key].club !== undefined && $rootScope.clubs[key].club.slug == $stateParams.slug) {
                    club = $rootScope.clubs[key].club;
                    $scope.display = true;
                }
            }
        }

        // Si l'utilisateur est un exterieur de l'administration
        if ($rootScope.hasRight('ROLE_EXTERIEUR'))
            club = $rootScope.clubs[0].club;

        var init = function() {
            $scope.focus = false;
            $scope.post = {
                entry_method: 'Entrée libre',
                publication_state: 'Published',
                text: '',
                start_date: '',
                end_date: '',
                shotgun_date: ''
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

        $scope.changeType = function(type) {
            $scope.type = type;

            switch (type) {
                case 'news':
                    if ($rootScope.hasRight('ROLE_EXTERIEUR'))
                        $scope.placeholder = 'Texte de la news';
                    else
                        $scope.placeholder = 'Que se passe-t-il d\'intéressant dans ton asso ?';
                    if ($scope.post.publication_state == 'Scheduled') {
                        $scope.post.publication_state = 'Published';
                    }
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


        $scope.selectFiles = function (files) {
            $scope.postFiles = files;
        };

        $scope.publish = function(post, files) {
            var params  = {
                text: nl2br(post.text),
                name: post.name,
                publicationState: post.publication_state
            };

            if (!$scope.modify) {
                if ($scope.club != clubDummy) {
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
                    if(!$scope.isLoading) {
                        $scope.isLoading = true;

                        if (!$scope.modify) {
                            // On demande si on envoie un mail
                            if(params.publicationState == 'Published') {
                                alertify.confirm(
                                    'Veux-tu envoyer un mail pour cette news ? Si non, tu pourras l\'envoyer ultérieurement.',
                                    function (e) {
                                        if (e) {
                                            params.publicationState = $scope.pub_info['Emailed'].number;
                                        }
                                    }
                                );
                            }

                            Upload.upload({
                                    method: "POST",
                                    url: apiPrefix + 'newsitems',
                                    data: params
                                })
                                .then(function() {
                                    $scope.$emit('newNewsitem');
                                    Achievements.check();
                                    alertify.success('News publiée');
                                    init();
                                    $scope.isLoading = false;
                                }, function() {
                                    alertify.error('Formulaire vide ou mal rempli');
                                    $scope.isLoading = false;
                            });
                        } else {
                            $http.patch(apiPrefix + 'newsitems/' + post.slug, params).then(function(){
                                $scope.$emit('modifiedNewsitem');
                                alertify.success('Publication modifiée');
                                init();
                                $scope.isLoading = false;
                            }, function() {
                                alertify.error('Formulaire vide ou mal rempli');
                                $scope.isLoading = false;
                            });
                        }
                    }
                    break;
                case 'event':
                    params.place = post.place;
                    params.entryMethod = post.entry_method;
                    params.startDate = moment(post.start_date).unix();
                    params.endDate = moment(post.end_date).unix();

                    if (!post.start_date || !post.end_date) {
                        alertify.error('Il faut préciser une date de début et de fin');
                        return;
                    }

                    if (params.startDate >= params.endDate) {
                        alertify.error('La date de début doit être avant la date de fin');
                        return;
                    }

                    if (post.entry_method == 'Shotgun') {
                        params.shotgunDate = moment(post.shotgun_date).unix();
                        params.shotgunLimit = post.shotgun_limit;
                        params.shotgunText = post.shotgun_text;

                        if (!post.shotgun_date) {
                            alertify.error('Il faut préciser une date de shotgun');
                            return;
                        }
                        if (params.shotgunDate >= params.startDate) {
                            alertify.error('La date de shotgun doit être avant la date de début');
                            return;
                        }
                    }

                    if(!$scope.isLoading) {
                        $scope.isLoading = true;

                        if (!$scope.modify) {

                            // On demande si on envoie un mail
                            if(params.publicationState == 'Published') {
                                alertify.confirm(
                                    'Veux-tu envoyer un mail pour cet événement ? Si non, tu pourras l\'envoyer ultérieurement.',
                                    function(e) {
                                        if(e) {
                                            params.publicationState = $scope.pub_info['Emailed'].number;
                                        }
                                    }
                                );
                            }

                            Upload.upload({
                                method: "POST",
                                url: apiPrefix + 'events',
                                data: params
                            }).then(function() {
                                $scope.$emit('newEvent');
                                Achievements.check();
                                init();
                                alertify.success('Événement publié');
                                $scope.isLoading = false;
                            }, function() {
                                alertify.error('Formulaire vide ou mal rempli');
                                $scope.isLoading = false;
                            });
                        } else {
                            $http.patch(apiPrefix + 'events/' + post.slug, params)
                            .then(function() {
                                $scope.$emit('modifiedEvent');
                                alertify.success('Événement modifié');
                                init();
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

        $scope.$on('modifyNewsitem', function(event, post) {
            $scope.modify = true;
            $scope.changeType('news');
            $scope.post = post;
        });

        $scope.$on('modifyEvent', function(event, post) {
            $scope.modify = true;
            $scope.changeType('event');

            if (!post.dateModified) {
                // Fix date to javascript timestamp
                post.start_date = moment.unix(post.start_date).toDate();
                post.end_date = moment.unix(post.end_date).toDate();
                if (post.shotgun_date) {
                    post.shotgun_date = moment.unix(post.shotgun_date).toDate();
                }

                post.dateModified = true;
            }

            $scope.post = post;
            $scope.initialEntryMethod = post.entry_method;
            $scope.initialPubNumber = $scope.pub_info[post.publication_state].number;
        });
    }]);
