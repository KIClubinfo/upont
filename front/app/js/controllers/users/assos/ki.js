angular.module('upont')
    .controller('KI_Ctrl', ['$scope', '$rootScope', '$resource', '$http', 'fixs', 'ownFixs', 'members', 'Paginate', 'Achievements', function($scope, $rootScope, $resource, $http, fixs, ownFixs, members, Paginate, Achievements) {
        $('#focus-input').focus();
        $scope.fixs = fixs;
        $scope.ownFixs = ownFixs;
        $scope.isFromKI = false;
        $rootScope.displayTabs = true;

        for (var key in members) {
            if (members[key].user !== undefined && members[key].user.username == $rootScope.username) {
                $scope.isFromKI = true;
            }
        }

        $scope.reload = function() {
            Paginate.first($scope.ownFixs).then(function(data){
                $scope.ownFixs = data;
            });
            Paginate.first($scope.fixs).then(function(data){
                $scope.fixs = data;
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

            $http.post(apiPrefix + 'fixs', params).success(function(data){
                $scope.fix = '';
                $scope.msg = '';
                alertify.success('Demande correctement envoyée !');
                Achievements.check();
                $scope.reload();
            });
        };

        $scope.changeStatus = function(fix) {
            $http.patch(apiPrefix + 'fixs/' + fix.slug, {status: fix.status}).success(function(data){
                $scope.reload();
            });
        };

        $scope.delete = function(fix) {
            alertify.confirm('Veux-tu vraiment supprimer le ticket ?', function(e) {
                if (e) {
                    $http.delete(apiPrefix + 'fixs/' + fix.slug).success(function(data){
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
                templateUrl: 'controllers/users/assos/ki.html',
                controller: 'KI_Ctrl',
                data: {
                    title: 'Dépannage - uPont',
                    top: true
                },
                resolve: {
                    fixs: ['Paginate', function(Paginate) {
                        return Paginate.get('fixs', 50);
                    }],
                    ownFixs: ['Paginate', function(Paginate) {
                        return Paginate.get('own/fixs', 50);
                    }],
                    members: ['$resource', function($resource) {
                        return $resource(apiPrefix + 'clubs/ki/users').query().$promise;
                    }]
                }
            });
    }])
    .filter('statusFilter', function() {
        return function(array) {
            fixs = {
                unseen: [],
                waiting: [],
                doing: [],
                done: [],
                closed: []
            };

            for (var key in array) {
                switch (array[key].status) {
                    case 'Non vu': fixs.unseen.push(array[key]); break;
                    case 'En attente': fixs.waiting.push(array[key]); break;
                    case 'En cours': fixs.doing.push(array[key]); break;
                    case 'Résolu': fixs.done.push(array[key]); break;
                    case 'Fermé': fixs.closed.push(array[key]); break;
                }
            }
            return fixs.unseen.concat(fixs.waiting.concat(fixs.doing.concat(fixs.done.concat(fixs.closed))));
        };
    })
;
