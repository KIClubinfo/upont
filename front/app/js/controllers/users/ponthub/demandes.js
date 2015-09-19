angular.module('upont')
    .controller('Ponthub_Demandes_Ctrl', ['$rootScope', '$scope', 'club', 'members', function($rootScope, $scope, club, members) {
        $scope.demandes = demandes;
        $scope.categories = ['film','série','album','jeu','logiciel','autre'];
        $scope.nom ='';
        $scope.category = '';

        $scope.addPoint() {
            $scope.member.points = $scope.member.points+1 ;
            }

        $scope.post = function(nom, category) {
            if (nom === undefined || category === undefined) {
                alertify.error('Au moins un des champs n\'est pas rempli');
                return;
            }

            $http.post(apiPrefix + '/ponthub/demande', {nom: nom, category: category})
                .success(function(){
                    alertify.success('Demande ajoutée !');
                    $scope.nom = '';;
                })
                .error(function(){
                    alertify.error('Erreur...');
                })
            ;
        };

    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.ponthub.demandes', {
                url: '/demandes',
                controller: 'Ponthub_Demandes_Ctrl',
                templateUrl: 'controllers/users/ponthub/demandes.html',
                resolve: {
                    demandes: ['$resource', '$stateParams', function($resource, $stateParams) {
                        return $resource(apiPrefix + 'ponthub/demandes').query().$promise;
                    }]
                }
            });
    }]);
