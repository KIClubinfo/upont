describe('factory StorageService', function() {
    var StorageService;
    beforeEach(module('upont'));

    beforeEach(inject(function(_StorageService_) {
        localStorage.clear();
        StorageService = _StorageService_;
    }));

    it('should be able to write simple objects on local storage', function(){
        StorageService.set('str', 'potato');
        StorageService.set('nbr', 17);
        expect(localStorage.getItem('str')).toBe('potato');
        expect(localStorage.getItem('nbr')).toBe('17');
    });

    it('should be able to write json objects on local storage', function(){
        StorageService.set('obj', { key: 'value'});
        expect(JSON.parse(localStorage.getItem('obj')).key).toBe( 'value');
    });

    it('should be able to read local storage', function(){
        localStorage.setItem('key', 'potato');
        localStorage.setItem('obj', JSON.stringify({ key: 'value' }));

        expect(StorageService.get('key')).toBe('potato');
        expect(StorageService.get('obj')).toBe( '{"key":"value"}' );
    });

    it('should be able to remove elements from local storage', function(){
        localStorage.setItem('key', 'potato');
        expect(localStorage.getItem('key')).toBe('potato');
        StorageService.remove('key');
        expect(localStorage.getItem('key')).toBe(null);
    });

    it('should be able to clear local storage', function(){
        localStorage.setItem('key', 'potato');
        expect(localStorage.getItem('key')).toBe('potato');
        StorageService.clearAll();
        expect(localStorage.getItem('key')).toBe(null);
    });
});
