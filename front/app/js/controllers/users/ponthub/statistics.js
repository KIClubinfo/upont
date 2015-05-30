angular.module('upont')
    .controller('Ponthub_Statistics_Ctrl', ['$scope', 'ponthub', function($scope, ponthub) {
        var points = [],
        region_p,
        region_val,
        region_i,
        category_p,
        category_i;
        var data = ponthub.ponthub;

        region_i = 0;
        for (var region in data) {
            region_val = 0;
            region_p = {
                id: 'id_' + region_i,
                name: region,
                value: 1
            };
            category_i = 0;
            for (var category in data[region]) {
                category_p = {
                    id: region_p.id + '_' + category_i,
                    name: category,
                    parent: region_p.id,
                    color: Highcharts.getOptions().colors[region_i],
                    value: Math.round(data[region][category])
                };
                points.push(category_p);
                category_i++;
            }
            //region_p.value = Math.round(region_val / category_i);
            points.push(region_p);
            region_i++;
        }

        $scope.chartPonthub = {
            chart: {
                renderTo: 'ponthub',
            },
            credits: {
                enabled: false,
            },
            exporting: {
                enabled: false,
            },
            title: {
                text: 'Catalogue PontHub'
            },
            subtitle: {
                text: 'Les totaux des totaux',
            },
            series: [{
                type: 'treemap',
                layoutAlgorithm: 'stripes',
                alternateStartingDirection: true,
                levels: [{
                    level: 1,
                    layoutAlgorithm: 'sliceAndDice',
                    dataLabels: {
                        enabled: true,
                        align: 'left',
                        verticalAlign: 'top',
                        style: {
                            fontSize: '15px',
                            fontWeight: 'bold'
                        }
                    }
                }],
                data: points
            }]
        };

        var categories = ponthub.years.categories;
        $scope.chartYears = {
            chart: {
                renderTo: 'years',
                type: 'bar'
            },
            credits: {
                enabled: false,
            },
            exporting: {
                enabled: false,
            },
            title: {
                text: 'Répartition par années'
            },
            subtitle: {
                text: 'Oldies but goldies',
            },
            xAxis: [{
                categories: categories,
                reversed: false,
                labels: {
                    step: 1
                }
            }, { // mirror axis on right side
                opposite: true,
                reversed: false,
                categories: categories,
                linkedTo: 0,
                labels: {
                    step: 1
                }
            }],
            yAxis: {
                title: {
                    text: 'Nombre de fichiers'
                },
                labels: {
                    formatter: function () {
                        return Math.abs(this.value);
                    }
                },
                min: ponthub.years.min,
                max: ponthub.years.max
            },

            plotOptions: {
                series: {
                    stacking: 'normal'
                }
            },

            tooltip: {
                formatter: function () {
                    return '<b>' + this.series.name + ' de ' + this.point.category + '</b><br/>' +
                    Highcharts.numberFormat(Math.abs(this.point.y), 0) + ' fichier' +
                    (this.point.y > 1 ? 's' : '');
                }
            },
            series: ponthub.years.series
        };
    }]);
