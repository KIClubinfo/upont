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

        var serie1 = {
            openDate: moment("2016-06-09 20:00 +0200", "YYYY-MM-DD HH:mm Z"),
            closeDate: moment("2016-06-12 23:00 +0200", "YYYY-MM-DD HH:mm Z"),
            serie: 1,
        };

        $scope.shotgun = serie1;
        $scope.shotgun.openDateString = $scope.shotgun.openDate.format('LLLL');
        $scope.shotgun.closeDateString = $scope.shotgun.closeDate.format('LLLL');

        var tick = function () {
            var now = moment();
            $scope.shotgun.fromNow = $scope.shotgun.openDate.fromNow();
            $scope.shotgunOpen = $scope.shotgun.openDate.isBefore();
            $timeout(tick, 1000);
        };
        $timeout(tick, 1000);

        $scope.shotgunOpen = $scope.shotgun.openDate.isBefore();

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
