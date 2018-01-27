import angular from 'angular';

import {API_PREFIX} from 'upont/js/config/constants';

angular.module('upont').factory('Paginate', [
    '$http',
    '$q',
    '$rootScope',
    function($http, $q, $rootScope) {
        const loadData = (paginationData, append) => {
            // On indique qu'on est en train de charger de nouvelles données
            $rootScope.infiniteLoading = true;

            return $http.get(API_PREFIX + paginationData.url, paginationData.pagination_params).then(function(response) {
                if (!append) {
                    paginationData = response.data;
                } else {
                    const merged = paginationData.data.concat(response.data.data);
                    paginationData = response.data;
                    paginationData.data = merged;
                }

                $rootScope.infiniteLoading = false;

                return paginationData;
            }, function() {
                $rootScope.infiniteLoading = false;
            });
        };

        return {
            get: function(url, limit) {
                return $http.get(API_PREFIX + url, {
                    limit
                }).then(
                    (response) => response.data,
                    () => {
                    }
                );
            },

            next: function(paginationData) {
                if ('next_page' in paginationData.pagination_infos) {
                    paginationData.pagination_params.page = paginationData.pagination_infos.next_page;
                    return loadData(paginationData, true);
                }
                else {
                    return $q((resolve) => {
                        resolve(paginationData);
                    });
                }
            },

            first: function(paginationData) {
                paginationData.pagination_params.page = paginationData.pagination_infos.first_page;
                return loadData(paginationData);
            }
        };
    }
]);
