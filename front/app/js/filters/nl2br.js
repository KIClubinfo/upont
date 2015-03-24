angular.module('upont').filter('nl2br', function() {
    return function(text) {
        return nl2br(text);
    };
});
