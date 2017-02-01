angular.module('upont').controller('Admissibles_Ctrl', ['$scope', '$location', '$http', '$timeout', 'Scroll', function($scope, $location, $http, $timeout, Scroll) {
        $scope.goTo = function(id) {
            $location.hash(id);
            Scroll.scrollTo(id);
        };
        $scope.campuschannel = 'https://www.youtube.com/watch?v=yIPz0ecYM1w';
        $scope.admissible = {};

        $scope.shotgun = {
            openDate: moment($rootScope.config.admissibles.openDate, 'YYYY-MM-DD HH:mm Z'),
            closeDate: moment($rootScope.config.admissibles.closeDate, 'YYYY-MM-DD HH:mm Z'),
        };
        $scope.shotgun.openDateString = $scope.shotgun.openDate.format('LLLL');
        $scope.shotgun.closeDateString = $scope.shotgun.closeDate.format('LLLL');

        var tick = function() {
            $scope.shotgun.fromNow = $scope.shotgun.openDate.fromNow();
            $scope.shotgunOpen = $scope.shotgun.openDate.isBefore() && $scope.shotgun.closeDate.isAfter();
            $timeout(tick, 1000);
        };
        $timeout(tick, 1000);

        $scope.shotgunOpen = $scope.shotgun.openDate.isBefore() && $scope.shotgun.closeDate.isAfter();

        $scope.submit = function(data) {
            if (!$scope.isLoading) {
                $scope.isLoading = true;

                if (data.lastName === undefined || data.firstName === undefined || data.scei === undefined || data.contact === undefined || data.serie === undefined || data.room === undefined || data.lastName === '' || data.firstName === '' || data.scei === '' || data.contact === '' || data.serie === '' || data.room === '') {
                    alertify.error('Au moins un des champs n\'est pas rempli...');
                    return;
                }

                $http.post(apiPrefix + 'admissibles', data).then(function() {
                    $scope.isLoading = false;
                    alertify.success('Demande prise en compte !');
                }, function(response) {
                    $scope.isLoading = false;
                    if (response.data.code == 400 && response.data.errors.children.scei.errors !== undefined)
                        alertify.error('Tu es déjà inscrit !');
                    else
                        alertify.error('Erreur...');
                });

            }
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
