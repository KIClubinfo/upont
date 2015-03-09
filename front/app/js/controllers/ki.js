angular.module('upont').controller('KI_Ctrl', ['$scope', '$resource', function($scope, $resource) {
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state("root.ki", {
                url: "ki",
                templateUrl: "views/ki/index.html",
                data: {
                    defaultChild: "contact",
                    parent: "ki",
                    title: "uPont - KI"
                }
            })
            .state("root.ki.contact", {
                url: "/contact",
                templateUrl: 'views/ki/contact.html',
                controller: 'KI_Ctrl',
                data: {
                    title: "uPont - Dépannage"
                }
            })
            .state("root.ki.tutos", {
                url: "/tutoriels",
                templateUrl: 'views/ki/tutos.html',
                controller: 'KI_Ctrl',
                data: {
                    title: "uPont - Tutoriels"
                }
            })
            .state("root.ki.avancement", {
                url: "/avancement",
                templateUrl: 'views/ki/avancement.html',
                controller: 'KI_Ctrl',
                data: {
                    title: "uPont - Statistiques"
                }
            });
    }]);
