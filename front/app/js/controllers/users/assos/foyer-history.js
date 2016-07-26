angular.module('upont')
   .controller('Foyer_History_Ctrl', ['$scope', '$rootScope', '$http', 'historique', 'Paginate', function($scope, $rootScope, $http, historique, Paginate) {
    $scope.historique = historique;

    $scope.next = function() {
        Paginate.next($scope.historique).then(function(data){
            $scope.historique = data;
        });
    };

    }])
    .config(['$stateProvider', function($stateProvider) {
       $stateProvider
           .state('root.users.assos.foyer-history', {
               url: '/foyer/historique',
               templateUrl: 'controllers/users/assos/foyer-history.html',
               controller: 'Foyer_History_Ctrl',
               data: {
                   title: 'Historique foyer - uPont',
                   top: true
               },
               resolve: {
                   //historique: ['$resource', '$rootScope', function($resource, $rootScope) {
                   //    return $resource(apiPrefix + 'users/:slug/transactions').query( {slug: $rootScope.username}).$promise;
                   historique: ['Paginate', '$rootScope', function(Paginate, $rootScope) {
                        return Paginate.get('users/' + $rootScope.username +'/transactions?sort=-date', 8);
                   }]
               }
           });
   }]);
