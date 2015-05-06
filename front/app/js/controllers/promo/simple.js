var chartBeers;
angular.module('upont')
    .controller('Profile_Simple_Ctrl', ['$scope', 'user', 'foyer', 'clubs', function($scope, user, foyer, clubs) {
        $scope.user = user;
        $scope.foyer = foyer;
        $scope.clubs = clubs;

        // Définition des graphes Highcharts
        var beers = [];
        for(var key in foyer.perBeer) {
            beers.push(eval(foyer.perBeer[key]));
        }
        var liters = [];
        for(key in foyer.stackedLiters) {
            liters.push(eval(foyer.stackedLiters[key]));
        }

        $scope.chartBeers = new Highcharts.Chart({
            chart: {
                renderTo: 'beers',
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: true,
            },
            credits: {
                enabled: false,
            },
            exporting: {
                enabled: false,
            },
            title: {
                text: 'Bières préférées',
            },
            subtitle: {
                text: 'Dis moi ce que tu bois, je te dirai qui tu es...',
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        formatter: function() {
                        return '<b>'+ this.point.name +'</b> : '+ this.y;
                        }
                    }
                }
            },
            series: [{
                type: 'pie',
                name: 'Nombre de bières',
                data: beers
            }]
        });

        $scope.chartLiters = new Highcharts.Chart({
            chart: {
                renderTo: 'liters',
                type: 'area',
            },
            credits: {
                enabled: false,
            },
            exporting: {
                enabled: false,
            },
            title: {
                text: 'Litres ingérés',
            },
            subtitle: {
                text: 'Tss tss...',
            },
            legend: {
                enabled: false
            },
            xAxis: {
                type: 'datetime',
                dateTimeLabelFormats: {
                    month: '%b %e',
                    year: '%b'
                }
            },
            yAxis: {
                title: {
                    text: 'Volume (L)'
                },
                min: 0
            },
            tooltip: {
                pointFormat: 'Volume : <strong>{point.y:,.1f}L</strong>',
                dateTimeLabelFormats: {
                    month: '%b %e',
                    day: '%A %e %B',
                    year: '%b'
                }
            },
            series: [{ name: 'Volume ingéré', data: liters}]
        });
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.promo.simple', {
                url: '/:slug',
                templateUrl: 'views/promo/simple.html',
                controller: 'Profile_Simple_Ctrl',
                resolve: {
                    user: ['$resource', '$stateParams', function($resource, $stateParams) {
                        return $resource(apiPrefix + 'users/:slug').get({
                            slug: $stateParams.slug
                        }).$promise;
                    }],
                    foyer: ['$resource', '$stateParams', function($resource, $stateParams) {
                        return $resource(apiPrefix + 'foyer/statistics/:slug').get({
                            slug: $stateParams.slug
                        }).$promise;
                    }],
                    clubs: ['$resource', '$stateParams', function($resource, $stateParams) {
                        return $resource(apiPrefix + 'users/:slug/clubs').query({
                            slug: $stateParams.slug
                        }).$promise;
                    }]
                },
                data: {
                    title: 'Profil - uPont',
                    top: true
                }
            });
    }]);
