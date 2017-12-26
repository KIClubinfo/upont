// Permet d'injecter des dépendances à un controleur fourni par ng-controller
angular.module('upont').directive('ngInject', ['$parse', '$interpolate', '$controller', '$compile', function($parse, $interpolate, $controller, $compile) {
    return {
        terminal: true,
        transclude: true,
        priority: 510,
        link: function(scope, element, attrs, ctrls, transclude) {
            if (!attrs.ngController) {
                element.removeAttr('ng-inject');
                $compile(element)(scope);
                return;
            }

            var controllerName = attrs.ngController;
            var newScope = scope.$new(false);
            var locals = $parse(attrs.ngInject)(scope);
            locals.$scope = newScope;
            var controller = $controller(controllerName, locals);

            element.data('ngControllerController', controller);
            element.removeAttr('ng-inject').removeAttr('ng-controller');
            $compile(element)(newScope);
            transclude(newScope, function(clone){
            element.append(clone);
        });
        // restore to hide tracks
        element.attr('ng-controller', controllerName);
        }
    };
}]);
