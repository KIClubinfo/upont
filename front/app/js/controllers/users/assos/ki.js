angular.module('upont')
    .controller('KI_Ctrl', ['$scope', '$rootScope', '$resource', '$http', 'fixes', 'ownFixes', 'members', 'Paginate', function($scope, $rootScope, $resource, $http, fixes, ownFixes, members, Paginate) {
        $scope.fixes = fixes;
        $scope.ownFixes = ownFixes;
        $scope.isFromKI = false;

        for (var key in members) {
            if (members[key].user !== undefined && members[key].user.username == $rootScope.me.username) {
                $scope.isFromKI = true;
            }
        }

        $scope.reload = function() {
            Paginate.first($scope.ownFixes).then(function(data){
                $scope.ownFixes = data;
            });
            Paginate.first($scope.fixes).then(function(data){
                $scope.fixes = data;
            });
        }

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
                $scope.reload();
            });
        };

        $scope.changeStatus = function(fix) {
            var params = {
                status: fix.status
            };

            if (fix.status == 'Résolu') {
                params.solved = moment().unix();
            }

            $http.patch(apiPrefix + 'fixes/' + fix.slug, params).success(function(data){
                $scope.reload();
            });
        };

        $scope.delete = function(fix) {
            alertify.confirm('Veux-tu vraiment faire ça ?', function(e) {
                if (e) {
                    $http.delete(apiPrefix + 'fixes/' + fix.slug).success(function(data){
                        $scope.reload();
                    });
                }
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
                    }],
                    members: ['$resource', function($resource) {
                        return $resource(apiPrefix + 'clubs/ki/users').query().$promise;
                    }]
                }
            });
    }]);
