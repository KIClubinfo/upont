import Highcharts from 'highcharts';

import * as HighchartsMore from 'highcharts/highcharts-more';
import Funnel from 'highcharts/modules/funnel';

// Register the "more" module into Highcharts
HighchartsMore(Highcharts);
Funnel(Highcharts);

/* @ngInject */
class Students_Simple_Ctrl {
    constructor($rootScope, $scope, $resource, $http, user, foyer, ponthub, clubs, achievements) {
        $scope.user = user;
        $scope.foyer = foyer;
        $scope.displayFoyer = user.stats_foyer;
        $scope.ponthub = ponthub;
        $scope.displayPonthub = user.stats_ponthub && empty(ponthub.error) && $rootScope.isStudentNetwork;
        $scope.clubs = clubs;
        $scope.achievements = achievements;

        if ($scope.displayFoyer) {
            // Définition des graphes Highcharts
            let beers = [];
            for(const key in foyer.beersDrunk) {
                beers.push([foyer.beersDrunk[key].beer.name, foyer.beersDrunk[key].count]);
            }
            let liters = [];
            for(const key in foyer.stackedLiters) {
                liters.push([key*1000, foyer.stackedLiters[key]]);
            }

            $scope.chartBeers = {
                chart: {
                    renderTo: 'beers',
                    backgroundColor:'rgba(255, 255, 255, 0)',
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
            };

            $scope.chartLiters = {
                chart: {
                    renderTo: 'liters',
                    backgroundColor:'rgba(255, 255, 255, 0)',
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
            };
        }

        if ($scope.displayPonthub) {
            // Définition des graphes Highcharts
            $scope.chartRepartition = {
                chart: {
                    renderTo: 'repartition',
                    backgroundColor:'rgba(255, 255, 255, 0)',
                    type: 'pyramid',
                },
                credits: {
                    enabled: false,
                },
                exporting: {
                    enabled: false,
                },
                title: {
                    text: 'Répartition des téléchargements',
                },
                subtitle: {
                    text: 'Adepte de séries ou gamer ?',
                },
                plotOptions: {
                    series: {
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b> ({point.y:,.0f})',
                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black',
                            softConnector: true,
                        }
                    }
                },
                legend: {
                    enabled: false,
                },
                series: [{name: 'Téléchargements', data: ponthub.repartition}],
            };

            $scope.chartTimeline = {
                chart: {
                    renderTo: 'timeline',
                    backgroundColor:'rgba(255, 255, 255, 0)',
                    type: 'area',
                    zoomType: 'xy',
                },
                credits: {
                    enabled: false,
                },
                exporting: {
                    enabled: false,
                },
                title: {
                    text: 'Téléchargements cumulés'
                },
                subtitle: {
                    text: '= niveau d\'oisiveté',
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
                        text: 'Téléchargements'
                    },
                },
                plotOptions: {
                    area: {
                        stacking: 'normal',
                        lineColor: '#666666',
                        lineWidth: 1,
                        marker: {
                            lineWidth: 1,
                            lineColor: '#666666'
                        }
                    }
                },
                tooltip: {
                    shared: true,
                    valueSuffix: ' téléchargements'
                },
                series: ponthub.timeline,
            };

            $scope.chartHipster = {
                chart: {
                    renderTo: 'hispter',
                    backgroundColor:'rgba(255, 255, 255, 0)',
                    type: 'gauge',
                    plotBackgroundColor: null,
                    plotBackgroundImage: null,
                    plotBorderWidth: 0,
                    plotShadow: false
                },
                title: {
                    text: 'Hipsteromètre'
                },
                subtitle: {
                    text: 'Plus tu télécharges des fichiers que personne ne va chercher, plus t\'es bon',
                },
                credits: {
                    enabled: false,
                },
                exporting: {
                    enabled: false,
                },
                pane: {
                    startAngle: -150,
                    endAngle: 150,
                    background: [{
                        backgroundColor: {
                            linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                            stops: [
                                [0, '#FFF'],
                                [1, '#333']
                            ]
                        },
                        borderWidth: 0,
                        outerRadius: '109%'
                    }, {
                        backgroundColor: {
                            linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                            stops: [
                                [0, '#333'],
                                [1, '#FFF']
                            ]
                        },
                        borderWidth: 1,
                        outerRadius: '107%'
                    }, {
                        // default background
                    }, {
                        backgroundColor: '#DDD',
                        borderWidth: 0,
                        outerRadius: '105%',
                        innerRadius: '103%'
                    }]
                },

                yAxis: {
                    min: 0,
                    max: 200,

                    minorTickInterval: 'auto',
                    minorTickWidth: 1,
                    minorTickLength: 10,
                    minorTickPosition: 'inside',
                    minorTickColor: '#666',

                    tickPixelInterval: 30,
                    tickWidth: 2,
                    tickPosition: 'inside',
                    tickLength: 10,
                    tickColor: '#666',
                    labels: {
                        step: 2,
                        rotation: 'auto'
                    },
                    title: {
                        text: 'HpStR'
                    },
                    plotBands: [{
                        from: 0,
                        to: 120,
                        color: '#55BF3B' // green
                    }, {
                        from: 120,
                        to: 160,
                        color: '#DDDF0D' // yellow
                    }, {
                        from: 160,
                        to: 200,
                        color: '#DF5353' // red
                    }]
                },
                series: [{
                    name: 'Hipsteritude',
                    data: [ponthub.hipster],
                    tooltip: {
                        valueSuffix: ' HpStR'
                    }
                }]
            };
        }
    }
}

export default Students_Simple_Ctrl;
