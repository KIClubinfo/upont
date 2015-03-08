angular.module('upont')
    .directive('upCol', function() {
        return {
            link: function(scope, element, args){
                // if( typeof element.parent().attr("up-row") != 'undefined')
                //     element.wrap('<div></div>');
                if(args.upCol){
                    var width = args.upCol;
                    var suffix = width.indexOf('%') ? 'pct':'px;';
                    element.addClass('up-col-'+parseInt(width)+suffix);
                    if(document.querySelector('style').textContent.indexOf('.up-col-'+parseInt(width)+suffix) == -1){
                        document.querySelector('style').textContent +=
                            ' .up-col-'+parseInt(width)+suffix+'{'+
                                    'width:'+ width+
                            '}';
                    }
                }
                if( typeof element.parent().attr("padded") != 'undefined' && !element.parent().hasClass('padded'))
                    element.parent().addClass('padded');
            }
        };
    })
    .directive('upColXs', function(){
        return {
            link: function(scope, element, args){
                // if( typeof element.parent().attr("up-row") != 'undefined')
                //     element.wrap('<div></div>');
                if(args.upColXs){
                    var width = args.upColXs;
                    var suffix = width.indexOf('%') ? 'pct':'px;';
                    element.addClass('up-col-xs-'+parseInt(width)+suffix);
                    if(document.querySelector('style').textContent.indexOf('.up-col-xs-'+parseInt(width)+suffix) == -1){
                        document.querySelector('style').textContent +=
                            ' @media (max-width:768px){'+
                                ' .up-col-xs-'+parseInt(width)+suffix+'{'+
                                        'width:'+ width+' !important'+
                                '}}';
                    }
                }
                if( typeof element.parent().attr("padded") != 'undefined' && !element.parent().hasClass('padded'))
                    element.parent().addClass('padded');
            }
        };
    })
    .directive('upColSm', function(){
        return {
            link: function(scope, element, args){
                // if( typeof element.parent().attr("up-row") != 'undefined')
                //     element.wrap('<div></div>');
                if(args.upColSm){
                    var width = args.upColSm;
                    var suffix = width.indexOf('%') ? 'pct':'px';
                    element.addClass('up-col-sm-'+parseInt(width)+suffix);
                    if(document.querySelector('style').textContent.indexOf('.up-col-sm-'+parseInt(width)+suffix) == -1)
                        document.querySelector('style').textContent +=
                            ' @media (min-width:768px) and (max-width:992px){'+
                                '.up-col-sm-'+parseInt(width)+suffix+'{'+
                                    'width:'+ width+' !important'+
                            '}}';
                }
                if( typeof element.parent().attr("padded") != 'undefined' && !element.parent().hasClass('padded'))
                    element.parent().addClass('padded');
            }
        };
    })
    .directive('upColMd', function(){
        return {
            link: function(scope, element, args){
                // if( typeof element.parent().attr("up-row") != 'undefined')
                //     element.wrap('<div></div>');
                if(args.upColMd){
                    var width = args.upColMd;
                    var suffix = width.indexOf('%') ? 'pct':'px';
                    element.addClass('up-col-md-'+parseInt(width)+suffix);
                    if(document.querySelector('style').textContent.indexOf('.up-col-md-'+parseInt(width)+suffix) == -1)
                        document.querySelector('style').textContent +=
                            ' @media (min-width:992px) and (max-width:1200px){'+
                                '.up-col-md-'+parseInt(width)+suffix+'{'+
                                    'width:'+ width+' !important'+
                            '}}';
                }
                if( typeof element.parent().attr("padded") != 'undefined' && !element.parent().hasClass('padded'))
                    element.parent().addClass('padded');
            }
        };
    })
    .directive('upColLg', function(){
        return {
            link: function(scope, element, args){
                // if( typeof element.parent().attr("up-row") != 'undefined')
                //     element.wrap('<div></div>');
                if(args.upColLg){
                    var width = args.upColLg;
                    var suffix = width.indexOf('%') ? 'pct':'px';
                    element.addClass('up-col-lg-'+parseInt(width)+suffix);
                    if(document.querySelector('style').textContent.indexOf('.up-col-Lg-'+parseInt(width)+suffix) == -1)
                        document.querySelector('style').textContent +=
                            ' @media (min-width:1200px){'+
                                '.up-col-lg-'+parseInt(width)+suffix+'{'+
                                    'width:'+ width+' !important'+
                            '}}';
                }
                if( typeof element.parent().attr("padded") != 'undefined' && !element.parent().hasClass('padded'))
                    element.parent().addClass('padded');
            }
        };
    });