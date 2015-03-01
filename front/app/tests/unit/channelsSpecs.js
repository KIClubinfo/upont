describe("ChannelListe_Ctrl and ChannelSimple_Ctrl", function() {
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

    it('should load the list to the scope', function(){
        var controller = $controller('ChannelsListe_Ctrl', { $scope: $scope, channels: [{}, {}, {}] });
        expect($scope.channels.length).toBe(3);
    });

    it('should load the channel alone to the scope', function(){
        var controller = $controller('ChannelsSimple_Ctrl', { $scope: $scope, channel: {}, publications : [{},{},{}], membres: [{},{},{}]});
        expect($scope.channel).not.toBe(undefined);
        expect($scope.publications.length).toBe(3);
        expect($scope.membres.length).toBe(3);
    });
});
