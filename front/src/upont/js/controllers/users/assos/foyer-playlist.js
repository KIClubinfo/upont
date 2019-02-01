import alertify from 'alertifyjs';

import { API_PREFIX } from 'upont/js/config/constants';

/* @ngInject */
class Assos_FoyerPlaylist_Ctrl {
    constructor($scope, $rootScope, $http, youtube, stats, Paginate) {
        $scope.youtube = youtube;
        $scope.stats = stats;
        $scope.predicate = 'volume';
        $scope.reverse = true;

        $scope.reload = function() {
            Paginate.first($scope.youtube).then(data => {
                $scope.youtube = data;
            });
        };

        $scope.post = function(link) {
            if (!link.match(/^(https?:\/\/)?(www\.youtube\.com|youtu\.?be)\/.+$/)) {
                alertify.error('Ce n\'est pas une vidéo YouTube !');
                return;
            }

            $http.post(API_PREFIX + 'youtubes', {name: 'Lien Youtube Foyer', link: link}).then(() => {
                $scope.link = '';
                alertify.success('Yeah !');
                $scope.reload();
            });
        };

        $scope.delete = function(youtube) {
            alertify.confirm('Veux-tu vraiment faire ça ?', () => {
                $http.delete(API_PREFIX + 'youtubes/' + youtube.slug).then(() => {
                    $scope.reload();
                });
            });
        };
    }
}

export default Assos_FoyerPlaylist_Ctrl;
