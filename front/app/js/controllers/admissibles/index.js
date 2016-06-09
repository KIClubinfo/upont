angular.module('upont').controller('Admissibles_Ctrl', ['$scope', '$location', '$http', '$timeout', 'Scroll', function($scope, $location, $http, $timeout, Scroll) {
        $scope.goTo = function (id){
            $location.hash(id);
            Scroll.scrollTo(id);
        };
        $scope.campuschannel = 'https://www.youtube.com/watch?v=yIPz0ecYM1w';
        $scope.admissible = {};
        var downloads = [
            'https://upont.enpc.fr/plaquette.pdf'
        ];

        $scope.shotgun = {
            openDate: moment("2016-06-15 20:00 +0200", "YYYY-MM-DD HH:mm Z"),
            serie1CloseDate: moment("2016-06-16 20:00 +0200", "YYYY-MM-DD HH:mm Z"),
            closeDate: moment("2016-06-20 23:00 +0200", "YYYY-MM-DD HH:mm Z"),
        };
        $scope.shotgun.openDateString = $scope.shotgun.openDate.format('LLLL');
        $scope.shotgun.serie1CloseDateString = $scope.shotgun.serie1CloseDate.format('LLLL');
        $scope.shotgun.closeDateString = $scope.shotgun.closeDate.format('LLLL');

        var tick = function () {
            $scope.shotgun.fromNow = $scope.shotgun.openDate.fromNow();
            $scope.shotgunOpen = $scope.shotgun.openDate.isBefore() && $scope.shotgun.closeDate.isAfter();
            $scope.serie1Open = $scope.shotgun.openDate.isBefore() && $scope.shotgun.serie1CloseDate.isAfter();
            $timeout(tick, 1000);
        };
        $timeout(tick, 1000);

        $scope.shotgunOpen = $scope.shotgun.openDate.isBefore() && $scope.shotgun.closeDate.isAfter();
        $scope.serie1Open = $scope.shotgun.openDate.isBefore() && $scope.shotgun.serie1CloseDate.isAfter();

        $scope.submit = function(data) {
            if (data.lastName === undefined || data.firstName === undefined || data.scei === undefined || data.contact === undefined || data.serie === undefined || data.room === undefined || data.lastName === '' || data.firstName === '' || data.scei === '' || data.contact === '' || data.serie === '' || data.room === '') {
                alertify.error('Au moins un des champs n\'est pas rempli...');
                return;
            }

            $http.post(apiPrefix + 'admissibles', data).success(function(){
                alertify.success('Demande prise en compte !');
            });
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
                templateUrl: 'controllers/admissibles/index.html',
                data: {
                    title: 'Espace Admissibles - uPont',
                    top: true
                }
            });
    }]);
