angular.module('upont').config(['$stateProvider', function($stateProvider) {
    $stateProvider
        .state("root.zone_admissibles.home", {
            url: "",
            templateUrl: "views/zone_admissibles/home.html",
            data: {
                title: 'Zone admissibles - uPont',
                top: true
            }
        });
}]);
