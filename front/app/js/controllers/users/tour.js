angular.module('upont')
    .controller('Tour_Ctrl', ['$scope', '$rootScope', '$http', '$state', function($scope, $rootScope, $http, $state) {
        var steps= [
            {
                state: 'root.users.publications.index',
                icon: 'sign-in',
                text: '<strong>Bienvenue sur uPont !</strong><br>' +
                'Que tu sois nouveau ou un vieux, cet intranet déborde tellement de fonctionnalités que  '
            },
            {
                state: 'root.users.publications.index',
                icon: 'sign-in',
                text: 'Bienvenue sur uPont 2 !'
            },
            {
                state: 'root.users.calendar',
                icon: 'calendar',
                text: 'Ceci est le calendrier !'
            }
        ];
        $scope.numberSteps = steps.length;
        $scope.step = 0;

        $scope.off = function() {
            // On demande confirmation
            alertify.confirm('Veux-tu quitter le tutoriel ? Tu pourras toujours le réactiver depuis la page de profil.', function(e){
                if (e) {
                    $http.patch($rootScope.url + 'users/' + $rootScope.me.username + '?achievement=unlocked', {tour: true}).success(function(){
                        $rootScope.me.tour = true;
                        alertify.success('Tutoriel masqué !');
                    });
                }
            });
        };

        $scope.previous = function() {
            if ($scope.step > 0)
                $scope.loadStep($scope.step - 1);
        };

        $scope.next = function() {
            if ($scope.step + 1 < steps.length) {
                $scope.loadStep($scope.step + 1);
            } else if ($scope.step + 1 == steps.length) {
                $http.patch($rootScope.url + 'users/' + $rootScope.me.username, {tour: true}).success(function(){
                    $rootScope.me.tour = true;
                });
            }
        };

        $scope.loadStep = function(step) {
            $state.transitionTo(steps[step].state).then(function(){
                $scope.step = step;
                $scope.icon = steps[step].icon;
                $scope.text = steps[step].text;
            });
        };

        if (!$rootScope.me.tour)
            $scope.loadStep(0);

        $rootScope.$on('tourEnabled', function() {
            $scope.step = 0;
            $scope.loadStep(0);
        });
    }]);
