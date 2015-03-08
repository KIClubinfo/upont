angular.module('upont').directive('upTicketListe', ['$window', function($window) {
    return {
        scope: {
            content: '=',
            ponthub: '=',
            category: '=',
        },
        template: "<div>" +
            "<a ui-sref='ponthub.category.simple({slug: ponthub})'>"+
            "<div class='up-img-ponthub'><div class='img-ph'></div></div>" +
            "<h1>{{ content.title }}</h1>" +
            "</div>"+
            "</a>",
        link: function(scope, element, attrs) {
            if(!scope.ponthub){
                element.find('div.up-img-ponthub').unwrap();
            }

            var clas;
            switch(scope.category){
                case 'jeux':
                    clas = "up-col-xs-4";
                    break;
                case 'films':
                case 'series':
                    clas = "up-col-xs-2";
                    break;
                case 'musiques':
                case 'autres':
                case 'logiciels':
                    clas = "up-col-xs-2";
                    break;
                default:
                    clas = "up-col-xs-3";
            }
            element.addClass(clas);
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
