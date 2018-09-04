import alertify from 'alertifyjs';

import constants, { API_PREFIX } from 'upont/js/config/constants';

/* @ngInject */
class Students_Modify_Ctrl {
    constructor($scope, $rootScope, $resource, $http, preferences, user, clubs, clubsSuivis, token, devices, Achievements) {
        $scope.COUNTRIES = constants.COUNTRIES;
        $scope.DEPARTMENTS = constants.DEPARTMENTS;
        $scope.ORIGINS = constants.ORIGINS;
        $scope.PROMOS = constants.PROMOS;

        for (var i = 0; i < clubsSuivis.length; i++)
            clubsSuivis[i] = clubsSuivis[i].slug;

        for (var j = 0; j < clubs.length; j++)
            clubs[j].suivi = (clubsSuivis.indexOf(clubs[j].slug) >= 0);

        $scope.preferences = preferences;
        $scope.clubs = clubs;
        $scope.user = user;
        $scope.profilePicture = null;
        $scope.token = token.token;
        $scope.devices = devices;

        $scope.subscribe = function(slug) {
            $resource(API_PREFIX + 'clubs/:slug/follow', {slug: slug}).save();
            $scope.clubs.forEach(function (element){
                if(element.slug == slug)
                    element.suivi = true;
            });
        };

        $scope.unsubscribe = function(slug) {
            $resource(API_PREFIX + 'clubs/:slug/unfollow', {slug: slug}).save();
            $scope.clubs.forEach(function (element){
                if(element.slug == slug)
                    element.suivi = false;
            });
        };

        $scope.submitUser = function(usr, image) {
            var params = {
                firstName: usr.first_name,
                lastName: usr.last_name,
                promo: usr.promo,
                gender: usr.gender,
                nationality: usr.nationality,
                phone: usr.phone,
                location: usr.location,
                department: usr.department,
                origin: usr.origin,
                skype: usr.skype,
                nickname: usr.nick,
                statsFoyer: usr.stats_foyer,
                statsPonthub: usr.stats_ponthub,
                statsFacegame: usr.stats_facegame,
                mailEvent: usr.mail_event,
                mailModification: usr.mail_modification,
                mailShotgun: usr.mail_shotgun,
            };

            if (image) {
                params.image = image.base64;
            }

            $http.patch(API_PREFIX + 'users/' + user.username, params).then(function(){
                $resource(API_PREFIX + 'users/:slug', {slug: user.username}).get(function(data){
                    user = data;
                    Achievements.check();
                });
                alertify.success('Profil mis à jour !');
            });
        };

        $scope.submitAccount = function(me, old, password, confirm) {
            if (password === undefined || confirm === undefined || old === undefined) {
                alertify.error('Champs non remplis');
                return;
            }

            if (password != confirm) {
                alertify.error('Les deux mots de passe ne sont pas identiques');
                return;
            }

            var params = {
                old: old,
                password: password,
                confirm: confirm
            };

            $http.post(API_PREFIX + 'own/user', params)
                .then(function(){
                    $resource(API_PREFIX + 'users/:slug', {slug: $rootScope.username}).get(function(data){
                        $rootScope.me = data;
                        Achievements.check();
                    });
                    alertify.success('Compte mis à jour !');
                }, function(){
                    alertify.error('Ancien mot de passe incorrect');
                })
            ;
        };

        // Gère l'accordéon du tuto ICS
        $scope.openICS = -1;
        $scope.open = function(index) {
            $scope.openICS = $scope.openICS != index ? index : -1;
        };

        $scope.achievementCheck = function() {
            Achievements.check();
        };

        $scope.enableTour = function(e) {
            e.preventDefault();
            if (!$rootScope.me.tour)
                return;
            $http.patch(API_PREFIX + 'users/' + $rootScope.username, {tour: false}).then(function(){
                $rootScope.me.tour = false;
                $rootScope.$broadcast('tourEnabled');
                alertify.success('Tutoriel réactivé !');
            });
        };
    }
}

export default Students_Modify_Ctrl;
