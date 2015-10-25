angular.module('upont')
    .controller('Publications_Post_Messages_Ctrl', ['$scope', '$rootScope', '$http', function($scope, $rootScope, $http) {
        // Fonctions relatives à la publication
        var init = function() {
            $scope.focus = false;
            $scope.msg = {
                text: ''
            };
        };
        init();

        $scope.post = function(msg, image){
            var params  = {
                text: msg.text
            };

            if (image) {
                params.image = image.base64;
            }

            $http.post(apiPrefix + 'messages', params).success(function(data){
                $rootScope.$broadcast('newMessage');
                init();
            }).error(function(){
                alertify.error('Formulaire vide ou mal rempli');
            });
        };
    }]);
