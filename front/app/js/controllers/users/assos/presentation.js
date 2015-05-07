angular.module('upont')
    .controller('Assos_Presentation_Ctrl', ['$scope', function($scope) {
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.assos.simple.presentation', {
                url: '/presentation',
                controller : 'Assos_Presentation_Ctrl',
                templateUrl: 'views/users/assos/presentation.html',
                data: {
                    title: 'Présentation - uPont',
                    top: true
                }
            });
    }]);
