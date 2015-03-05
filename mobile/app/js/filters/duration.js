// Transforme des secondes en une durée lisible

module.filter('duration', function(){
    return function(duration)
    {
        var result = '';
        var minutes = Math.floor((duration%3600)/60);
        if(duration > 3600) {
            result += Math.floor(duration/3600)+'h';
            if(minutes !== 0) {
                result += minutes > 9 ? minutes : '0' + minutes;
            }
            return result;
        }
        if(duration > 60) {
            return minutes+'min';
        }
        return duration+'s';
    };
});
