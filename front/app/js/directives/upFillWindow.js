angular.module('upont').directive('upFillWindow', ['$window', function ($window) {
	return {
		link:function ($scope, $element, $attrs){
			//Initialisation
			var newHeight = 0;
			if($attrs.upFillWindow == 'calendrier'){
				newHeight = $window.innerHeight - $('header').outerHeight();
				$element.height(newHeight.toString()+'px' );
			}
			else{
				newHeight = $window.innerHeight - $('header').outerHeight() - $('footer').outerHeight();
				$element.css('min-height', newHeight.toString()+'px' );
			}

			//On vérifie si le header ou le footer changent de taille
			$scope.$watch(function(){
				return $('footer').outerHeight();
				}, function(){
				if($attrs.upFillWindow == 'calendrier'){
					newHeight = $window.innerHeight - $('header').outerHeight();
					$element.height(newHeight.toString()+'px' );
				}
				else{
					newHeight = $window.innerHeight - $('header').outerHeight() - $('footer').outerHeight();
					$element.css('min-height', newHeight.toString()+'px' );
				}
			});

			//On observe si le navigateur change de taille
			angular.element($window).on('resize', function(){
				$scope.$apply(function(){
					if($attrs.upFillWindow == 'calendrier'){
						newHeight = $window.innerHeight - $('header').outerHeight();
						$element.height(newHeight.toString()+'px' );
					}
					else{
						newHeight = $window.innerHeight - $('header').outerHeight() - $('footer').outerHeight();
						$element.css('min-height', newHeight.toString()+'px' );
					}
				});
			});

		}
	};
}]);