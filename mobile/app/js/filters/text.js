// Applique la fonction nl2br au texte
var allowedTags = '<a><br><strong><small><ul><ol><li><pre><i>';
module.filter('text', ['$sce', '$filter', function ($sce, $filter) {
    return function(text)
    {
        return text ? $sce.trustAsHtml($filter('stripTags')(text, allowedTags)) : '';
    };
}]);
