describe("Calendrier_Ctrl", function() {
    var $rootScope, $controller,$state, $scope;
    var fakeEvents = [{
            "name": "Don Giovanni",
            "slug": "don-giovanni",
            "author_club": {
                "name": "BDA"
            },
            "start_date": 1424530800,
            "end_date": 1424545200,
        },
        {
            "name": "Interne de Noël",
            "slug": "interne-de-noel",
            "author_club": {
                "name": "BDE"
            },
            "start_date": 1424419200,
            "end_date": 1424440800
        },
        {
            "name": "Formations PEP - Objectif recrutement",
            "slug": "formations-pep-objectif-recrutement",
            "author_club": {
                "name": "PEP"
            },
            "start_date": 1413999000,
            "end_date": 1414009800
        }];
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

    it('should load the events to the scope', function(){
        var controller = $controller('Calendrier_Ctrl', { $scope: $scope, events: fakeEvents });
        expect($scope.events.length).toEqual(3);
    });
});
