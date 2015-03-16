angular.module('upont').directive('upTicketListe', ['$window', function($window) {
    return {
        scope: {
            content: '=',
            ponthub: '=',
            category: '=',
        },
        template:
            '<a ui-sref="root.ponthub.simple({slug: ponthub})">' +
            '<div class="up-img-ponthub"><div class="img-ph"></div></div>' +
            '<div class="up-title-ponthub">{{ content.title }}</div>' +
            '</a>',
        link: function(scope, element, attrs) {
            if(!scope.ponthub){
                element.find('div.up-img-ponthub').unwrap();
            }

            var classe;
            switch(scope.category){
                case 'jeux':
                    classe = "up-col-xs-12 up-col-sm-6 up-col-md-4";
                    break;
                case 'films':
                case 'series':
                case 'musiques':
                case 'autres':
                case 'logiciels':
                    classe = "up-col-xs-2";
                    break;
                default:
                    classe = 'up-col-xs-12 up-col-sm-4 up-col-md-2';
            }
            element.addClass(classe);
            element.css('position', 'relative');
            element.find('div.up-img-ponthub').addClass(scope.category);
            element.find('div.img-ph').css({
                'overflow': 'hidden',
                'background-image':  'url('+apiPrefix+scope.content.img+')',
                'background-repeat': 'no-repeat',
                'background-position': 'center',
                'background-size': 'cover',
                'position': 'absolute',
                'top':'0',
                'bottom':'0',
                'right':'0',
                'left':'0',
            });
        }
    };
}]);
