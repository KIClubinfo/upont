describe("Cours_Ctrl", function() {
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

    it('should load', function(){
        var controller = $controller('Cours_Ctrl', { $scope: $scope, exos : [{}, {}, {}]});
        expect($scope.exos.length).toBe(3);
    });
});
