angular.module('upont')
    .controller('KI_Ctrl', ['$scope', '$rootScope', '$resource', '$http', 'fixes', 'ownFixes', 'members', 'Paginate', 'Achievements', function($scope, $rootScope, $resource, $http, fixes, ownFixes, members, Paginate, Achievements) {
        $('#focus-input').focus();
        $scope.fixes = fixes;
        $scope.ownFixes = ownFixes;
        $scope.isFromKI = false;
        $rootScope.displayTabs = true;

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
        };

        $scope.post = function(msg, isFix) {
            if($rootScope.isAdmissible)
                return;

            var params  = {
                problem: msg,
                name: msg.substring(0, 20),
                fix: isFix
            };

            $http.post(apiPrefix + 'fixes', params).success(function(data){
                $scope.fix = '';
                $scope.msg = '';
                alertify.success('Demande correctement envoyée !');
                Achievements.check();
                $scope.reload();
            });
        };

        $scope.changeStatus = function(fix) {
            $http.patch(apiPrefix + 'fixes/' + fix.slug, {status: fix.status}).success(function(data){
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
                url: '/depannage',
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
    }])
    .filter('statusFilter', function() {
        return function(array) {
            fixes = {
                unseen: [],
                waiting: [],
                doing: [],
                done: [],
                closed: []
            };

            for (var key in array) {
                switch (array[key].status) {
                    case 'Non vu': fixes.unseen.push(array[key]); break;
                    case 'En attente': fixes.waiting.push(array[key]); break;
                    case 'En cours': fixes.doing.push(array[key]); break;
                    case 'Résolu': fixes.done.push(array[key]); break;
                    case 'Fermé': fixes.closed.push(array[key]); break;
                }
            }
            return fixes.unseen.concat(fixes.waiting.concat(fixes.doing.concat(fixes.done.concat(fixes.closed))));
        };
    })
;
