/**
 * Détermine le layout flex en fonction des paramètres de la directive
 */
function setFlexLayout(layout, element, args) {
    var primaryLayout, secondaryLayout;

    // Soit le layout a été précisé, soit on utilise celui par défaut
    if (layout !== undefined && layout !== '') {
        layout = layout.split(' ');
        primaryLayout   = layout[0];
        secondaryLayout = layout[1];
    } else {
        primaryLayout   = 'start';
        secondaryLayout = 'stretch';
    }

    element.addClass('flex');
    element.addClass('p-' + primaryLayout);
    element.addClass('s-' + secondaryLayout);
    if (args.hasOwnProperty('wrap')) {
        element.addClass('flex-wrap');
    }
}

angular.module('upont')
    .directive('row', function() {
        return {
            priority: 1001,
            compile: function(element, args) {
                if(args.hasOwnProperty('reverse')) {
                    element.addClass('flex-row-reverse');
                } else {
                    element.addClass('flex-row');
                }
                setFlexLayout(args.row, element, args);
            }
        };
    })
    .directive('col', function() {
        return {
            priority: 1001,
            compile: function(element, args) {
                if(args.hasOwnProperty('reverse')) {
                    element.addClass('flex-col-reverse');
                } else {
                    element.addClass('flex-col');
                }
                setFlexLayout(args.col, element, args);
            }
        };
    })
    .directive('flex', function() {
        return {
            priority: 1001,
            compile: function(element, args){
                element.addClass('flex-' + args.flex + 'pct');
            }
        };
    })
    .directive('flexXs', function(){
        return {
            priority: 1001,
            compile: function(element, args){
                element.addClass('flex-xs-' + args.flexXs + 'pct');
            }
        };
    })
    .directive('flexSm', function(){
        return {
            priority: 1001,
            compile: function(element, args){
                element.addClass('flex-sm-' + args.flexSm + 'pct');
            }
        };
    })
    .directive('flexMd', function(){
        return {
            priority: 1001,
            compile: function(element, args){
                element.addClass('flex-md-' + args.flexMd + 'pct');
            }
        };
    })
    .directive('flexLg', function(){
        return {
            priority: 1001,
            compile: function(element, args){
                element.addClass('flex-lg-' + args.flexLg + 'pct');
            }
        };
    })
;
