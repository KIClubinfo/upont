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
            "token": "eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXUyJ9.eyJleHAiOjE0MjUyNTgwMDAsInVzZXJuYW1lIjoiZGUtYm9pc2MiLCJpcCI6IjEyNy4wLjAuMSIsImlhdCI6IjE0MjQ2NTI3NDUifQ.j5OykI6mxltE0Hlb-8_GrdQt4hU6FHT82B9LZJUUWorPl0qoj9MkLTgEboPBQyzp7avUZCBMXVGbxiwZSaMSIcGA27yBaFWRvRmwW1yLRTYCnO6xPsaCbpDW7GP_7S7NTgB3h5xqolZ0xmuoghmn1Fu-QPDrGr81fzFd2gKthFSMPCUBFvx-mFcCyfVkYwK1L3gEdsvNhZeeH2GhrCNLriA2g7ZgAM8x1tHmsaKFpwU9FPaNIhgV-6KrR_UCh2MtMqRv97eEHPOZPFLRcq8y2YJ6VnBT9LlEcRb9DyYI0H-6QXqnIQ7kzbb-f42wH002SDIWyrbcGE0IZWcQSzAzRg",
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
        $scope = $rootScope.$new();

        //Cancel the behavior of $state.go(..)
        spyOn($state, 'go').and.callFake(function(state, params) {

        });
    }));

    afterEach(function() {
        $httpBackend.verifyNoOutstandingExpectation();
        $httpBackend.verifyNoOutstandingRequest();
        localStorage.setItem('token_exp', Math.floor(Date.now() / 1000) + 3600 );
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
