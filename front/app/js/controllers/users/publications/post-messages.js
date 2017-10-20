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

        $scope.post = function(msg, image){
            var params  = {
                text: msg.text,
                name: 'message',
                publicationState: 'Published'
            };

            if (image) {
                params.image = image.base64;
            }

            $http.post(apiPrefix + 'newsitems', params).then(function(){
                $rootScope.$broadcast('newMessage');
                init();
            }, function(){
                alertify.error('Formulaire vide ou mal rempli');
            });
        };
    }]);
