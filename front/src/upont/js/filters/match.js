import angular from 'angular';
// Mets en valeur les bouts de mots trouvés dans la chaîne filtrée

angular.module('upont').filter('match', function(){
    return function(input, string){
        var searches = string.split(' ');
        var reg = null;

        for (var i = 0; i < searches.length; i++){
            reg = new RegExp('(' + searches[i].replace(/([.?*+^$[\]\\(){}|-])/g, '\\$1') + ')', 'gi');
            input = input.replace(reg, '<strong>$1</strong>');
        }

        return input;
    };
});
