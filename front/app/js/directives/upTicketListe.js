angular.module('upont').directive('upTicketListe', ['$window', function($window) {
    return {
        scope: {
            content: '=',
            ponthub: '=',
            games: '=',
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
            if(scope.games){
                element.css('width', '27em');
                element.find('div.header').css({
                    'overflow': 'hidden',
                    'height': '15em',
                });
            }
            else{
                element.css('width', '17em');
                element.find('div.header').css({
                    'overflow': 'hidden',
                    'height': scope.ponthub? '15em':'10em',
                });
            }
            element.find('div.header').css({
                'background-image':  'url('+apiPrefix+scope.content.img+')',
                'background-repeat': 'no-repeat',
                'background-position': 'center',
                'background-size': 'contain'
            });
        }
    };
}]);
