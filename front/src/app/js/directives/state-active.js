angular.module('upont')
    .directive('upStateActive', ['$state', '$rootScope', function($state) {
        return {
            controller: ['$scope', '$element', '$attrs', '$state', function($scope, $element, $attrs, $state) {
                var stateActive = '';
                if($attrs.upStateActive === '')
                    stateActive = $attrs.uiSref;
                else
                    stateActive = $attrs.upStateActive;

                if($attrs.upStateActiveParams){
                    var params = JSON.parse($attrs.upStateActiveParams);
                }
                $scope.$on('$stateChangeSuccess', function(event, toState, toParams, fromState, fromParams){
                    // Cas particulier: on veut que le lien "profil" ne surligne pas "élèves"
                    if (toState.name == 'root.users.students.modify' && $element.attr('id') == 'link-students') {
                        $element.removeClass('active');
                        return;
                    }
                    if((params && $state.includes(stateActive, params)) || ( !$attrs.upStateActiveParams && $state.includes(stateActive) ) ){
                        $element.addClass('active');
                    }
                    else {
                        $element.removeClass('active');
                    }
                });
            }]
        };
    }]);
