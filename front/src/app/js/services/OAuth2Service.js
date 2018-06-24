import { UserManager, WebStorageStateStore } from 'oidc-client';

import { API_PREFIX } from 'upont/js/config/constants';

/* @ngInject */
export class OAuth2Service {
    constructor($http, AuthService) {
        this.$http = $http;
        this.AuthService = AuthService;

        this.userManager = new UserManager(getClientSettings());
    }

    startAuthentication() {
        return this.userManager.signinRedirect();
    }

    async completeAuthentication() {
        const oidcUser = await this.userManager.signinRedirectCallback();

        const response = await this.$http.post(API_PREFIX + 'login', {
            // access_token: oidcUser.access_token,
            username: 'trancara',
            password: 'password',
        });

        return this.AuthService.setUserFromToken(response.data.token);
    }
}

function getClientSettings() {
    return {
        authority: 'http://localhost:4444',
        client_id: 'upont-front-dev',
        redirect_uri: 'http://localhost:8080/oauth2/callback',
        post_logout_redirect_uri: 'http://localhost:8080/',
        response_type: 'id_token token',
        scope: 'openid profile',
        filterProtocolClaims: false,
        loadUserInfo: false,
        userStore: new WebStorageStateStore({store: window.localStorage || window.sessionStorage}),
    };
}
