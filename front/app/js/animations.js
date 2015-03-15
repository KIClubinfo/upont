angular.module('upont')
    .animation('.up-slide', function(){
        return {
            enter: function(element, done){
                element.hide().slideDown();
            },
            leave: function(element, done){
                element.slideUp();
            }
        };
    });
