describe('filter formatSize', function() {
    var $filter;
    beforeEach(module('upont'));

    beforeEach(inject(function(_$filter_) {
        $filter = _$filter_;
    }));

    it('should return a length in Octets for tiny sizes', function() {
        expect($filter('formatSize')(100)).toBe('100 Octets');
        expect($filter('formatSize')(1024 * 0.8 - 1)).toBe('818.2 Octets');
    });

    it('should return a length in Ko for small sizes', function() {
        expect($filter('formatSize')(1024 * 0.8)).toBe('0.8 Kio');
        expect($filter('formatSize')(1.5 * 1024)).toBe('1.5 Kio');
        expect($filter('formatSize')(1024 * 1024 * 0.8 - 1)).toBe('819.19 Kio');
    });

    it('should return a length in Mo for medium sizes', function() {
        expect($filter('formatSize')(1024 * 1024 * 0.8)).toBe('0.8 Mio');
        expect($filter('formatSize')(1.5 * 1024 * 1024)).toBe('1.5 Mio');
        expect($filter('formatSize')(1024 * 1024 * 1024 * 0.8 - 1)).toBe('819.19 Mio');
    });

    it('should return a length in Go for big sizes', function() {
        expect($filter('formatSize')(1024 * 1024 * 1024 * 0.8)).toBe('0.8 Gio');
        expect($filter('formatSize')(1.5 * 1024 * 1024 * 1024)).toBe('1.5 Gio');
        expect($filter('formatSize')(200.5 * 1024 * 1024 * 1024)).toBe('200.5 Gio');
    });

    it('should return nothing if the input is not a number', function() {
        expect($filter('formatSize')("potato")).toBe(null);
    });
});

describe('filter formatDuration', function() {
    var $filter;
    beforeEach(module('upont'));

    beforeEach(inject(function(_$filter_) {
        $filter = _$filter_;
    }));

    it('should return a duration in second for a short time', function() {
        expect($filter('formatDuration')(50)).toBe('50s');
        expect($filter('formatDuration')(59)).toBe('59s');
    });

    it('should return a duration in minutes for a medium time', function() {
        expect($filter('formatDuration')(60)).toBe('1mn');
        expect($filter('formatDuration')(75)).toBe('1mn15');
        expect($filter('formatDuration')(3599)).toBe('59mn59');
    });

    it('should return a duration in hours for a long time', function() {
        expect($filter('formatDuration')(3600)).toBe('1h');
        expect($filter('formatDuration')(5400)).toBe('1h30');
        expect($filter('formatDuration')(7200)).toBe('2h');
    });

    it('should return nothing if the input is not a number', function() {
        expect($filter('formatDuration')('potato')).toBe(null);
    });
});

describe('filter urlFile', function() {
    var $filter;
    beforeEach(module('upont'));

    beforeEach(inject(function(_$filter_) {
        $filter = _$filter_;
    }));

    it('should send the url of a picture when just given a string', function() {
        expect($filter('urlFile')('path/to/img.jpg')).toBe('/api/path/to/img.jpg');
    });

    it('should send the path of a movie when given a movie object', function() {
        expect($filter('urlFile')({
            slug: 'randomMovie',
            type: 'movie'
        })).toBe('/api/movies/randomMovie/download');
    });

    it('should send the path of an album when given an album object', function() {
        expect($filter('urlFile')({
            slug: 'randomAlbum',
            type: 'album'
        })).toBe('/api/albums/randomAlbum/download');
    });

    it('should send the path of a game when given a game object', function() {
        expect($filter('urlFile')({
            slug: 'randomGame',
            type: 'game'
        })).toBe('/api/games/randomGame/download');
    });

    it('should send the path of a software when given a software object', function() {
        expect($filter('urlFile')({
            slug: 'randomSoft',
            type: 'software'
        })).toBe('/api/softwares/randomSoft/download');
    });

    it('should send the path of a other when given a other object', function() {
        expect($filter('urlFile')({
            slug: 'randomOther',
            type: 'other'
        })).toBe('/api/others/randomOther/download');
    });

    it('should send the path of an episode when given an episode object and a serie object', function() {
        expect($filter('urlFile')({
            slug: 'randomEpisode',
            type: 'episode'
        }, {
            slug: 'randomSerie',
            type: 'serie'
        })).toBe('/api/series/randomSerie/episodes/randomEpisode/download');
    });

    it('should send the path of an exercise when given an exercise object and a course object', function() {
        expect($filter('urlFile')({
            slug: 'randomExo',
            type: 'exercice'
        }, {
            slug: 'randomCourse',
            type: 'course'
        })).toBe('/api/courses/randomCourse/exercices/randomExo/download');
    });

    it('should return # when given incorect input', function(){
        expect($filter('urlFile')(17)).toBe('#');
        expect($filter('urlFile')()).toBe('#');
        expect($filter('urlFile')({slug: 'potato', type: 'banana'})).toBe('#');

        expect($filter('urlFile')({slug: 'randomEpisode', type: 'episode'})).toBe('#');
        expect($filter('urlFile')({slug: 'randomEpisode', type: 'episode'}, 17)).toBe('#');
        expect($filter('urlFile')({slug: 'randomEpisode', type: 'episode'}, { slug: 'randomThing', type: 'thing' })).toBe('#');

        expect($filter('urlFile')({slug: 'randomExo', type: 'exercice'})).toBe('#');
        expect($filter('urlFile')({slug: 'randomExo', type: 'exercice'}, 17)).toBe('#');
        expect($filter('urlFile')({slug: 'randomExo', type: 'exercice'}, { slug: 'randomThing', type: 'thing' })).toBe('#');
    });
});
