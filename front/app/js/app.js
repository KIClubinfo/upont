var apiPrefix = "/api/";
if (!location.origin)
     location.origin = location.protocol + "//" + location.host;
moment.locale('fr');

alertify.set({ labels: {
    ok     : 'Ok !',
    cancel : 'Annuler'
}});

angular.module('upont', ['ui.router', 'ngResource', 'ngAnimate', 'mgcrea.ngStrap', 'ngSanitize', 'angular-jwt', 'angular.filter', 'naif.base64', 'infinite-scroll']);
