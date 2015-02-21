// Applique un suffixe de position à un nombre

module.filter('position', function () {
    return function(position)
    {
        return position == 1 ? '1ère' : position + 'ère';
    };
});
