angular.module('upont').controller('Admissibles_Ctrl', ['$scope', '$location', '$http', 'Scroll', function($scope, $location, $http, Scroll) {
        $scope.goTo = function (id){
            $location.hash(id);
            Scroll.scrollTo(id);
        };
        $scope.campuschannel = 'https://www.youtube.com/watch?v=hNOfq6rmT2I';
        $scope.admissible = {};
        var downloads = [
            'https://upont.enpc.fr/plaquette.pdf'
        ];
        //'http://autonomie-universites.toile-libre.org/plaquette.pdf'
        var rand = Math.floor((Math.random() * downloads.length) + 1);
        $scope.download = downloads[rand-1];

        $scope.submit = function(data) {
            if (data.lastName === undefined || data.firstName === undefined || data.scei === undefined || data.contact === undefined || data.serie === undefined || data.room === undefined || data.lastName === '' || data.firstName === '' || data.scei === '' || data.contact === '' || data.serie === '' || data.room === '') {
                alertify.error('Au moins un des champs n\'est pas rempli...');
                return;
            }

            $http.post(apiPrefix + 'admissibles', data).success(function(){
                alertify.success('Demande prise en compte !');
            });
        };
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.admissibles', {
                url: 'admissibles',
                abstract: true,
                template: '<div ui-view></div>'
            })
            .state('root.admissibles.index', {
                url: '',
                controller: 'Admissibles_Ctrl',
                templateUrl: 'controllers/admissibles/index.html',
                data: {
                    title: 'Espace Admissibles - uPont',
                    top: true
                }
            });
    }]);
