angular.module('upont').filter('ucfirst', function() {
    return function(string) {
        return ucfirst(string);
    };
});
