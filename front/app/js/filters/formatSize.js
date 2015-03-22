angular.module('upont').filter('formatSize', function() {
    return function(size) {
        if (typeof(size) == 'number') {
            if (size >= 1024 * 1024 * 1024 * 0.8)
                return Math.floor(size / 10737418.24) / 100 + ' Go';
            if (size >= 1024 * 1024 * 0.8)
                return Math.floor(size / 10485.76) / 100 + ' Mo';
            if (size >= 1024 * 0.8)
                return Math.floor(size / 10.24) / 100 + ' Ko';
            return size + ' Octets';
        } else return null;
    };
});
