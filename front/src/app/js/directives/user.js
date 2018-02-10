import angular from 'angular';

import { API_PREFIX } from 'upont/js/config/constants';

import template_user from './user.html';


angular.module('upont').directive('upUser', function() {
    return {
        transclude: true,
        scope: {
            user: '='
        },
        link: {
            post: function(scope, element){
                element.addClass('User');
            }
        },
        controller: ['$scope', '$rootScope', '$resource', '$timeout', function($scope, $rootScope, $resource, $timeout) {
            $scope.hover = false;
            $scope.clubs = [];
            $scope.timer = null;
            $scope.timerOut = null;

            $scope.hoverIn = function(){
                $timeout.cancel($scope.timerOut);

                if (!$rootScope.hovering && !$scope.hover) {
                    $scope.timer = $timeout(function () {
                        $rootScope.hovering = true;
                        $resource(API_PREFIX + 'users/' + $scope.user.username + '/clubs').query(function(data) {
                            $scope.clubs = data;

                            // On ferme tous les autres
                            $rootScope.$broadcast('closeHover');
                            $rootScope.hovering = false;
                            $scope.hover = true;
                        });
                    }, 500);
                }
            };

            $scope.hoverOut = function(){
                $scope.timerOut = $timeout(function () {
                    $timeout.cancel($scope.timer);
                    $scope.hover = false;
                    $rootScope.$broadcast('closeHover');
                }, 200);
            };

            $scope.$on('closeHover', function() {
                $scope.hover = false;
            });
        }],
        templateUrl: template_user
    };
});
