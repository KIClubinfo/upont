angular.module('upont').filter('urlFile', function() {
    return function(input, inputParent) {
        return apiPrefix + input;
    };
});
