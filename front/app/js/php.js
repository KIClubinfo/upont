//Fichier définissant l'équivalent de fonctions php en javasale

function br2nl(varTest){
    return varTest.replace(/<br>/g, "\r");
}

function nl2br(str, is_xhtml) {
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}

function in_array(needle, haystack, recherche) {
    var length = haystack.length;
    for(var i = 0; i < length; i++) {
        //Petite customisation, pas besoin de renseigner l'argument recherche pour la fonction normale
        if(recherche){
            haystack[i] = haystack[i].sansAccent().toUpperCase();
            needle = needle.toUpperCase();
        }
        if(haystack[i] == needle) {
            return true;
        }
    }
    return false;
}

function array_search(needle, haystack, argStrict) {
    var strict = !! argStrict,
    key = '';

    if (haystack && typeof haystack === 'object' && haystack.change_key_case) {
        return haystack.search(needle, argStrict);
    }
    if (typeof needle === 'object' && needle.exec) {
        if (!strict) {
            var flags = 'i' + (needle.global ? 'g' : '') +
            (needle.multiline ? 'm' : '') +
            (needle.sticky ? 'y' : '');
            needle = new RegExp(needle.source, flags);
        }
        for (key in haystack) {
            if (needle.test(haystack[key])) {
                return key;
            }
        }
        return false;
    }

    for (key in haystack) {
        if ((strict && haystack[key] === needle) || (!strict && haystack[key] == needle)) {
            return key;
        }
    }

    return false;
}


function stripslashes(str) {
  return (str + '')
    .replace(/\\(.?)/g, function(s, n1) {
      switch (n1) {
        case '\\':
          return '\\';
        case '0':
          return '\u0000';
        case '':
          return '';
        default:
          return n1;
      }
    });
}

function addslashes(str) {
  return (str + '')
    .replace(/[\\"']/g, '\\$&')
    .replace(/\u0000/g, '\\0');
}

function htmlspecialchars(string, quote_style) {
    var optTemp = 0,
    i = 0,
    noquotes = false;
    if (typeof quote_style === 'undefined') {
        quote_style = 2;
    }
    string = string.toString()
        .replace(/&lt;/g, '<')
        .replace(/&gt;/g, '>');
    var OPTS = {
        'ENT_NOQUOTES': 0,
        'ENT_HTML_QUOTE_SINGLE': 1,
        'ENT_HTML_QUOTE_DOUBLE': 2,
        'ENT_COMPAT': 2,
        'ENT_QUOTES': 3,
        'ENT_IGNORE': 4
    };
    if (quote_style === 0) {
        noquotes = true;
    }
    if (typeof quote_style !== 'number') { // Allow for a single string or an array of string flags
        quote_style = [].concat(quote_style);
        for (i = 0; i < quote_style.length; i++) {
            // Resolve string input to bitwise e.g. 'PATHINFO_EXTENSION' becomes 4
            if (OPTS[quote_style[i]] === 0) {
                noquotes = true;
            } else if (OPTS[quote_style[i]]) {
                optTemp = optTemp || OPTS[quote_style[i]];
            }
        }
        quote_style = optTemp;
    }
    if (quote_style && OPTS.ENT_HTML_QUOTE_SINGLE) {
        string = string.replace(/&#0*39;/g, "'"); // PHP doesn't currently escape if more than one 0, but it should
        // string = string.replace(/&apos;|&#x0*27;/g, "'"); // This would also be useful here, but not a part of PHP
    }
    if (!noquotes) {
        string = string.replace(/&quot;/g, '"');
    }
    // Put this in last place to avoid escape being double-decoded
    string = string.replace(/&amp;/g, '&');

    return string;
}

function ucfirst(str) {
    str += '';
    var f = str.charAt(0).toUpperCase();
    return f + str.substr(1);
}

function ping(ip, callback) {
    if (!this.inUse) {
        this.status = 'unchecked';
        this.inUse = true;
        this.callback = callback;
        this.ip = ip;
        var _that = this;
        this.img = new Image();
        this.img.onload = function () {
            _that.inUse = false;
            _that.callback('responded');

        };
        this.img.onerror = function (e) {
            if (_that.inUse) {
                _that.inUse = false;
                _that.callback('responded', e);
            }

        };
        this.start = new Date().getTime();
        this.img.src = "http://" + ip;
        this.timer = setTimeout(function () {
            if (_that.inUse) {
                _that.inUse = false;
                _that.callback('timeout');
            }
        }, 1000);
    }
}

function empty(mixed_var) {
    var emptyValues = [null, false, 0, '', '0', undefined, {}, []];

    for (var i = 0; i < emptyValues.length; i++) {
        if (mixed_var === emptyValues[i]) {
            return true;
        }
    }

    if (typeof mixed_var === 'object') {
        for (var key in mixed_var) {
            return false;
        }
        return true;
    }

    return false;
}
