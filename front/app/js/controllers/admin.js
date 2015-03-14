angular.module('upont')
    .controller('Admin_Ctrl', ['$scope', '$resource', '$location', function($scope, $resource, $location) {

    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state("root.admin", {
                url: "admin",
                templateUrl: "views/admin/index.html",
                data: {
                    defaultChild: "eleves",
                    parent: "admin",
                    title: "uPont - Administration"
                }
            })
            .state("root.admin.eleves", {
                url: "/eleves",
                templateUrl: 'views/admin/eleves.html',
                controller: 'Admin_Ctrl'
            })
            .state("root.admin.channels", {
                url: "/channels",
                templateUrl: 'views/admin/channels.html',
                controller: 'Admin_Ctrl'
            })
            .state("root.admin.permissions", {
                url: "/permissions",
                templateUrl: 'views/admin/perms.html',
                controller: 'Admin_Ctrl'
            })
            .state("root.admin.logs", {
                url: "/logs",
                templateUrl: 'views/admin/logs.html',
                controller: 'Admin_Ctrl'
            })
            .state("root.admin.moderation", {
                url: "/moderation",
                templateUrl: 'views/admin/moderation.html',
                controller: 'Admin_Ctrl'
            });
    }]);
