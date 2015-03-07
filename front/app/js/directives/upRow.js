angular.module('upont').directive('upRow', ['$window', function($window) {
    return {
        link: function(scope, element, args){
            element.children().each(function(){
                angular.element(this).wrap('<div></div>');

                if(this.attributes['up-col']){
                    var width = this.attributes['up-col'].value;
                    var suffix = width.indexOf('%') ? 'pct':'px;';
                    angular.element(this).parent().addClass('up-col-'+parseInt(width)+suffix);
                    if(document.querySelector('style').textContent.indexOf('.up-col-'+parseInt(width)+suffix) == -1){
                        document.querySelector('style').textContent +=
                            ' .up-col-'+parseInt(width)+suffix+'{'+
                                    'width:'+ width+
                            '}';
                    }
                }
                if(this.attributes['up-col-xs']){
                    var widthXs = this.attributes['up-col-xs'].value;
                    var suffixXs = widthXs.indexOf('%') ? 'pct':'px;';
                    angular.element(this).parent().addClass('up-col-xs-'+parseInt(widthXs)+suffixXs);
                    if(document.querySelector('style').textContent.indexOf('.up-col-xs-'+parseInt(widthXs)+suffixXs) == -1){
                        document.querySelector('style').textContent +=
                            ' @media (max-width:768px){'+
                                ' .up-col-xs-'+parseInt(widthXs)+suffixXs+'{'+
                                        'width:'+ widthXs+
                                '}}';
                    }
                }
                if(this.attributes['up-col-sm']){
                    var widthSm = this.attributes['up-col-sm'].value;
                    var suffixSm = widthSm.indexOf('%') ? 'pct':'px';
                    angular.element(this).parent().addClass('up-col-sm-'+parseInt(widthSm)+suffixSm);
                    if(document.querySelector('style').textContent.indexOf('.up-col-sm-'+parseInt(widthSm)+suffixSm) == -1)
                        document.querySelector('style').textContent +=
                            ' @media (min-width:768px) and (max-width:992px){'+
                                '.up-col-sm-'+parseInt(widthSm)+suffixSm+'{'+
                                    'width:'+ widthSm +
                            '}}';
                }
                if(this.attributes['up-col-md']){
                    var widthMd = this.attributes['up-col-md'].value;
                    var suffixMd = widthMd.indexOf('%') ? 'pct':'px';
                    angular.element(this).parent().addClass('up-col-md-'+parseInt(widthMd)+suffixMd);
                    if(document.querySelector('style').textContent.indexOf('.up-col-md-'+parseInt(widthMd)+suffixMd) == -1)
                        document.querySelector('style').textContent +=
                            ' @media (min-width:992px) and (max-width:1200px){'+
                                '.up-col-md-'+parseInt(widthMd)+suffixMd+'{'+
                                    'width:'+ widthMd+
                            '}}';                }
                if(this.attributes['up-col-lg']){
                    var widthLg = this.attributes['up-col-lg'].value;
                    var suffixLg = widthLg.indexOf('%') ? 'pct':'px';
                    angular.element(this).parent().addClass('up-col-lg-'+parseInt(widthLg)+suffixLg);
                    if(document.querySelector('style').textContent.indexOf('.up-col-Lg-'+parseInt(widthLg)+suffixLg) == -1)
                        document.querySelector('style').textContent +=
                            ' @media (min-width:1200px){'+
                                '.up-col-lg-'+parseInt(widthLg)+suffixLg+'{'+
                                    'width:'+ widthLg+
                            '}}';
                }

                if(args.hasOwnProperty('padded'))
                    angular.element(this).parent().css('padding', '1em');
            });
            if(args.hasOwnProperty('left'))
                element.addClass('left');
            if(args.hasOwnProperty('wrap'))
                element.addClass('wrap');
            if(args.hasOwnProperty('right'))
                element.addClass('right');
            element.addClass('up-row');
        }
    };
}]);