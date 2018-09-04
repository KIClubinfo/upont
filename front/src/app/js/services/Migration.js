import alertify from 'alertifyjs';
import angular from 'angular';
import {API_PREFIX} from "upont/js/config/constants";

// Service effectuant des opérations de mise à jour au fur et à mesure des versions
angular.module('upont').factory('Migration', ['$http', '$rootScope', function($http, $rootScope) {
    return {
        // Ajout des statistiques Foyer et PontHub
        v211: function(user) {
            if (user.stats_foyer === null || user.stats_foyer === undefined && !$rootScope.migration) {
                $rootScope.migration = true;
                alertify.confirm('<strong>Statistiques uPont</strong><br><br>' +
                    'uPont te permet de partager un peu ton activité aux Ponts aux travers de statistiques comme celles du Foyer ou de PontHub.<br>' +
                    'Laisse-les publiques et tes potes verront qu\'ils ont encore du chemin à faire avant de te rattraper !<br>' +
                    '(Ce réglage est toujours modifiable depuis la page de modification du profil)',
                    () => {
                        $http.patch(API_PREFIX + 'users/' + user.username, {statsFoyer: true, statsPonthub: true}).then(function(){
                            $rootScope.me.statsFoyer = true;
                            $rootScope.me.statsPonthub = true;
                            alertify.success('Super !');
                            $rootScope.migration = false;
                        });
                    },
                    () => {
                        $http.patch(API_PREFIX + 'users/' + user.username, {statsFoyer: false, statsPonthub: false}).then(function(){
                            $rootScope.me.statsFoyer = false;
                            $rootScope.me.statsPonthub = false;
                            alertify.success('Dommage :(');
                            $rootScope.migration = false;
                        });
                    },
                );
            }
        },
    };
}]);
