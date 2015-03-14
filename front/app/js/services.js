angular.module('upont')
    .factory('StorageService', function() {
        return {
            get: function(key) {
                return localStorage.getItem(key);
            },

            set: function(key, data) {
                if (typeof(data) == 'object')
                    localStorage.setItem(key, JSON.stringify(data));
                else
                    localStorage.setItem(key, data);
            },

            remove: function(key) {
                localStorage.removeItem(key);
            },

            clearAll: function() {
                localStorage.clear();
            }
        };
    })
    .factory('Paginate', ["$resource", "$q", function($resource, $q) {
        return {
            get: function(url){
                  var defered = $q.defer();
                  $resource(apiPrefix + url).query(function(data, headers){
                         defered.resolve({data: data, headers: headers()});
                  }, function(httpResponse){
                         defered.reject(httpResponse);
                  });
                  return defered.promise;
            },

            next: function(load) {
                // On analyse les headers
                // On cherche un lien de la forme </ressource?page=1&limit=100>;rel=self
                var match = load.headers.links.match(/<(.+)>;rel=next/);

                // S'il y a une prochaine page, on la charge
                if (match) {
                    $resource(apiPrefix + match[1]).query(function(data, headers){
                        load = {data: load.data.concat(data), headers: headers()};
                    });
                }
            }
        };
    }])
    .filter('formatSize', function() {
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
    })
    .filter('formatDuration', function() {
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
    })
    .filter('urlFile', function() {
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
    })
    .filter('formatDate', function() {
        return function(date) {
            date = moment.unix(date);
            return ucfirst(date.calendar());
        };
    });
