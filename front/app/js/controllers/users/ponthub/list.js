angular.module('upont')
    .controller('Ponthub_List_Ctrl', ['$scope', '$stateParams', 'elements', 'Paginate', 'Ponthub', function($scope, $stateParams, elements, Paginate, Ponthub) {
        $scope.elements = elements;
        $scope.category = $stateParams.category;
        $scope.lastWeek = moment().subtract(7 , 'days').unix();

        $scope.faIcon = function(element){
            switch(element.type){
                case 'game':
                    return 'fa-gamepad';
                case 'movie':
                case 'serie':
                    return 'fa-film';
                case 'album':
                    return 'fa-music';
                case 'other':
                    return 'fa-file-o';
                case 'software':
                    return 'fa-desktop';
                default:
                    return '';
            }
        };

        $scope.next = function() {
            Paginate.next($scope.elements).then(function(data){
                $scope.elements = data;
            });
        };

        $scope.popular = function(count) {
            return Ponthub.isPopular(count, $stateParams.category);
        };
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.ponthub', {
                url: 'ponthub/:category',
                templateUrl: 'views/users/ponthub/index.html',
                abstract: true,
                data: {
                    title: 'PontHub - uPont',
                    top: true
                },
                params: {
                    category: 'films'
                }
            })
            .state('root.users.ponthub.list', {
                url: '',
                templateUrl: 'views/users/ponthub/list.html',
                controller: 'Ponthub_List_Ctrl',
                resolve: {
                    elements: ['Paginate', '$stateParams', 'Ponthub', function(Paginate, $stateParams, Ponthub) {
                        return Paginate.get(Ponthub.cat($stateParams.category) + '?sort=-added,id', 20);
                    }]
                }
            });
    }]);
