angular.module('upont').filter('promoFilter', function() {
    // Filtre spécial qui renvoie les membres selon une année précise
    // En effet, les respos 2A sont d'une année différente
    return function(members, year) {
        var results = [];
        for (const member of members) {
            // Pas de xor en javasale...
            const isRightYear = member.user.promo == year;
            const isYearBefore = member.user.promo == year - 1;
            const isRespo2A = member.role.match(/2A/gi);

            if (
                (isRightYear && !isRespo2A) ||
                (isYearBefore && isRespo2A)
            ) {
                results.push(member);
            }
        }

        return results;
    };
});
