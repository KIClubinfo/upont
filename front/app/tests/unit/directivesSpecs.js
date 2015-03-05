describe('upFillWindow', function(){
    var $compile, $rootScope, $state, $window;

    beforeEach(module('upont'));

    beforeEach(inject(function(_$compile_, _$rootScope_, _$state_, _$window_){
        $compile = _$compile_;
        $rootScope = _$rootScope_;
        $state = _$state_;
        $window = _$window_;
        spyOn($state, 'go').and.callFake(function(state, params) {
        });
    }));


    it('should apply the right styles to the normal function even after a digest cycle or a resize', function(){
        var element = $compile("<up-fill-window></up-fill-window>")($rootScope);
        expect(element.css('min-height')).toBe(($window.innerHeight - $('header').outerHeight() - $('footer').outerHeight())+'px');

        $rootScope.$digest();
        expect(element.css('min-height')).toBe(($window.innerHeight - $('header').outerHeight() - $('footer').outerHeight())+'px');

        angular.element($window).trigger('resize');
        expect(element.css('min-height')).toBe(($window.innerHeight - $('header').outerHeight() - $('footer').outerHeight())+'px');
    });

    it('should apply the right styles to the function in case of the calendar even after a digest cycle or a resize', function(){
        var element = $compile("<div up-fill-window='calendrier'></div>")($rootScope);
        expect(element.css('height')).toBe(($window.innerHeight - $('header').outerHeight() - $('footer').outerHeight())+'px');

        $rootScope.$digest();
        expect(element.css('height')).toBe(($window.innerHeight - $('header').outerHeight() - $('footer').outerHeight())+'px');

        angular.element($window).trigger('resize');
        expect(element.css('height')).toBe(($window.innerHeight - $('header').outerHeight() - $('footer').outerHeight())+'px');
    });
});

describe('upLikes', function(){
    var $compile, $rootScope, $state,$httpBackend;

    beforeEach(module('upont'));
    beforeEach(module('templates'));

    beforeEach(inject(function($injector, _$compile_, _$rootScope_, _$state_){
        $httpBackend = $injector.get('$httpBackend');
        $compile = _$compile_;
        $rootScope = _$rootScope_;
        $state = _$state_;
        spyOn($state, 'go').and.callFake(function(state, params) {
        });
    }));

    afterEach(function() {
        $httpBackend.verifyNoOutstandingExpectation();
        $httpBackend.verifyNoOutstandingRequest();
    });

    it('should be filled with the right html', function(){
        var element = $compile("<div up-likes></div>")($rootScope);
        $rootScope.$digest();
        expect(element.html()).toContain('<span class="glyphicon glyphicon-thumbs-up"></span>');
        expect(element.html()).toContain('<span class="glyphicon glyphicon-thumbs-down"></span>');
        expect(element.html()).toContain('<span class="glyphicon glyphicon-comment"></span>');
    });

    it('should be able to upvote(), usual case', function(){
        $rootScope.potato = {
            like: false,
            dislike: false,
            likes: 10,
            dislikes: 10,
        };
        $rootScope.url='carrot';

        var element = $compile("<div up-likes objet='potato' url='url'></div>")($rootScope);
        $rootScope.$digest();
        var scope = element.isolateScope();
        $httpBackend.expectPOST('/api/carrot/like').respond(200, '');
        scope.upvote();
        $httpBackend.flush();
        expect($rootScope.potato.like).toBe(true);
        expect($rootScope.potato.dislike).toBe(false);
        expect($rootScope.potato.likes).toBe(11);
        expect($rootScope.potato.dislikes).toBe(10);
    });

    it('should be able to upvote(), having already upvoted', function(){
        $rootScope.potato = {
            like: true,
            dislike: false,
            likes: 11,
            dislikes: 10,
        };
        $rootScope.url='events';

        var element = $compile("<div up-likes objet='potato' url='url'></div>")($rootScope);
        $rootScope.$digest();
        var scope = element.isolateScope();
        $httpBackend.expectDELETE('/api/events/like').respond(200, '');
        scope.upvote();
        $httpBackend.flush();
        expect($rootScope.potato.like).toBe(false);
        expect($rootScope.potato.dislike).toBe(false);
        expect($rootScope.potato.likes).toBe(10);
        expect($rootScope.potato.dislikes).toBe(10);
    });

    it('should be able to upvote(), having already downvoted', function(){
        $rootScope.potato = {
            like: false,
            dislike: true,
            likes: 10,
            dislikes: 11,
        };
        $rootScope.url='carrot';

        var element = $compile("<div up-likes objet='potato' url='url'></div>")($rootScope);
        $rootScope.$digest();
        var scope = element.isolateScope();
        $httpBackend.expectPOST('/api/carrot/like').respond(200, '');
        scope.upvote();
        $httpBackend.flush();
        expect($rootScope.potato.like).toBe(true);
        expect($rootScope.potato.dislike).toBe(false);
        expect($rootScope.potato.likes).toBe(11);
        expect($rootScope.potato.dislikes).toBe(10);
    });

    it('should be able to downvote(), usual case', function(){
        $rootScope.potato = {
            like: false,
            dislike: false,
            likes: 10,
            dislikes: 10,
        };
        $rootScope.url='carrot';

        var element = $compile("<div up-likes objet='potato' url='url'></div>")($rootScope);
        $rootScope.$digest();
        var scope = element.isolateScope();
        $httpBackend.expectPOST('/api/carrot/dislike').respond(200, '');
        scope.downvote();
        $httpBackend.flush();
        expect($rootScope.potato.like).toBe(false);
        expect($rootScope.potato.dislike).toBe(true);
        expect($rootScope.potato.likes).toBe(10);
        expect($rootScope.potato.dislikes).toBe(11);
    });

    it('should be able to downvote(), having already downvoted', function(){
        $rootScope.potato = {
            like: false,
            dislike: true,
            likes: 10,
            dislikes: 11,
        };
        $rootScope.url='carrot';

        var element = $compile("<div up-likes objet='potato' url='url'></div>")($rootScope);
        $rootScope.$digest();
        var scope = element.isolateScope();
        $httpBackend.expectDELETE('/api/carrot/dislike').respond(200, '');
        scope.downvote();
        $httpBackend.flush();
        expect($rootScope.potato.like).toBe(false);
        expect($rootScope.potato.dislike).toBe(false);
        expect($rootScope.potato.likes).toBe(10);
        expect($rootScope.potato.dislikes).toBe(10);
    });

    it('should be able to downvote(), having already upvoted', function(){
        $rootScope.potato = {
            like: true,
            dislike: false,
            likes: 11,
            dislikes: 10,
        };
        $rootScope.url='carrot';

        var element = $compile("<div up-likes objet='potato' url='url'></div>")($rootScope);
        $rootScope.$digest();
        var scope = element.isolateScope();
        $httpBackend.expectPOST('/api/carrot/dislike').respond(200, '');
        scope.downvote();
        $httpBackend.flush();
        expect($rootScope.potato.like).toBe(false);
        expect($rootScope.potato.dislike).toBe(true);
        expect($rootScope.potato.likes).toBe(10);
        expect($rootScope.potato.dislikes).toBe(11);
    });
});


describe('upPubliText', function(){
    var $compile, $rootScope, $state;

    beforeEach(module('upont'));
    // beforeEach(module('templates'));

    beforeEach(inject(function(_$compile_, _$rootScope_, _$state_){
        $compile = _$compile_;
        $rootScope = _$rootScope_;
        $state = _$state_;
        spyOn($state, 'go').and.callFake(function(state, params) {
        });
    }));

    it('should be filled with the right html if the string is short', function(){
        $rootScope.bla = 'I love potatoes !';
        var element = $compile("<div up-publi-text string='bla'></div>")($rootScope);
        $rootScope.$digest();
        expect(element.html()).toContain('I love potatoes !');
        expect(element.html()).not.toContain('Afficher la suite');
        expect(element.isolateScope().opened).toBe(true);
    });

    it('should be filled with the right html if the string is long', function(){
        $rootScope.bla = 'I love potatoes ! Loooooooooooooooooooooooooooooooooooo'+
        'oooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooo'+
        'ooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooon'+
        'ooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooon'+
        'oooooooooooooooooooooooooooooog string...';
        var element = $compile("<div up-publi-text string='bla'></div>")($rootScope);
        $rootScope.$digest();
        expect(element.isolateScope().opened).toBe(false);
        expect(element.html()).toContain('Afficher la suite');

        element.isolateScope().open();
        expect(element.isolateScope().opened).toBe(true);
    });

});