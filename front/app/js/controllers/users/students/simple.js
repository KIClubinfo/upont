angular.module('upont')
    .controller('Students_Simple_Ctrl', ['$rootScope', '$scope', 'user', 'foyer', 'ponthub', 'clubs', function($rootScope, $scope, user, foyer, ponthub, clubs) {
        $scope.user = user;
        $scope.foyer = foyer;
        $scope.displayFoyer = empty(foyer.error);
        $scope.ponthub = ponthub;
        $scope.displayPonthub = empty(ponthub.error);
        $scope.clubs = clubs;

        if (empty(foyer.error)) {
            // Définition des graphes Highcharts
            var beers = [];
            for(var key in foyer.perBeer) {
                /*jslint evil: true */
                beers.push(eval(foyer.perBeer[key]));
            }
            var liters = [];
            for(key in foyer.stackedLiters) {
                /*jslint evil: true */
                liters.push(eval(foyer.stackedLiters[key]));
            }

            $scope.chartBeers = {
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
            };

            $scope.chartLiters = {
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
            };
        }

        if (empty(ponthub.error)) {
            // Définition des graphes Highcharts
            $scope.chartRepartition = {
                chart: {
                    renderTo: 'repartition',
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
                series: [],
            };
        }
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.students.simple', {
                url: '/:slug',
                templateUrl: 'views/users/students/simple.html',
                controller: 'Students_Simple_Ctrl',
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
                    ponthub: ['$resource', '$stateParams', function($resource, $stateParams) {
                        return $resource(apiPrefix + 'statistics/:slug').get({
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
