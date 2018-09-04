import angular from 'angular';

angular.module('upont').filter('trustAsHtml', ['$sce', function($sce) {
    return $sce.trustAsHtml;
}]);
