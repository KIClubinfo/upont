angular.module('upont').factory('Paginate', ['$resource', '$q', '$rootScope', function($resource, $q, $rootScope) {
    loadData = function(load, url, append) {
        // On indique qu'on est en train de charger de nouvelles données
        $rootScope.infiniteLoading = true;
        var defered = $q.defer();

        // S'il y a une page, on la charge
        if (url) {
            $resource(apiPrefix + url[1]).query(function(data, headers){
                var result;
                if (!append) {
                    result = data;
                } else {
                    result = load.data.concat(data);
                }

                defered.resolve({data: result, headers: headers()});
                $rootScope.infiniteLoading = false;
            }, function(httpResponse){
                 defered.reject(httpResponse);
                 $rootScope.infiniteLoading = false;
            });

        } else {
            defered.reject();
            $rootScope.infiniteLoading = false;
        }
        return defered.promise;
    };

    return {
        get: function(url, limit) {
            var suffix = '';
            if (limit > 0) {
                suffix = url.match(/\?/) === null ? '?' : '&';
                suffix += 'limit=' + limit;
            }
            var defered = $q.defer();

            $resource(apiPrefix + url + suffix).query(function(data, headers){
                defered.resolve({data: data, headers: headers()});
            }, function(httpResponse){
                defered.reject(httpResponse);
            });
            return defered.promise;
        },

        next: function(load) {
            return loadData(load, load.headers.links.match(/self,<\/(.*?)>;rel=next/), true);
        },

        first: function(load) {
            return loadData(load, load.headers.links.match(/<\/(.*?)>;rel=first/));
        }
    };
}]);
