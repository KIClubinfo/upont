module.factory('Paginate', ['$resource', '$q', '$rootScope', function($resource, $q, $rootScope) {
    return {
        get: function(link, limit) {
            $rootScope.infiniteLoading = true;
            var suffix = '';
            if (limit > 0) {
                suffix = link.match(/\?/) === null ? '?' : '&';
                suffix += 'limit=' + limit;
            }
            var defered = $q.defer();

            $resource(url + '/' + link + suffix).query(function(data, headers){
                defered.resolve({data: data, headers: headers()});
                $rootScope.infiniteLoading = false;
            }, function(httpResponse){
                defered.reject(httpResponse);
                $rootScope.infiniteLoading = false;
            });
            return defered.promise;
        },

        next: function(load) {
            // On indique qu'on est en train de charger de nouvelles données
            $rootScope.infiniteLoading = true;

            // On analyse les headers
            // On cherche un lien de la forme </ressource?page=1&limit=100>;rel=next
            var match = load.headers.links.match(/last,<(\/.*?)>;rel=next/);
            var defered = $q.defer();

            // S'il y a une prochaine page, on la charge
            if (match) {
                $resource(url + match[1]).query(function(data, headers){
                    defered.resolve({data: load.data.concat(data), headers: headers()});
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
        }
    };
}]);
