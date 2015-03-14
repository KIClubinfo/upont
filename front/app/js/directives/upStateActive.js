angular.module('upont')
    .directive('upStateActive', ['$state', '$rootScope', function($state) {
        return {
            controller: ['$scope', '$element', '$attrs', '$state', function($scope, $element, $attrs, $state){
                var stateActive = '';
                if($attrs.upStateActive === '')
                    stateActive = $attrs.uiSref;
                else
                    stateActive = $attrs.upStateActive;

                if($attrs.upStateActiveParams){
                    var params = JSON.parse($attrs.upStateActiveParams);
                }
                $scope.$on('$stateChangeSuccess', function(){
                    if((params && $state.includes(stateActive, params)) || ( !$attrs.upStateActiveParams && $state.includes(stateActive) ) ){
                        $element.addClass('active');
                    }
                    else{
                        $element.removeClass('active');
                    }
                });
            }]
        };
    }]);