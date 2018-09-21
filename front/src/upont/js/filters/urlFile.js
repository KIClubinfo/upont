import angular from 'angular';

import { API_PREFIX } from 'upont/js/config/constants';

angular.module('upont').filter('urlFile', function() {
    return function(input, inputParent) {
        if (input === null || input === undefined) {
            return '/img/default-user.png';
        }

        if (typeof(input) === 'string') {
            return API_PREFIX + input;
        }

        if (typeof(input) === 'object') {
            switch (input.type) {
            case 'movie':
                return API_PREFIX + 'movies/' + input.slug + '/download';
            case 'game':
                return API_PREFIX + 'games/' + input.slug + '/download';
            case 'software':
                return API_PREFIX + 'softwares/' + input.slug + '/download';
            case 'other':
                return API_PREFIX + 'others/' + input.slug + '/download';
            case 'episode':
                if (inputParent && typeof(inputParent) === 'object' && inputParent.type === 'serie') {
                    return API_PREFIX + 'series/' + inputParent.slug + '/episodes/' + input.slug + '/download';
                }
                break;
            case 'exercice':
                if (inputParent && typeof(inputParent) === 'object' && inputParent.type === 'course') {
                    return API_PREFIX + 'courses/' + inputParent.slug + '/exercices/' + input.slug + '/download';
                }
            }
        }

        return '#';
    };
});
