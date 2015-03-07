angular.module('upont')
    .controller('Admin_Ctrl', ['$scope', '$resource', '$location', function($scope, $resource, $location) {

    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state("admin", {
                url: "/admin",
                templateUrl: "views/admin/index.html",
                data: {
                    defaultChild: "eleves",
                    parent: "admin",
                    title: "uPont - Administration"
                }
            })
            .state("admin.eleves", {
                url: "/eleves",
                templateUrl: 'views/admin/eleves.html',
                controller: 'Admin_Ctrl'
            })
            .state("admin.channels", {
                url: "/channels",
                templateUrl: 'views/admin/channels.html',
                controller: 'Admin_Ctrl'
            })
            .state("admin.permissions", {
                url: "/permissions",
                templateUrl: 'views/admin/perms.html',
                controller: 'Admin_Ctrl'
            })
            .state("admin.logs", {
                url: "/logs",
                templateUrl: 'views/admin/logs.html',
                controller: 'Admin_Ctrl'
            })
            .state("admin.moderation", {
                url: "/moderation",
                templateUrl: 'views/admin/moderation.html',
                controller: 'Admin_Ctrl'
            });
    }]);
