angular.module('upont').filter('reverse', function() {
  return function(items) {
    return items.slice().reverse();
  };
});
