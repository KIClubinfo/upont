angular.module('upont')
    .factory('StorageService', ['$rootScope', function($rootScope) {
        return {
            get: function(key) {
                return localStorage.getItem(key);
            },

            set: function(key, data) {
                localStorage.setItem(key, JSON.stringify(data));
            },

            remove: function(key) {
                localStorage.removeItem(key);
            },

            clearAll: function() {
                localStorage.clear();
            }
        };
    }])
    .factory("isLogged", ["StorageService", function(StorageService) {
        return function() {
            if (StorageService.get('token') && StorageService.get('token_exp') > Math.floor(Date.now() / 1000))
                return true;
            else
                return false;
        };
    }])
    .factory("isAdmin", ["StorageService", function(StorageService) {
        return function() {
            // alert(StorageService.get('droits'));
            if (StorageService.get('droits'))
                return StorageService.get('droits').indexOf("ROLE_ADMIN") != -1;
            else return false;
        };
    }])
    .factory("isModo", ["StorageService", function(StorageService) {
        return function() {
            return StorageService.get('droits').indexOf("ROLE_MODO") != -1;
        };
    }])
    .filter('formatSize', function() {
        return function(size) {
            if (size > 1000000000)
                return Math.floor(size / 10000000) / 100 + ' Go';
            if (size > 1000000)
                return Math.floor(size / 10000) / 100 + ' Mo';
            if (size > 1000)
                return Math.floor(size / 10) / 100 + ' Ko';
            return size + ' Octets';
        };
    })
    .filter('formatDuration', function() {
        return function(duration) {
            if (duration > 3600)
                return Math.floor(duration / 3600) + 'h' + Math.floor((duration % 3600) / 60);
            if (duration > 60){
                var retour = Math.floor(duration / 60) + ' mn';
                if(duration%60 > 0)
                    retour += ' '+duration%60;
                return retour;
            }
            return duration + 's';
        };
    })
    .filter('urlFile', function() {
        return function(url) {
            return apiPrefix + url;
        };
    });
