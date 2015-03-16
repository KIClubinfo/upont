angular.module('upont').filter('formatSize', function() {
    return function(size) {
        if (typeof(size) == 'number') {
            if (size >= 1024 * 1024 * 1024 * 0.8)
                return Math.floor(size / 10737418.24) / 100 + ' Gio';
            if (size >= 1024 * 1024 * 0.8)
                return Math.floor(size / 10485.76) / 100 + ' Mio';
            if (size >= 1024 * 0.8)
                return Math.floor(size / 10.24) / 100 + ' Kio';
            return size + ' Octets';
        } else return null;
    };
});
