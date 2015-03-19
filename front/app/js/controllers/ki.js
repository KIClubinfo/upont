angular.module('upont').controller('KI_Ctrl', ['$scope', '$resource', function($scope, $resource) {
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state("root.ki", {
                url: "ki",
                abstract: true,
                templateUrl: "views/ki/index.html",
                data: {
                    title: "KI - uPont"
                }
            })
            .state("root.ki.contact", {
                url: "/contact",
                templateUrl: 'views/ki/contact.html',
                controller: 'KI_Ctrl',
                data: {
                    title: "Dépannage - uPont"
                }
            })
            .state("root.ki.tutos", {
                url: "/tutoriels",
                templateUrl: 'views/ki/tutos.html',
                controller: 'KI_Ctrl',
                data: {
                    title: "Tutoriels - uPont"
                }
            })
            .state("root.ki.avancement", {
                url: "/avancement",
                templateUrl: 'views/ki/avancement.html',
                controller: 'KI_Ctrl',
                data: {
                    title: "Statistiques - uPont"
                }
            });
    }]);
