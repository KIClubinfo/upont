import constants, { API_PREFIX } from 'upont/js/config/constants';

angular.module('upont')
    .controller('Assos_Presentation_Ctrl', ['$scope', '$http', '$sce', '$filter', function($scope, $http, $sce, $filter) {
        $scope.PROMOS = constants.PROMOS;

        $scope.presentation = $sce.trustAsHtml($scope.club.presentation);
        $scope.edit = false;

        $scope.editPresentation = function() {
            $scope.edit = true;
        };

        $scope.modify = function(presentation) {
            $http.patch(API_PREFIX + 'clubs/' + $scope.club.slug, {presentation: presentation}).then(function(){
                $scope.presentation = $sce.trustAsHtml(presentation);
                alertify.success('Modifications prises en compte !');
            });
            $scope.edit = false;
        };
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.assos.simple.presentation', {
                url: '/presentation',
                controller : 'Assos_Presentation_Ctrl',
                templateUrl: 'controllers/users/assos/presentation.html',
                data: {
                    title: 'Présentation - uPont',
                    top: true
                }
            });
    }]);
