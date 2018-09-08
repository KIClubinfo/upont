//Fichier définissant l'équivalent de fonctions php en javasale

export function nl2br(str, is_xhtml) {
    const breakTag = (is_xhtml || typeof is_xhtml === 'undefined')
        ? '<br />'
        : '<br>';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}

export function ucfirst(str) {
    str += '';
    const f = str.charAt(0).toUpperCase();
    return f + str.substr(1);
}
