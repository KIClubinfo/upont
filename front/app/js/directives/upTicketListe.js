angular.module('upont').directive('upTicketListe', ['$window', function($window) {
    return {
        scope: {
            content: '=',
            ponthub: '=',
            category: '=',
        },
        template: "<div class='up-ticket up-ticket-liste outer up-col-xs-12'>" +
            "<a ui-sref='ponthub.category.simple({slug: ponthub})'>"+
            "<div class='header' ></div>" +
            "<h1>{{ content.title }}</h1>" +
            "</div>"+
            "</a>",
        link: function(scope, element, attrs) {
            if(!scope.ponthub){
                element.find('div.header').unwrap();
            }

            var wdt;
            switch(scope.category){
                case 'jeux':
                    wdt = 30;
                    break;
                case 'films':
                case 'series':
                    wdt = 13.5;
                    break;
                case 'musiques':
                case 'autres':
                case 'logiciels':
                    wdt = 18.5;
                    break;
                default:
                    wdt = 13.5;
            }
            element.css('width', wdt+'em');
            element.find('div.header').css({
                'overflow': 'hidden',
                'height': '15em',
            });

            element.find('div.header').css({
                'background-image':  'url('+apiPrefix+scope.content.img+')',
                'background-repeat': 'no-repeat',
                'background-position': 'center',
                'background-size': 'contain'
            });
        }
    };
}]);
