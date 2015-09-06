var url = 'https://upont.enpc.fr/api';
//var url = 'http://localhost/api';
moment.locale('fr');

var onsAlert = function(title, message) {
    ons.notification.alert({title: title, animation: 'default', buttonLabel: 'OK', message: message});
};

// Controlleur principal de l'app
var module = angular.module('upont', ['onsen','ngResource','infinite-scroll'])
    .factory('LoginInterceptor', ['StorageService', '$location', '$q', function (StorageService, $location, $q){
        return {
            request: function (config){
                config.headers = config.headers || {'Access-Control-Allow-Origin' : '*'};
                var token = StorageService.get('token');
                if(token) {
                    config.headers.Authorization = 'Bearer ' + token;
                }
                menu.setSwipeable(true);
                return config;
            },
            responseError: function (response) {
                // Erreur d'autentification
                if (response.status == 401) {
                    if(StorageService.get('token')){
                        StorageService.remove('token');
                        StorageService.remove('token_exp');
                    }
                    menu.setSwipeable(false);
                    menu.setMainPage('views/login.html', {closeMenu: true});
                    return $q.reject(response);
                }

                // Autres erreurs irrécupérables
                if(response.status == 404) {
                    onsAlert('Erreur', 'Ressource non trouvée.');
                }
                else if(response.status == 400) {
                    onsAlert('Erreur', 'Requête invalide.');
                } else if(response.status == 500) {
                    onsAlert('Erreur', 'Erreur interne du serveur !');
                } else if(response.status == 503) {
                    onsAlert('Maintenance', 'Le serveur est actuellement en maintenance. Veuillez réessayer dans quelques minutes');
                    menu.setSwipeable(false);
                    menu.setMainPage('views/error.html', {closeMenu: true});
                } else {
                    onsAlert('Erreur', 'Pas de connexion à Internet !');
                    menu.setSwipeable(false);
                    menu.setMainPage('views/error.html', {closeMenu: true});
                }

                return $q.reject(response);
            }
        };
    }])
    .run(['$rootScope', 'StorageService', function($rootScope, StorageService){
        $rootScope.registered = StorageService.get('registered');
    }])
    .config(['$httpProvider', '$resourceProvider', function($httpProvider, $resourceProvider) {
        $httpProvider.interceptors.push('LoginInterceptor');
        $resourceProvider.defaults.stripTrailingSlashes = false;
    }]);
