describe("Login Interceptor", function() {
    var $httpBackend, $rootScope, $http, $state, $scope, $location;

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
    });

    it('should send the token with every request', function() {
        $rootScope.isLogged = true;
        localStorage.setItem('token_exp', Math.floor(Date.now() / 1000) + 3600);
        localStorage.setItem('token', 'superToken');
        localStorage.setItem('droits', ['ROLE_USER', 'ROLE_ADMIN']);

        $httpBackend.expectGET('/potato', function(headers) {
            return headers.Authorization == 'Bearer superToken';
        }).respond(200, '');
        $http.get('/potato');
        $httpBackend.flush();
    });

    it('should not send a request with an expired token', function() {
        $rootScope.isLogged = true;
        localStorage.setItem('token_exp', Math.floor(Date.now() / 1000) - 3600);
        localStorage.setItem('token', 'superToken');
        localStorage.setItem('droits', ['ROLE_USER', 'ROLE_ADMIN']);
        $http.get('/potato');
    });

    it('should log out upon receiving a 401', function() {
        $rootScope.isLogged = true;
        localStorage.setItem('token_exp', Math.floor(Date.now() / 1000) + 3600);
        localStorage.setItem('token', 'superToken');
        localStorage.setItem('droits', ['ROLE_USER', 'ROLE_ADMIN']);

        $httpBackend.expectGET('/potato').respond(401, '');
        $http.get('/potato');
        $httpBackend.flush();

        expect($rootScope.isLogged).toBe(false);
        expect(localStorage.getItem('token_exp')).toBe(null);
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
