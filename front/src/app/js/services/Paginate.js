import angular from 'angular';

import {API_PREFIX} from 'upont/js/config/constants';

angular.module('upont').factory('Paginate', [
    '$http',
    '$q',
    '$httpParamSerializer',
    '$rootScope',
    function($http, $q, $httpParamSerializer, $rootScope) {
        const loadData = (paginationData, append) => {
            // On indique qu'on est en train de charger de nouvelles données
            $rootScope.infiniteLoading = true;

            const url = paginationData.url;

            return $http({
                method: 'GET',
                url: API_PREFIX + url + '?' + $httpParamSerializer(paginationData.pagination_params)
            }).then(
                (response) => {
                    if (!append) {
                        paginationData = response.data;
                    } else {
                        const merged = paginationData.data.concat(response.data.data);
                        paginationData = response.data;
                        paginationData.data = merged;
                    }
                    paginationData.url = url;

                    $rootScope.infiniteLoading = false;

                    return paginationData;
                }, () => {
                    $rootScope.infiniteLoading = false;
                }
            );
        };

        return {
            get: function(url, paginationParams) {
                return $http({
                    method: 'GET',
                    url: API_PREFIX + url + '?' + $httpParamSerializer(paginationParams)
                }).then(
                    (response) => {
                        const paginationData = response.data;
                        paginationData.url = url;
                        return paginationData;
                    }
                );
            },

            next: function(paginationData) {
                if ('next_page' in paginationData.pagination_infos) {
                    paginationData.pagination_params.page = paginationData.pagination_infos.next_page;
                    return loadData(paginationData, true);
                }
                else {
                    return $q((resolve, reject) => {
                        reject();
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
