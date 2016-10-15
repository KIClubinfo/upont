angular.module('upont')
    .controller('Ponthub_Modify_Ctrl', ['$scope', '$state', '$stateParams', 'Ponthub', '$http', 'element', function($scope, $state, $stateParams, Ponthub, $http, element) {
        $scope.init = function(element) {
            $scope.element = element;
            $scope.element.tags = $scope.element.tags.join();
            $scope.element.genres = $scope.element.genres.join();
            if ($scope.element.actors_list)
                $scope.element.actors_list = $scope.element.actors_list.join();
        };
        $scope.init(element);

        $scope.category = $stateParams.category;
        $scope.type = Ponthub.cat($stateParams.category);
        $scope.propositions = [];

        $scope.search = function(criteria) {
            if ($scope.type == 'movies' || $scope.type == 'series') {

                criteria = criteria.replace(/ \([0-9]{4}\)/, '');

                $http.post(apiPrefix + 'imdb/search', {name: criteria}).success(function(data){
                    $scope.propositions = data;

                    if (data.length > 0) {
                        $scope.proposition = data[0].id;
                    } else {
                        alertify.error('Aucun résultat trouvé !');
                    }
                });
            }
        };

        $scope.fill = function(proposition) {
            if ($scope.type == 'movies' || $scope.type == 'series') {
                if (!proposition.length) {
                    alertify.error('Aucun film recherché !');
                } else {
                    $http.post(apiPrefix + 'imdb/infos', {id: proposition}).success(function(data){
                        $scope.element.name = data.title;
                        $scope.element.year = data.year;
                        $scope.element.duration = data.duration;
                        $scope.element.genres = data.genres.join();
                        $scope.element.actors_list = data.actors.join();
                        $scope.element.director = data.director;
                        $scope.element.description = data.description;
                        $scope.element.rating = data.rating;
                        $scope.imageUrl = data.image;
                    });
                }
            }
        };

        $scope.submitFile = function(element, imageUrl, imageBase64) {
            var params = {
                'name' : element.name,
                'description' : element.description,
            };

            var genres = [];
            var actors = [];
            var tags = [];
            var list = [];
            var i = 0;

            params.tags = element.tags;
            params.genres = element.genres;

            if (imageUrl !== '') {
                params.image = imageUrl;
            }
            // On donne la priorité à l'image par upload de fichier si elle est remplie
            if (imageBase64) {
                params.image = imageBase64.base64;
            }

            switch ($scope.type) {
                case 'movies':
                case 'series':
                    params.actors = element.actors_list;
                    params.year = element.year;
                    params.duration = element.duration;
                    params.director = element.director;
                    if (element.rating !== '' && element.rating != 'N/A')
                        params.rating = element.rating;
                    break;
                case 'games':
                    params.year = element.year;
                    params.studio = element.studio;
                    break;
                case 'softwares':
                    params.year = element.year;
                    params.author = element.author;
                    params.version = element.version;
                    break;
                case 'others':
                    break;
            }

            $http.patch(apiPrefix + $scope.type + '/' + element.slug, params).success(function(data){
                $state.go('root.users.ponthub.category.list', {category: $stateParams.category});
                alertify.success('Modifications prises en compte !');
            });
        };
    }])
;
