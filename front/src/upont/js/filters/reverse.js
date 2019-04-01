import angular from 'angular';

angular.module('upont').filter('reverse', function() {
    return function(items) {
        if(!Array.isArray(items)) {
            return [];
        }

        return items.slice().reverse();
    };
});
