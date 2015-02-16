module
    .factory('StorageService', ['$rootScope', function ($rootScope){        
        return {
            get: function (key) {
               return localStorage.getItem(key);
            },

            set: function (key, data) {
               localStorage.setItem(key, JSON.stringify(data));
            },

            remove: function (key){
                localStorage.removeItem(key);
            },
            
            clearAll : function () {
                localStorage.clear();
            }
        };
    }]);
