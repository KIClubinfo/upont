// À partir d'un objet user, donne son acronyme
module.filter('acronyme', function () {
    return function(user)
    {
        var r = '';
        var string = (user.first_name + ' ' + user.last_name).split(' ');
        for(var key in string)
            r += string[key][0];
        return (r + '\'' + user.promo).toUpperCase();
    };
});
