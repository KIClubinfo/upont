var apiPrefix = '/api/';
if (!location.origin)
     location.origin = location.protocol + '//' + location.host;

// Configuration de la langue
moment.locale('fr');
Highcharts.setOptions({
    lang: {
        months: ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'],
        weekdays: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
        shortMonths: ['Jan', 'Fev', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aout', 'Sept', 'Oct', 'Nov', 'Déc'],
        loading: 'Chargement en cours...',
        resetZoom: 'Réinitialiser le zoom',
        resetZoomTitle: 'Réinitialiser le zoom au niveau 1:1',
        thousandsSep: ' ',
        decimalPoint: ','
    }
});

alertify.set({ labels: {
    ok     : 'Ok !',
    cancel : 'Annuler'
}});

angular.module('upont', ['ui.router', 'ngResource', 'ngAnimate', 'mgcrea.ngStrap', 'ngSanitize', 'angular-jwt', 'angular.filter', 'naif.base64', 'infinite-scroll', 'ui.bootstrap.datetimepicker', 'monospaced.elastic']);

// Définition des constantes
var promos = [
    '011',
    '012',
    '013',
    '014',
    '015',
    '016',
    '017',
    '018'
];

var departments = [
    '1A',
    'GCC',
    'GCC-Archi',
    'GMM',
    'GI',
    'IMI',
    'SEGF',
    'VET'
];

var origins = [
    'Concours Commun',
    'AST',
    'Double Diplôme',
    'FCI Civil',
    'Ingénieur Élève',
    'ENSAVT',
    'Master',
    'Stagiaire',
    'AUE',
    'Autre'
];

var countries = [
    'France',
    'Algérie',
    'Allemagne',
    'Argentine',
    'Australie',
    'Brésil',
    'Bulgarie',
    'Cambodge',
    'Canada',
    'Chine',
    'Espagne',
    'États-Unis',
    'Grèce',
    'Italie',
    'Japon',
    'Liban',
    'Luxembourg',
    'Malaisie',
    'Mali',
    'Maroc',
    'Pologne',
    'République Tchèque',
    'Roumanie',
    'Russe',
    'Slovaquie',
    'Suède',
    'Tunisie',
    'Viêt Nam',
    'Autre'
];

// Web Application Fontawesome icons as of 4.3
var faIcons = ['adjust','anchor','archive','area-chart','arrows','arrows-h','arrows-v',
'asterisk','at','ban','bar-chart','barcode','bars','bed','beer','bell','bell-o','bell-slash',
'bell-slash-o','bicycle','binoculars','birthday-cake','bolt','bomb','book','bookmark',
'bookmark-o','briefcase','bug','building','building-o','bullhorn','bullseye','bus',
'calculator','calendar','calendar-o','camera','camera-retro','car','caret-square-o-down',
'caret-square-o-left','caret-square-o-right','caret-square-o-up','cart-arrow-down',
'cart-plus','cc','certificate','check','check-circle','check-circle-o','check-square',
'check-square-o','child','circle','circle-o','circle-o-notch','circle-thin','clock-o',
'cloud','cloud-download','cloud-upload','code','code-fork','coffee','cog','cogs',
'comment','comment-o','comments','comments-o','compass','copyright','credit-card',
'crop','crosshairs','cube','cubes','cutlery','database','desktop','diamond',
'dot-circle-o','download','ellipsis-h','ellipsis-v','envelope','envelope-o',
'envelope-square','eraser','exchange','exclamation','exclamation-circle',
'exclamation-triangle','external-link','external-link-square','eye','eye-slash',
'eyedropper','fax','female','fighter-jet','file-archive-o','file-audio-o',
'file-code-o','file-excel-o','file-image-o','file-pdf-o','file-powerpoint-o',
'file-video-o','file-word-o','film','filter','fire','fire-extinguisher','flag',
'flag-checkered','flag-o','flask','folder','folder-o','folder-open','folder-open-o',
'frown-o','futbol-o','gamepad','gavel','gift','glass','globe','graduation-cap',
'hdd-o','headphones','heart','heart-o','heartbeat','history','home','inbox','info',
'info-circle','key','keyboard-o','language','laptop','leaf','lemon-o','level-down',
'level-up','life-ring','lightbulb-o','line-chart','location-arrow','lock','magic',
'magnet','male','map-marker','meh-o','microphone','microphone-slash','minus',
'minus-circle','minus-square','minus-square-o','mobile','money','moon-o','motorcycle',
'music','newspaper-o','paint-brush','paper-plane','paper-plane-o','paw','pencil',
'pencil-square','pencil-square-o','phone','phone-square','picture-o','pie-chart',
'plane','plug','plus','plus-circle','plus-square','plus-square-o','power-off','print',
'puzzle-piece','qrcode','question','question-circle','quote-left','quote-right',
'random','recycle','refresh','reply','reply-all','retweet','road','rocket','rss',
'rss-square','search','search-minus','search-plus','server','share','share-alt',
'share-alt-square','share-square','share-square-o','shield','ship','shopping-cart',
'sign-in','sign-out','signal','sitemap','sliders','smile-o','sort','sort-alpha-asc',
'sort-alpha-desc','sort-amount-asc','sort-amount-desc','sort-asc','sort-desc',
'sort-numeric-asc','sort-numeric-desc','space-shuttle','spinner','spoon','square',
'square-o','star','star-half','star-half-o','star-o','street-view','suitcase','sun-o',
'tablet','tachometer','tag','tags','tasks','taxi','terminal','thumb-tack','thumbs-down',
'thumbs-o-down','thumbs-o-up','thumbs-up','ticket','times','times-circle','times-circle-o',
'tint','toggle-off','toggle-on','trash','trash-o','tree','trophy','truck','tty',
'umbrella','university','unlock','unlock-alt','upload','user','user-plus','user-secret',
'user-times','users','video-camera','volume-down','volume-off','volume-up','wheelchair',
'wifi','wrench'];
