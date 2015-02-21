module
    .controller('MainController', ['$rootScope', 'StorageService', '$http', 'PushNotifications', function($rootScope, StorageService, $http, PushNotifications) {
        $rootScope.dark = StorageService.get('dark') ? true : false;
    }
]);
