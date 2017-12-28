import angular from 'angular';

angular.module('upont').filter('thumb', function() {
    return function(path) {
        if (path === null || path === undefined) {
            return;
        }
        return path.replace(/images/, 'thumbnails');
    };
});
