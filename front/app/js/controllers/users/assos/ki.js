angular.module('upont')
    .controller('KI_Ctrl', ['$scope', '$resource', '$http', 'fixes', 'ownFixes', 'Paginate', function($scope, $resource, $http, fixes, ownFixes, Paginate) {
        $scope.fixes = fixes;
        $scope.ownFixes = ownFixes;

        $scope.post = function(msg, isFix) {
            var params  = {
                problem: msg,
                name: msg.substring(0, 20),
                fix: isFix
            };

            $http.post(apiPrefix + 'fixes', params).success(function(data){
                $scope.fix = '';
                $scope.msg = '';
                alertify.success('Demande correctement envoyée !');

                Paginate.first($scope.ownFixes).then(function(data){
                    $scope.ownFixes = data;
                });
                Paginate.first($scope.fixes).then(function(data){
                    $scope.fixes = data;
                });
            });
        };

        $scope.changeStatus = function(slug, status) {
            var params = {
                status: status
            };

            if (status == 'Résolu') {
                params.solved = moment().unix();
            }

            $http.patch(apiPrefix + 'fixes/' + slug, params).success(function(data){
                alertify.success('Merci de nous aider à améliorer uPont :-)');

                Paginate.first($scope.ownFixes).then(function(data){
                    $scope.ownFixes = data;
                });
                Paginate.first($scope.fixes).then(function(data){
                    $scope.fixes = data;
                });
            });
        };
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.assos.ki', {
                url: '/ki',
                templateUrl: 'views/users/assos/ki.html',
                controller: 'KI_Ctrl',
                data: {
                    title: 'Dépannage - uPont',
                    top: true
                },
                resolve: {
                    fixes: ['Paginate', function(Paginate) {
                        return Paginate.get('fixes', 20);
                    }],
                    ownFixes: ['Paginate', function(Paginate) {
                        return Paginate.get('own/fixes', 20);
                    }]
                }
            });
    }]);
