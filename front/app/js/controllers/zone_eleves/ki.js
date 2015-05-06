angular.module('upont').controller('KI_Ctrl', ['$scope', '$resource', function($scope, $resource) {
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state("root.zone_eleves.ki", {
                url: "ki",
                abstract: true,
                templateUrl: "views/zone_eleves/ki/index.html",
                data: {
                    title: "KI - uPont"
                }
            })
            .state("root.zone_eleves.ki.contact", {
                url: "/contact",
                templateUrl: 'views/zone_eleves/ki/contact.html',
                controller: 'KI_Ctrl',
                data: {
                    title: "Dépannage - uPont"
                }
            })
            .state("root.zone_eleves.ki.tutos", {
                url: "/tutoriels",
                templateUrl: 'views/zone_eleves/ki/tutos.html',
                controller: 'KI_Ctrl',
                data: {
                    title: "Tutoriels - uPont"
                }
            })
            .state("root.zone_eleves.ki.avancement", {
                url: "/avancement",
                templateUrl: 'views/zone_eleves/ki/avancement.html',
                controller: 'KI_Ctrl',
                data: {
                    title: "Statistiques - uPont"
                }
            });
    }]);
