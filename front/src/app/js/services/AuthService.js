import {UserManager, WebStorageStateStore} from 'oidc-client';
import alertify from 'alertifyjs';
import Raven from 'raven-js';

export class UserProfile {
    constructor(user) {
        this.user = user;
    }

    static fromOidcUser(user) {
        return new UserProfile(user);
    }

    get isExpired() {
        return this.user.expired || false;
    }

    get username() {
        return this.user.profile.sub;
    }

    get accessToken() {
        return this.user.access_token;
    }

    get idToken() {
        return this.user.id_token;
    }

    get isJardinier() {
        return this.user.scopes.includes('upont:jardinier');
    }

    get isStudent() {
        return this.user.scopes.includes('upont:student');
    }
}

/* @ngInject */
export class AuthService {
    constructor($rootScope, $analytics) {
        // Dependency Injection
        this.$rootScope = $rootScope;
        this.$analytics = $analytics;

        this.userManager = new UserManager(getClientSettings());

        this.user = null;

        this.userManager.events.addUserLoaded((user) => {
            this._setUser(user);
        });

        this.userManager.events.addUserUnloaded(() => {
            this._resetUser();
        });
    }

    loadUser() {
        return this.userManager.getUser().then((user) => {
            if(user) {
                return this._setUser(user);
            }
            else {
                return null;
            }
        }, (error) => {
            console.warn(error);
            return null;
        });
    }

    getUser() {
        return this.isLoggedIn() ? this.user : null;
    }

    isLoggedIn() {
        return this.user != null && !this.user.isExpired;
    }

    startAuthentication() {
        return this.userManager.signinRedirect();
    }

    completeAuthentication() {
        return this.userManager.signinRedirectCallback().then(() => {
            return this.getUser();
        }, () => {
            this.logout();
            return Promise.reject();
        });
    }

    logout() {
        this.userManager.removeUser();
    }

    // Private functions
    _setUser(user) {
        console.log(user);

        user = UserProfile.fromOidcUser(user);
        this.user = user;
        this.$rootScope.user = user;

        const username = user.username;
        this.$analytics.setUsername(username);
        Raven.setUserContext({
            username,
        });

        return this.user;
    }

    _resetUser() {
        this.user = null;
        this.$rootScope.user = null;

        this.$analytics.setUsername('');
        Raven.setUserContext(null);
    }

}

export function getClientSettings() {
    return {
        authority: 'http://localhost:4444',
        client_id: 'upont-front-dev',
        redirect_uri: 'http://localhost:8080/oauth2/callback',
        post_logout_redirect_uri: 'http://localhost:8080/',
        response_type: "id_token token",
        scope: "openid profile upont",
        filterProtocolClaims: true,
        loadUserInfo: true,
        userStore: new WebStorageStateStore({ store: window.localStorage || window.sessionStorage }),
    };
}