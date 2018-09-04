import angular from 'angular';

angular.module('upont').run(['$rootScope', 'StorageService', function($rootScope, StorageService) {
    $rootScope.themes = [
        {value: 'classic',      name: 'Classic'},
        {value: 'classic-dark', name: 'Aqua'},
        {value: 'grey-dark',    name: 'Techno'},
        {value: 'grey',         name: 'Work Hard'},
        {value: 'grey-green',   name: 'Matrix'},
        {value: 'grey-red',     name: 'Rubis'},
        {value: 'grey-yellow',  name: 'Bees'},
        {value: 'green',        name: 'Safari'},
        {value: 'brown',        name: 'Pure Choco'},
        {value: 'brown-dark',   name: 'Dark Choco'},
        {value: 'orange',       name: 'Magma'},
        {value: 'violet-dark',  name: 'Intense AV'},
    ];

    // On corrige le tir si le theme n'existe pas
    const userTheme = StorageService.get('theme');
    let themeExists = false;

    for (const theme of $rootScope.themes) {
        if (userTheme === theme.value) {
            themeExists = true;
        }
    }

    if (!themeExists) {
        StorageService.set('theme', 'classic');
    }
    $rootScope.theme = StorageService.get('theme');

    // Switch de thème
    $rootScope.switchTheme = function(theme) {
        StorageService.set('theme', theme);
        $rootScope.theme = theme;
    };
}]);
