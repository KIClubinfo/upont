describe("HTTP Interceptors", function() {
    var $httpBackend, $rootScope, $http, $state, $scope, $location;

    var superToken = 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXUyJ9.eyJleHAiOjMwMDMwOTQ4MD'+
    'AsInVzZXJuYW1lIjoiZGUtYm9pc2MiLCJpcCI6IjEyNy4wLjAuMSIsImlhdCI6IjE0MjUyMzg4MT'+
    'IifQ.g75obLfQmHXC5gsgA1F2SO42NYS4QISBoz5M0LdzdrrEdAMbRMYYUR7AUzU2i-tdf2SCnFU'+
    'Qx6bXQn3dItxNG8_HJrsIWV31H892KsawQlry6DhovH_jFf97uc7p466_u881ff9Qk_qFJdBHP2a'+
    'ou6glArWhGjqFaSI_2ISE3DvDBY8nWKQF1hXP3OaZR7mK4PScFmO2kkAudjyNWouaZ5O_oBBEFoM'+
    'vu68W3-3xuM65ATwN7kgd86ZROIYUC44fAPNcy3cO7Uh3Tvuds_by2Risfdh1TF_K0xTyjQudY1q'+
    '8Z0kf1JdE2KdlMc-_D1346bWncJMElb1pcIWAQ_uZ5g',
    mauvaisToken = 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXUyJ9.eyJleHAiOjExMDk2Mzg4MDAs'+
    'InVzZXJuYW1lIjoiZGUtYm9pc2MiLCJpcCI6IjEyNy4wLjAuMSIsImlhdCI6IjE0MjUyMzkzMTEi'+
    'fQ.EEHofoo_6U6FBqRP4WIibgYbTC_XL3sbVKbOUq2FPifAeuQdQxb1u_l2uTMCRcdwyL34TAOGL'+
    'ELT5HE_qZExeoG4hK6JzQcz8fwK3WRn8HdC_ZWbPaNEVKlkjx6d9bKCKxj0KSJvB88jpEAcBCYtF'+
    'VfpU8OSepFR4jRD3vhYfgphuBHRvEwCoIrdKo6N6z1eHYMunSxQeKymKS8232Fr06dkAIvYWImDq'+
    'GcfCldQE3SXXQlE6ul0SliJpQhazC4c_JH1A-VoGXrrXZCdKRG-OHp7p3Szx61YXqW6TkzJL9Dbb'+
    'T3zvopyIhrnedg04W5mp68Vg3EbcyhhG1Fw8y348g';


    beforeEach(module('upont'));

    beforeEach(inject(function($injector, _$rootScope_, _$http_, _$state_, _$location_) {
        $httpBackend = $injector.get('$httpBackend');
        $rootScope = _$rootScope_;
        $http = _$http_;
        $state = _$state_;
        $location = _$location_;

        $scope = $rootScope.$new();

        //Cancel the behavior of $state.go(..)
        spyOn($state, 'go').and.callFake(function(state, params) {});
        spyOn($location, 'path').and.callFake(function(state, params) {});
    }));

    afterEach(function() {
        $httpBackend.verifyNoOutstandingExpectation();
        $httpBackend.verifyNoOutstandingRequest();

        localStorage.clear();
    });

    it('should send the token with every request', function() {
        $rootScope.isLogged = true;
        localStorage.setItem('token', superToken);
        localStorage.setItem('droits', ['ROLE_USER', 'ROLE_ADMIN']);

        $httpBackend.expectGET('/potato', function(headers) {
            return headers.Authorization == 'Bearer '+superToken;
        }).respond(200, '');
        $http.get('/potato');
        $httpBackend.flush();
    });

    it('should not send a request with an expired token', function() {
        $rootScope.isLogged = true;
        localStorage.setItem('token', mauvaisToken);
        localStorage.setItem('droits', ['ROLE_USER', 'ROLE_ADMIN']);
        $http.get('/potato');
    });

    it('should log out upon receiving a 401', function() {
        $rootScope.isLogged = true;
        localStorage.setItem('token', superToken);
        localStorage.setItem('droits', ['ROLE_USER', 'ROLE_ADMIN']);

        $httpBackend.expectGET('/potato').respond(401, '');
        $http.get('/potato');
        $httpBackend.flush();

        expect($rootScope.isLogged).toBe(false);
        expect(localStorage.getItem('token')).toBe(null);
        expect(localStorage.getItem('droits')).toBe(null);
    });

    it('should redirect after a 500', function() {
        $httpBackend.expectGET('/potato').respond(500, '');
        $http.get('/potato');
        $httpBackend.flush();
        expect($location.path).toHaveBeenCalledWith('/erreur');
    });

    it('should redirect after a 404', function() {
        $httpBackend.expectGET('/potato').respond(404, '');
        $http.get('/potato');
        $httpBackend.flush();
        expect($location.path).toHaveBeenCalledWith('/404');
    });

    it('should redirect after a 503', function() {
        expect(localStorage.getItem('maintenance')).toBe(null);

        $httpBackend.expectGET('/potato').respond(503, '');
        $http.get('/potato');
        $httpBackend.flush();
        expect($location.path).toHaveBeenCalledWith('/maintenance');
        expect(localStorage.getItem('maintenance')).toBe(null);

        $httpBackend.expectGET('/potato').respond(503, { until : 42 });
        $http.get('/potato');
        $httpBackend.flush();
        expect($location.path).toHaveBeenCalledWith('/maintenance');
        expect(localStorage.getItem('maintenance')).toBe('42');
    });
});
