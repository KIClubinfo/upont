describe('Factory PH_categories', function() {
    var PH_categories;
    beforeEach(module('upont'));

    beforeEach(inject(function(_PH_categories_) {
        PH_categories = _PH_categories_;
    }));

    it('should make the right corespondances', function(){
      expect(PH_categories('films')).toBe('movies');
      expect(PH_categories('series')).toBe('series');
      expect(PH_categories('jeux')).toBe('games');
      expect(PH_categories('logiciels')).toBe('softwares');
      expect(PH_categories('musiques')).toBe('albums');
      expect(PH_categories('autres')).toBe('others');
    });
});

describe('PH_Liste_Ctrl et PH_Element_Ctrl', function(){
    var $rootScope, $controller,  $state, $scope;

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

    it('should load the elements in list view', function(){
        var controller = $controller('PH_Liste_Ctrl', { $scope: $scope, elements: [{}, {}, {}] });
        expect($scope.elements.length).toEqual(3);
    });

    it('should load the element in simple view', function(){
        var controller = $controller('PH_Element_Ctrl', { $scope: $scope, element: {}, episodes: null });
        expect(typeof($scope.element)).toBe('object');
    });

    it('should load a serie in simple view', function(){
        var controller = $controller('PH_Element_Ctrl', { $scope: $scope, element: {}, episodes: [{number: 1, season: 1 }, {number: 2, season: 1 }, {number: 1, season: 2 }] });
        expect(typeof($scope.element)).toBe('object');
        expect($scope.saisons.length).toBe(2);
        expect($scope.saisons[0].length).toBe(2);
        expect($scope.saisons[1].length).toBe(1);
    });
});
