angular.module('upont').filter('formatDuration', function() {
    return function(duration) {
        if (typeof(duration) == 'number') {
            if (duration >= 3600)
                if (duration % 3600 === 0)
                    return Math.floor(duration / 3600) + 'h';
                else
                    return Math.floor(duration / 3600) + 'h' + Math.floor((duration % 3600) / 60);
            if (duration >= 60)
                if (duration % 60 === 0)
                    return Math.floor(duration / 60) + 'mn';
                else
                    return Math.floor(duration / 60) + 'mn' + Math.floor(duration % 60);
            return duration + 's';
        } else
            return null;
    };
});
