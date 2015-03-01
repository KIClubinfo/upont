describe('$state resolutions', function(){
    var $rootScope, $state, $httpBackend, state, injector, StorageService;

    beforeEach(module('upont'));

    beforeEach(inject(function($injector, _$rootScope_, _$state_, _StorageService_) {
        $rootScope = _$rootScope_;
        $state = _$state_;
        injector = $injector;
        $httpBackend = $injector.get('$httpBackend');
        StorageService = _StorageService_;

        spyOn($state, 'go').and.callFake(function(state, params) {
        });
    }));

    afterEach(function() {
        $httpBackend.verifyNoOutstandingExpectation();
        $httpBackend.verifyNoOutstandingRequest();
    });



    /// Page Home
    it('should load for home.connected.liste', function(){
        state = $state.get('home.connected.liste');
        $httpBackend.expectGET('/api/own/newsitems').respond(200, [{}, {}, {}]);
        $httpBackend.expectGET('/api/events').respond(200, [{}, {}, {}]);

        //The $injector.invoke() method execute the given function. See the doc for more info
        injector.invoke(state.resolve.newsItems);
        injector.invoke(state.resolve.events);
        $httpBackend.flush();
    });




    /// Page Calendrier
    it('should load for calendrier', function(){
        state = $state.get('calendrier');
        $httpBackend.expectGET('/api/events').respond(200, [{}, {}, {}]);
        injector.invoke(state.resolve.events);
        $httpBackend.flush();
    });

    it('should make work the onEnter and onExit methods', function(){
        expect($rootScope.hideFooter).toBe(false);
        state = $state.get('calendrier');
        injector.invoke(state.onEnter);
        expect($rootScope.hideFooter).toBe(true);
        injector.invoke(state.onExit);
        expect($rootScope.hideFooter).toBe(false);
    });



    /// Page Channels
    it('should load for channels.liste', function(){
        state = $state.get('channels.liste');
        $httpBackend.expectGET('/api/clubs?sort=name').respond(200, [{}, {}, {}]);
        injector.invoke(state.resolve.channels);
        $httpBackend.flush();
    });

    it('should load for channel.simple', function(){
        state = $state.get('channels.simple');
        $httpBackend.expectGET('/api/clubs/bde').respond(200, {});
        injector.invoke(state.resolve.channel, this, {$stateParams: {slug: 'bde'}});
        $httpBackend.flush();
    });

    it('should load for channel.simple.publications', function(){
        state = $state.get('channels.simple.publications');
        $httpBackend.expectGET('/api/clubs/bde/publications').respond(200, [{}, {}, {}]);
        injector.invoke(state.resolve.publications, this, {$stateParams: {slug: 'bde'}});
        $httpBackend.flush();
    });

    it('should load for channel.simple.presentation', function(){
        state = $state.get('channels.simple.presentation');
        $httpBackend.expectGET('/api/clubs/bde/users').respond(200, [{}, {}, {}]);
        injector.invoke(state.resolve.membres, this, {$stateParams: {slug: 'bde'}});
        $httpBackend.flush();
    });




    /// Page Ponthub
    it('should load for ponthub.category.liste', function(){
        state = $state.get('ponthub.category.liste');
        $httpBackend.expectGET('/api/movies').respond(200, [{}, {}, {}]);
        injector.invoke(state.resolve.elements, this, {$stateParams: {category: 'films'}});
        $httpBackend.flush();
    });

    it('should load for ponthub.category.simple', function(){
        state = $state.get('ponthub.category.simple');
        $httpBackend.expectGET('/api/movies/potato').respond(200, {});
        injector.invoke(state.resolve.element, this, {$stateParams: {category: 'films', slug: 'potato'}});
        expect(injector.invoke(state.resolve.episodes, this, {$stateParams: {category: 'films', slug: 'potato'}})).toBe(true);
        $httpBackend.flush();

        state = $state.get('ponthub.category.simple');
        $httpBackend.expectGET('/api/series/potato').respond(200, {});
        $httpBackend.expectGET('/api/series/potato/episodes').respond(200, [{}, {}, {}]);
        injector.invoke(state.resolve.element, this, {$stateParams: {category: 'series', slug: 'potato'}});
        injector.invoke(state.resolve.episodes, this, {$stateParams: {category: 'series', slug: 'potato'}});
        $httpBackend.flush();
    });



    /// Page Profil
    it('should load for profil', function(){
        state = $state.get('profil');
        $httpBackend.expectGET('/api/own/preferences').respond(200, {});
        $httpBackend.expectGET('/api/clubs?sort=name').respond(200, [{}, {}, {}]);
        $httpBackend.expectGET('/api/own/followed').respond(200, [{}, {}, {}]);

        //The $injector.invoke() method execute the given function. See the doc for more info
        injector.invoke(state.resolve.preferences);
        injector.invoke(state.resolve.clubs);
        injector.invoke(state.resolve.clubsSuivis);
        $httpBackend.flush();
    });



    /// Page Promo
    it('should load for promo.trombi', function(){
        state = $state.get('promo.trombi');
        $httpBackend.expectGET('/api/users').respond(200, [{}, {}, {}]);

        //The $injector.invoke() method execute the given function. See the doc for more info
        injector.invoke(state.resolve.eleves);
        $httpBackend.flush();
    });



    /// Page Cours
    it('should load for cours.section', function(){
        state = $state.get('cours.section');
        // $httpBackend.expectGET('/api/exercices').respond(200, [{}, {}, {}]);
        injector.invoke(state.resolve.exos, this, {$stateParams: {section: '1a'}});
        // $httpBackend.flush();

    });
});
