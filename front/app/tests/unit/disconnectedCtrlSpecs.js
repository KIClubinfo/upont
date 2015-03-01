describe("Disconnected_Ctrl", function() {
    var $httpBackend, $rootScope, $controller, $http, $state, $scope;

    beforeEach(module('upont'));

    beforeEach(inject(function($injector, _$rootScope_, _$controller_, _$http_, _$state_) {
        $httpBackend = $injector.get('$httpBackend');
        $rootScope = _$rootScope_;
        $controller = _$controller_;
        $http = _$http_;
        $state = _$state_;

        $httpBackend.whenPOST('/api/login', function(data) {
            var jsonData = JSON.parse(data);
            return jsonData.username == 'de-boisc' && jsonData.password == '123';
        })
        .respond(200, {
            'token': 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXUyJ9.eyJleHAiOjMwMDMwOTQ4MDAsI'+
            'nVzZXJuYW1lIjoiZGUtYm9pc2MiLCJpcCI6IjEyNy4wLjAuMSIsImlhdCI6IjE0MjUyMzg4'+
            'MTIifQ.g75obLfQmHXC5gsgA1F2SO42NYS4QISBoz5M0LdzdrrEdAMbRMYYUR7AUzU2i-td'+
            'f2SCnFUQx6bXQn3dItxNG8_HJrsIWV31H892KsawQlry6DhovH_jFf97uc7p466_u881ff9'+
            'Qk_qFJdBHP2aou6glArWhGjqFaSI_2ISE3DvDBY8nWKQF1hXP3OaZR7mK4PScFmO2kkAudj'+
            'yNWouaZ5O_oBBEFoMvu68W3-3xuM65ATwN7kgd86ZROIYUC44fAPNcy3cO7Uh3Tvuds_by2'+
            'Risfdh1TF_K0xTyjQudY1q8Z0kf1JdE2KdlMc-_D1346bWncJMElb1pcIWAQ_uZ5g',
            "code": 200,
            "data": {
                "username": "de-boisc",
                "roles": [
                    "ROLE_ADMIN",
                    "ROLE_USER"
                ],
                "first": false
            }
        });
        //Ce token est valable jusqu'en 2065 donc ça va...
        $scope = $rootScope.$new();

        //Cancel the behavior of $state.go(..)
        spyOn($state, 'go').and.callFake(function(state, params) {

        });
    }));

    afterEach(function() {
        $httpBackend.verifyNoOutstandingExpectation();
        $httpBackend.verifyNoOutstandingRequest();

        localStorage.clear();
    });

    it('should not try logging without the inputs being filled', function(){
        var controller = $controller('Disconnected_Ctrl', { $scope: $scope });
        $scope.login('', '');
        $scope.login('de-boisc', '');
        $scope.login('', '123');
    });


    it('should not login successfully with improper informations', function(){
        var controller = $controller('Disconnected_Ctrl', { $scope: $scope });
        $httpBackend.expectPOST('/api/login').respond(401, '');
        $scope.login('wrongLogin', 'wrongPassword');
        $httpBackend.flush();
        expect($rootScope.isLogged).toBe(false);
    });

    it('should be able to fetch the token, then go to the actual home page', function() {
        var controller = $controller('Disconnected_Ctrl', { $scope: $scope });
        $httpBackend.expectPOST('/api/login');
        $scope.login('de-boisc', '123');
        $httpBackend.flush();

        expect($rootScope.isLogged).toBe(true);
        expect($state.go).toHaveBeenCalledWith('home.connected');
    });

    it('should be able to logout', function(){
        $rootScope.logout();
        expect($rootScope.isLogged).toBe(false);
        expect($state.go).toHaveBeenCalledWith('home.disconnected');
    });
});
