import Raven from 'raven-js';

import { API_PREFIX } from 'upont/js/config/constants';

/* @ngInject */
export class AuthService {
    constructor($rootScope, $analytics, StorageService, jwtHelper) {
        // Dependency Injection
        this.$rootScope = $rootScope;
        this.$analytics = $analytics;
        this.StorageService = StorageService;
        this.jwtHelper = jwtHelper;

        this.user = null;
    }

    loadUser() {
        const token = this.StorageService.get('token');

        if (token) {
            this.setUserFromToken(token);
        }
    }

    getUser() {
        return this.isLoggedIn() ? this.user : null;
    }

    isLoggedIn() {
        return this.user != null && !this.user.isExpired();
    }

    getAccessToken() {
        return this.user != null ? this.user.accessToken : null;
    }

    logout() {
        this.resetUser();
        this.StorageService.remove('token');
    }

    setUserFromToken(jwtToken) {
        this.StorageService.set('token', jwtToken);

        const user = new UserAuth(
            jwtToken,
            this.jwtHelper.decodeToken(jwtToken),
        );

        this.user = user;
        this.$rootScope.user = user;

        const username = user.username;
        this.$analytics.setUsername(username);
        Raven.setUserContext({
            username,
        });

        return this.user;
    }

    resetUser() {
        this.user = null;
        this.$rootScope.user = null;

        this.$analytics.setUsername('');
        Raven.setUserContext(null);
    }

}

export class UserAuth {
    constructor(jwtToken, decodedToken) {
        this.jwtToken = jwtToken;
        this.decodedToken = decodedToken;
    }

    isExpired() {
        const exp = new Date(0);
        exp.setUTCSeconds(this.decodedToken.exp);

        return exp <= new Date();
    }

    get username() {
        return this.decodedToken.username;
    }

    get accessToken() {
        if (this.isExpired()) {
            throw new Error('Expired access token');
        }

        return this.jwtToken;
    }

    isJardinier() {
        return this.hasRole('ROLE_JARDINIER');
    }

    isStudent() {
        return this.hasRole('ROLE_STUDENT');
    }

    isAdmin() {
        return this.hasRole('ROLE_ADMIN');
    }

    hasRole(role) {
        const roles = this.decodedToken.roles;

        // Ces rôles là ne doivent pas être répercutés aux admins
        if (role === 'ROLE_EXTERIEUR' || role === 'ROLE_ADMISSIBLE')
            return roles.indexOf(role) !== -1;

        if (roles.indexOf('ROLE_ADMIN') !== -1)
            return true;

        // Le modo a tous les roles sauf ceux de l'admin
        if (roles.indexOf('ROLE_MODO') !== -1 && role !== 'ROLE_ADMIN')
            return true;

        return roles.indexOf(role) !== -1;
    }
}
