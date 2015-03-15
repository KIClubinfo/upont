angular.module('upont')
    .directive('upPublishNew', function() {
        return {
            link: function(scope, element, args){
                scope.$watch('scope.startPublishing', function(){
                    element.prepend('<h1>Start Publish !</h1>');
                });
            }
        };
    });