angular.module('upont')
    .controller('Publications_Post_Messages_Ctrl', ['$scope', '$rootScope', '$http', function($scope, $rootScope, $http) {
        // Fonctions relatives à la publication
        var club = {name: 'Au nom de...'};
        var init = function() {
            $scope.focus = false;
            $scope.msg = {
                text: ''
            };
        };
        init();

        $scope.post = function(msg){
            var params  = {
                text: msg.text,
                name: 'null'
            };

            $http.post(apiPrefix + 'newsitems', params).success(function(data){
                $rootScope.$broadcast('newMessage');
                init();
            }).error(function(){
                alertify.error('Formulaire vide ou mal rempli');
            });
        };
    }]);
