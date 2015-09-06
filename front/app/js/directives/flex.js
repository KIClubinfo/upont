/**
 * Détermine le layout flex en fonction des paramètres de la directive
 */
function setFlexLayout(layout, element, args) {
    var primaryLayout, secondaryLayout;

    // Soit le layout a été précisé, soit on utilise celui par défaut
    if (layout !== undefined) {
        var layout = layout.split(' ');
        primaryLayout   = layout[0];
        secondaryLayout = layout[1];
    } else {
        primaryLayout   = 'start';
        secondaryLayout = 'stretch';
    }

    element.addClass('flex');
    element.addClass('p-' + primaryLayout);
    element.addClass('s-' + secondaryLayout);
    if(args.hasOwnProperty('wrap')) {
        element.addClass('wrap');
    }
}

angular.module('upont')
    .directive('row', function() {
        return {
            scope: {
                layout: '@?row',
            },
            link: {
                pre: function($scope, element, args) {
                    element.addClass('flex-row');
                    setFlexLayout($scope.layout, element, args)
                }
            }
        };
    })
    .directive('col', function() {
        return {
            scope: {
                layout: '@?col',
            },
            link: {
                pre: function($scope, element, args) {
                    element.addClass('flex-col');
                    setFlexLayout($scope.layout, element, args)
                }
            }
        };
    })
    .directive('flex', function() {
        return {
            scope: {
                layout: '@?row',
            },
            link: {
                pre: function($scope, element, args) {
                    element.addClass('col');
                    setFlexLayout($scope.layout, element, args)
                }
            }
        };
    })
;
