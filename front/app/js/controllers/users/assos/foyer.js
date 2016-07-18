angular.module('upont')
    .controller('Foyer_Ctrl', ['$scope', '$rootScope', '$http', 'youtube', 'stats', 'members', 'Paginate', function($scope, $rootScope, $http, youtube, stats, members, Paginate) {
        $('#focus-input').focus();
        $scope.youtube = youtube;
        $scope.stats = stats;
        $scope.predicate = 'liters';
        $scope.reverse = true;
        $scope.isFromFoyer = false;

        for (var key in members) {
            if (members[key].user !== undefined && members[key].user.username == $rootScope.me.username) {
                $scope.isFromFoyer = true;
            }
        }

        $scope.reload = function() {
            Paginate.first($scope.youtube).then(function(data){
                $scope.youtube = data;
            });
        };

        $scope.post = function(link) {
            if (!link.match(/^(https?\:\/\/)?(www\.youtube\.com|youtu\.?be)\/.+$/)) {
                alertify.error('Ce n\'est pas une vidéo YouTube !');
                return;
            }

            $http.post(apiPrefix + 'youtubes', {name: 'Lien Youtube Foyer', link: link}).success(function(data){
                $scope.link = '';
                alertify.success('Yeah !');
                $scope.reload();
            });
        };

        $scope.delete = function(youtube) {
            alertify.confirm('Veux-tu vraiment faire ça ?', function(e) {
                if (e) {
                    $http.delete(apiPrefix + 'youtubes/' + youtube.slug).success(function(data){
                        $scope.reload();
                    });
                }
            });
        };
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.assos.foyer', {
                url: '/c-est-ton-foyer',
                templateUrl: 'controllers/users/assos/foyer.html',
                controller: 'Foyer_Ctrl',
                data: {
                    title: 'Foyer - uPont',
                    top: true
                },
                resolve: {
                    youtube: ['Paginate', function(Paginate) {
                        return Paginate.get('youtubes?sort=-date', 20);
                    }],
                    stats: ['$resource', function($resource) {
                        return $resource(apiPrefix + 'statistics/foyer').get().$promise;
                    }],
                    members: ['$resource', function($resource) {
                        return $resource(apiPrefix + 'clubs/foyer/users').query().$promise;
                    }]
                }
            });
    }]);
