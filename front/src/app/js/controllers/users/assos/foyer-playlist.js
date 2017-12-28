import alertify from 'alertifyjs';

import { API_PREFIX } from 'upont/js/config/constants';

class Assos_FoyerPlaylist_Ctrl {
    constructor($scope, $rootScope, $http, youtube, stats, members, Paginate) {
        $scope.youtube = youtube;
        $scope.stats = stats;
        $scope.predicate = 'liters';
        $scope.reverse = true;
        $scope.isFromFoyer = false;

        for (var key in members) {
            if (members[key].user !== undefined && members[key].user.username == $rootScope.username) {
                $scope.isFromFoyer = true;
            }
        }

        $scope.reload = function() {
            Paginate.first($scope.youtube).then(function(response){
                $scope.youtube = response.data;
            });
        };

        $scope.post = function(link) {
            if (!link.match(/^(https?:\/\/)?(www\.youtube\.com|youtu\.?be)\/.+$/)) {
                alertify.error('Ce n\'est pas une vidéo YouTube !');
                return;
            }

            $http.post(API_PREFIX + 'youtubes', {name: 'Lien Youtube Foyer', link: link}).then(function(){
                $scope.link = '';
                alertify.success('Yeah !');
                $scope.reload();
            });
        };

        $scope.delete = function(youtube) {
            alertify.confirm('Veux-tu vraiment faire ça ?', function(e) {
                if (e) {
                    $http.delete(API_PREFIX + 'youtubes/' + youtube.slug).then(function(){
                        $scope.reload();
                    });
                }
            });
        };
    }
}

export default Assos_FoyerPlaylist_Ctrl;
