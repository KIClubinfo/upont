describe("Publis_Ctrl", function() {
    var $rootScope, $controller, $state, $scope;

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

    it('should load the news and events to the scope and order them', function(){
        var controller = $controller('Publis_Ctrl', { $scope: $scope, newsItems : [{date: 111 }, {date: 112 }, {date: 13 }], events: [{date: 1 }] });
        expect($scope.publications.length).toEqual(4);

        expect($scope.publications[0].date).toEqual(112);
        expect($scope.publications[1].date).toEqual(111);
        expect($scope.publications[2].date).toEqual(13);
        expect($scope.publications[3].date).toEqual(1);
    });
});
