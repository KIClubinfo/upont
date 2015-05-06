angular.module('upont')
    .controller('Admin_Ctrl', ['$scope', '$resource', '$location', function($scope, $resource, $location) {

    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state("root.zone_eleves.admin", {
                url: "admin",
                templateUrl: "views/zone_eleves/admin/index.html",
                controller: 'Admin_Ctrl',
                data: {
                    title: "Administration - uPont"
                }
            })
            .state("root.zone_eleves.admin.eleves", {
                url: "/eleves",
                templateUrl: 'views/zone_eleves/admin/eleves.html'
            })
            .state("root.zone_eleves.admin.channels", {
                url: "/channels",
                templateUrl: 'views/zone_eleves/admin/channels.html'
            })
            .state("root.zone_eleves.admin.permissions", {
                url: "/permissions",
                templateUrl: 'views/zone_eleves/admin/perms.html'
            })
            .state("root.zone_eleves.admin.logs", {
                url: "/logs",
                templateUrl: 'views/zone_eleves/admin/logs.html'
            })
            .state("root.zone_eleves.admin.moderation", {
                url: "/moderation",
                templateUrl: 'views/zone_eleves/admin/moderation.html'
            });
    }]);
