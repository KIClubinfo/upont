angular.module('upont').filter('urlFile', function() {
    return function(input, inputParent) {
        if (typeof(input) == 'string')
            return apiPrefix + input;
        // return apiPrefix + url;
        else if (typeof(input) == 'object') {
            switch (input.type) {
                case 'movie':
                    return apiPrefix + 'movies/' + input.slug + '/download';
                case 'album':
                    return apiPrefix + 'albums/' + input.slug + '/download';
                case 'game':
                    return apiPrefix + 'games/' + input.slug + '/download';
                case 'software':
                    return apiPrefix + 'softwares/' + input.slug + '/download';
                case 'other':
                    return apiPrefix + 'others/' + input.slug + '/download';
                case 'episode':
                    if (inputParent && typeof(inputParent) == 'object' && inputParent.type == 'serie')
                        return apiPrefix + 'series/' + inputParent.slug + '/episodes/' + input.slug + '/download';
                    break;
                case 'exercice':
                    if (inputParent && typeof(inputParent) == 'object' && inputParent.type == 'course')
                        return apiPrefix + 'courses/' + inputParent.slug + '/exercices/' + input.slug + '/download';
            }
        }
        return '#';
    };
});
