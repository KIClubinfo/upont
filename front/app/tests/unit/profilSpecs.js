describe("Profil_Ctrl", function() {
    var $httpBackend, $rootScope, $controller, $state, $scope;
    var fakePrefs ,fakeClubs, fakeClubsSuivis;

    beforeEach(module('upont'));

    beforeEach(inject(function($injector, _$rootScope_, _$controller_, _$state_) {
        $httpBackend = $injector.get('$httpBackend');
        $rootScope = _$rootScope_;
        $controller = _$controller_;
        $state = _$state_;

        $scope = $rootScope.$new();

        //Cancel the behavior of $state.go(..)
        spyOn($state, 'go').and.callFake(function(state, params) {
        });

        fakePrefs = {
            "notif_followed_event": 0,
            "notif_followed_news": 1,
            "notif_followed_poll": 1,
            "notif_ponthub": 3,
            "notif_ki_answer": 1,
            "notif_shotgun_h-1": 1,
        };
        fakeClubs = [
           {
               "likes": 0,
               "dislikes": 0,
               "comments": 0,
               "name": "Mediatek",
               "slug": "mediatek",
               "like": false,
               "dislike": false,
               "image_url": "uploads/images/default-user.png",
               "full_name": "Médiatek",
               "active": true,
               "type": "club"
           },
           {
               "likes": 0,
               "dislikes": 0,
               "comments": 0,
               "name": "PEP",
               "slug": "pep",
               "like": false,
               "dislike": false,
               "image_url": "uploads/images/12.png",
               "full_name": "Ponts Études Projets",
               "icon": "eur",
               "active": true,
               "type": "club"
           },
           {
               "likes": 0,
               "dislikes": 0,
               "comments": 0,
               "name": "BDA",
               "slug": "bda",
               "like": false,
               "dislike": false,
               "image_url": "uploads/images/9.jpg",
               "full_name": "Bureau Des Arts",
               "icon": "paint-brush",
               "active": true,
               "type": "club"
           },
           {
               "likes": 0,
               "dislikes": 0,
               "comments": 0,
               "name": "BDE",
               "slug": "bde",
               "like": false,
               "dislike": false,
               "image_url": "uploads/images/10.jpg",
               "full_name": "Bureau Des Élèves",
               "active": true,
               "type": "club"
           },
           {
               "likes": 0,
               "dislikes": 0,
               "comments": 0,
               "name": "KI",
               "slug": "ki",
               "like": false,
               "dislike": false,
               "image_url": "uploads/images/11.png",
               "full_name": "Club Informatique",
               "icon": "download",
               "active": true,
               "type": "club"
           }
        ];
        fakeClubsSuivis = [
           {
               "likes": 0,
               "dislikes": 0,
               "comments": 0,
               "name": "KI",
               "slug": "ki",
               "like": false,
               "dislike": false,
               "image_url": "uploads/images/11.png",
               "full_name": "Club Informatique",
               "icon": "download",
               "active": true,
               "type": "club"
           }
        ];

    }));

    it('should load the data to the scope', function(){
        var controller = $controller('Profil_Ctrl', { $scope: $scope, preferences: fakePrefs, clubs: fakeClubs, clubsSuivis: fakeClubsSuivis });
        //Les 6 préférences sont passées dans le $scope
        expect(Object.keys($scope.preferences).length).toEqual(6);
        //Les 5 clubs sont passés dans le $scope
        expect($scope.clubs.length).toEqual(5);

        //Le club KI est bien suivi
        expect($scope.clubs[4].name).toBe("KI");
        expect($scope.clubs[4].suivi).toBe(true);

        //Les autres clubs ne sont pas suivis
        expect($scope.clubs[0].suivi).toBe(false);
    });

    it('should be able to subscribe to a channel', function(){
        var controller = $controller('Profil_Ctrl', { $scope: $scope, preferences: fakePrefs, clubs: fakeClubs, clubsSuivis: fakeClubsSuivis });
        expect($scope.clubs[3].suivi).toBe(false);
        $httpBackend.expectPOST('/api/clubs/bde/follow').respond(200, '');
        $scope.subscribe("bde");
        $httpBackend.flush();

        expect($scope.clubs[3].suivi).toBe(true);
    });

    it('should be able to unsubcribe to a channel', function(){
        var controller = $controller('Profil_Ctrl', { $scope: $scope, preferences: fakePrefs, clubs: fakeClubs, clubsSuivis: fakeClubsSuivis });
        expect($scope.clubs[4].suivi).toBe(true);
        $httpBackend.expectPOST('/api/clubs/ki/unfollow').respond(200, '');
        $scope.unsubscribe("ki");
        $httpBackend.flush();

        expect($scope.clubs[4].suivi).toBe(false);
    });

    // it('should be able to change preferences', function(){

    // });

    // it('should be able to subscribe to a course', function(){

    // });

    // it('should be able to unsubcribe to a course', function(){


    // });
});
