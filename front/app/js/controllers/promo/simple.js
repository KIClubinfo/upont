angular.module('upont')
    .controller('Profile_Simple_Ctrl', ['$scope', 'user', function($scope, user, foyer, clubs) {
        $scope.user = user;
        $scope.foyer = foyer;
        $scope.clubs = clubs;

        // Définition des graphes Highcharts
        var beers = [];
        for(var key in foyer.beers) {
            beers.push({name: key, data: foyer.beers[key].join()});
        }
        $scope.chartBeers = new Highcharts.Chart({
        chart: {
            renderTo: 'consos',
            type: 'spline'
        },
        credits: {
            enabled: false,
        },
        exporting: {
            enabled: false,
        },
        title: {
            text: 'Consommation par produits',
            align: 'left',
            y: 5
        },
        subtitle: {
            text: '',
            align: 'right',
            verticalAlign: 'top',
            y: 5
        },
        legend: {
//             enabled: false
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
                text: 'Nombre de consommations'
            },
            min: 0
        },
        tooltip: {
        dateTimeLabelFormats: {
                    month: '%b %e',
            day: '%A %e %B',
                    year: '%b'
            }
        },
        series: beers
    });

    Highcharts.getOptions().colors = Highcharts.map(Highcharts.getOptions().colors, function(color) {
    return {
        radialGradient: { cx: 0.5, cy: 0.3, r: 0.7 },
        stops: [
        [0, color],
        [1, Highcharts.Color(color).brighten(-0.3).get('rgb')] // darken
        ]
    };
    });
    /*
    RepartitionProduits = new Highcharts.Chart({
        chart: {
                renderTo: 'repartition-produits',
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: true
        },
        credits: {
            enabled: false,
        },
        exporting: {
            enabled: false,
        },
        title: {
            text: 'Vos consommations',
            align: 'left',
            y: 5
        },
        subtitle: {
            text: '',
            align: 'right',
            verticalAlign: 'top',
            y: 5
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
                text: 'Volume en Litres'
            },
            min: 0
        },
    tooltip: {
        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
    },
    plotOptions: {
        pie: {
        allowPointSelect: true,
        cursor: 'pointer',
        dataLabels: {
            color: '#FDFDFF',
            connectorColor: '#E8E8E9',
            formatter: function() {
            return '<b>'+ this.point.name +'</b> : '+ this.y;
            }
        }
        }
    },
        series: [{ type: 'pie', name: 'Consos', data: [";

echo implode(',',$foyer->nbParProduit);
        echo "]}]
    });

    var highchartsOptions = Highcharts.setOptions(Highcharts.theme_gridgray);

    ConsosEmpile = new Highcharts.Chart({
        chart: {
            renderTo: 'consos_empile',
            type: 'area'
        },
        credits: {
            enabled: false,
        },
        exporting: {
            enabled: false,
        },
        title: {
            text: 'Nombre de litres de boisson ingérés',
            align: 'left',
            y: 5
        },
        subtitle: {
            text: 'Tss...',
            align: 'right',
            verticalAlign: 'top',
            y: 5
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
                text: 'Volume en Litres'
            },
            min: 0
        },
        tooltip: {
            pointFormat: 'Volume vendu : <strong>{point.y:,.0f}L</strong>',
            dateTimeLabelFormats: {
                    month: '%b %e',
            day: '%A %e %B',
                    year: '%b'
            }
        },
        series: [{ name: 'Volume ing�r�', data: ["; echo implode(',',$foyer->litresCumules); echo "]}]
    });*/
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
                        return $resource(apiPrefix + 'users/:slug/clubs').get({
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
