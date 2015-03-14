var apiPrefix = "/api/";
if (!location.origin)
     location.origin = location.protocol + "//" + location.host;
moment.locale('fr');

angular.module('upont', ['ui.router', 'ngResource', 'ngAnimate', 'mgcrea.ngStrap', 'ngSanitize', 'angular-jwt', 'angular.filter']);
