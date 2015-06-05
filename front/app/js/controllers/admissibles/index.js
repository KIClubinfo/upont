angular.module('upont').controller('Admissibles_Ctrl', ['$scope', '$location', 'Scroll', function($scope, $location, Scroll) {
        $scope.goTo = function (id){
            $location.hash(id);
            Scroll.scrollTo(id);
        };
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.admissibles', {
                url: 'admissibles',
                abstract: true,
                template: '<div ui-view></div>'
            })
            .state('root.admissibles.index', {
                url: '',
                controller: 'Admissibles_Ctrl',
                templateUrl: 'views/admissibles/index.html',
                data: {
                    title: 'Espace Admissibles - uPont',
                    top: true
                }
            });
    }]);
