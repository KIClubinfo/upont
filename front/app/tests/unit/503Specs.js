describe("503_Ctrl", function() {
    var $rootScope, $controller,$state, $scope;
    beforeEach(module('upont'));

    beforeEach(inject(function(_$rootScope_, _$controller_, _$state_) {
        $rootScope = _$rootScope_;
        $controller = _$controller_;
        $state = _$state_;

        $scope = $rootScope.$new();

        //Cancel the behavior of $state.go(..)
        spyOn($state, 'go').and.callFake(function(state, params) {
        });
    }));

    afterEach(function(){
        localStorage.clear();
    });

    it('should load the maintenance remaining time', function(){
        localStorage.setItem('maintenance', 10);
        var controller = $controller('503_Ctrl', { $scope: $scope});
        expect($scope.until).toEqual('10');
    });
});
