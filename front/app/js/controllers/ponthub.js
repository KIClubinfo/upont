angular.module('upont')
	.controller("PH_Ctrl", ['$scope', 'elements', function ($scope, elements) {
		$scope.elements = elements;
	}])
	.config(['$stateProvider', function ($stateProvider){
		$stateProvider.state("ponthub", {
	            url : "/ponthub",
	            templateUrl : "views/ponthub/index.html",
	            data : { defaultChild : "films", parent : "ponthub" }
	        })
	        .state("ponthub.films", {
	            url : "/films",
	            templateUrl : "views/ponthub/films.html",
	            controller : 'PH_Ctrl',
	            data : { model : "movies" },
	            resolve : {
	            	elements : ['$resource', function ($resource) {
						return $resource(apiPrefix+"ponthub/movies").query().$promise;
					}]
	            }
	        })
	        .state("ponthub.series", {
	            url : "/series",
	            templateUrl : "views/ponthub/series.html",
	            controller : 'PH_Ctrl',
	            data : { model : "series" },
	            resolve : {
	            	elements : ['$resource', function ($resource) {
						return $resource(apiPrefix+"ponthub/series").query().$promise;
					}]
	            }
	        })
	        .state("ponthub.musiques", {
	            url : "/musiques",
	            templateUrl : "views/ponthub/musiques.html",
	            controller : 'PH_Ctrl',
	            data : { model : "albums" },
	            resolve : {
	            	elements : ['$resource', function ($resource) {
						return $resource(apiPrefix+"ponthub/albums").query().$promise;
					}]
	            }
	        })
	        .state("ponthub.jeux", {
	            url : "/jeux",
	            templateUrl : "views/ponthub/jeux.html",
	            controller : 'PH_Ctrl',
	            data : { model : "games" },
	            resolve : {
	            	elements : ['$resource', function ($resource) {
						return $resource(apiPrefix+"ponthub/games").query().$promise;
					}]
	            }
	        })
	        .state("ponthub.logiciels", {
	            url : "/logiciels",
	            templateUrl : "views/ponthub/logiciels.html",
	            controller : 'PH_Ctrl',
	            data : { model : "softwares" },
	            resolve : {
	            	elements : ['$resource', function ($resource) {
						return $resource(apiPrefix+"ponthub/softwares").query().$promise;
					}]
	            }
	        })
	        .state("ponthub.autres", {
	            url : "/autres",
	            templateUrl : "views/ponthub/autres.html",
	            controller : 'PH_Ctrl',
	            data : { model : "others" },
	            resolve : {
	            	elements : ['$resource', function ($resource) {
						return $resource(apiPrefix+"ponthub/others").query().$promise;
					}]
	            }
	        });
	}]);
