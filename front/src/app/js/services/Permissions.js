angular.module('upont').factory('Permissions', ['StorageService', '$rootScope', 'jwtHelper', '$analytics', function(StorageService, $rootScope, jwtHelper, $analytics) {
    const remove = function() {
        $rootScope.isLogged = false;
        $rootScope.isAdmin = false;
        $rootScope.isAdmissible = false;
        $rootScope.isExterieur = false;
        StorageService.remove('token');
        StorageService.remove('roles');
    };

    // Charge les permissions à partir du token stocké dans le Storage
    const load = function() {
        var token = StorageService.get('token');

        if(!token)
            return;

        if (jwtHelper.isTokenExpired(token)) {
            remove();
            return;
        }

        var userData = jwtHelper.decodeToken(token);
        var username = userData.username;
        var roles = StorageService.get('roles');

        $rootScope.username = username;
        $analytics.setUsername(username);
        Raven.setUserContext({
            username: username
        });

        $rootScope.isAdmin = roles.indexOf('ROLE_ADMIN') != -1;
        $rootScope.isAdmissible = roles.indexOf('ROLE_ADMISSIBLE') != -1;
        $rootScope.isExterieur = roles.indexOf('ROLE_EXTERIEUR') != -1;
        $rootScope.isLogged = true;
    };

    return {
        // Vérifie si l'utilisateur a les roles sur un club
        hasClub: function(slug) {
            if ($rootScope.isAdmin)
                return true;

            if (typeof $rootScope.clubs === "undefined") {
                return false;
            }

            for (var i = 0; i < $rootScope.clubs.length; i++) {
                if ($rootScope.clubs[i].club.slug == slug)
                    return true;
            }
            return false;
        },

        // Vérifie si l'utilisateur a les roles sur un role
        hasRight: function(role) {
            if (StorageService.get('roles') === null)
                return false;
            // Ces rôles là ne doivent pas être répercutés aux admins
            if (role == 'ROLE_EXTERIEUR' || role == 'ROLE_ADMISSIBLE')
                return StorageService.get('roles').indexOf(role) != -1;
            if (StorageService.get('roles').indexOf('ROLE_ADMIN') != -1)
                return true;
            // Le modo a tous les roles sauf ceux de l'admin
            if (StorageService.get('roles').indexOf('ROLE_MODO') != -1 && role != 'ROLE_ADMIN')
                return true;
            return StorageService.get('roles').indexOf(role) != -1;
        },

        load: function() {
            load();
        },

        set: function(token, roles) {
            StorageService.set('token', token);
            StorageService.set('roles', roles);
            load();
        },

        remove: function() {
            remove();
        },

        username: function() {
            return jwtHelper.decodeToken(StorageService.get('token')).username;
        }
    };
}]);
