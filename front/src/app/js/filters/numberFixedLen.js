import angular from 'angular';

angular.module('upont').filter('numberFixedLen', function () {
    return function (n, l) {
        var num = parseInt(n,10);
        var len = parseInt(l,10);
        if (isNaN(num) || isNaN(len)) {
            return n;
        }
        num = '' + num;
        while (num.length < len) {
            num = '0' + num;
        }
        return num;
    };
});
