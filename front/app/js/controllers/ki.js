angular.module('upont').controller('KI_Ctrl', ['$scope', '$resource', function($scope, $resource) {
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state("ki", {
                url: "/ki",
                templateUrl: "views/ki/index.html",
                data: {
                    defaultChild: "contact",
                    parent: "ki"
                }
            })
            .state("ki.contact", {
                url: "/contact",
                templateUrl: 'views/ki/contact.html',
                controller: 'KI_Ctrl'
            })
            .state("ki.tutos", {
                url: "/tutoriels",
                templateUrl: 'views/ki/tutos.html',
                controller: 'KI_Ctrl'
            })
            .state("ki.avancement", {
                url: "/avancement",
                templateUrl: 'views/ki/avancement.html',
                controller: 'KI_Ctrl'
            });
    }]);
