import angular from 'angular';
// Applique un suffixe de position à un nombre

angular.module('upont').filter('formatPosition', function() {
    return function(position) {
        return position == 1
            ? '1ère'
            : position + 'ème';
    };
});
