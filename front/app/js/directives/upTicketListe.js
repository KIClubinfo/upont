angular.module('upont').directive('upTicketListe', ['$window', function($window) {
    return {
        scope: {
            content: '=',
            ponthub: '=',
            category: '=',
        },
        template:
            '<a ui-sref="root.ponthub.simple({slug: ponthub})">' +
                '<div class="up-img-ponthub">' +
                    '<div class="img-ph"></div>' +
                    '<div ng-if="$parent.popular(content.element.downloads)" class="ribbon-wrapper">' +
                        '<div class="ribbon ribbon-popular">POPULAIRE</div>' +
                    '</div>' +
                    '<div ng-if="content.element.added > $parent.lastWeek" class="ribbon-wrapper">' +
                        '<div class="ribbon ribbon-new">NOUVEAU</div>' +
                    '</div>' +
                '</div>' +
                '<div class="up-title-ponthub">{{ content.element.name }}</div>' +
            '</a>',
        link: function(scope, element, attrs) {
            if(!scope.ponthub){
                element.find('div.up-img-ponthub').unwrap();
            }

            var classe;
            switch(scope.category){
                case 'jeux':
                    classe = 'up-col-xs-12 up-col-sm-6 up-col-md-4';
                    break;
                default:
                    classe = 'up-col-xs-6 up-col-sm-4 up-col-md-3 up-col-lg-2';
            }
            element.addClass(classe);
            element.css('position', 'relative');
            element.find('div.up-img-ponthub').addClass(scope.category);

            // Si l'image existe
            if (scope.content.element.image_url) {
                element.find('div.img-ph').css({
                    'background-image':  'url(' + apiPrefix + scope.content.element.image_url + ')',
                });
            } else {
                var icon = '';
                switch(scope.category){
                    case 'jeux':
                        icon = 'fa-gamepad';
                        break;
                    case 'films':
                    case 'series':
                        icon = 'fa-film';
                        break;
                    case 'musiques':
                        icon = 'fa-music';
                        break;
                    case 'autres':
                        icon = 'fa-file-o';
                        break;
                    case 'logiciels':
                        icon = 'fa-desktop';
                        break;
                }
                element.find('div.img-ph').addClass('fa ' + icon);
            }
        }
    };
}]);
