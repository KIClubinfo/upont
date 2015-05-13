angular.module('upont')
    .controller('Foyer_Ctrl', ['$scope', '$http', 'youtube', 'stats', 'Paginate', function($scope, $http, youtube, stats, Paginate) {
        $('#focus-input').focus();
        $scope.youtube = youtube;
        $scope.stats = stats.rankings;
        $scope.predicate = 'litres_bus';
        $scope.reverse = true;

        $scope.reload = function() {
            Paginate.first($scope.youtube).then(function(data){
                $scope.youtube = data;
            });
        };

        $scope.post = function(link) {

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
                templateUrl: 'views/users/assos/foyer.html',
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
                        return $resource(apiPrefix + 'foyer/statistics').get().$promise;
                    }]
                }
            });
    }]);
