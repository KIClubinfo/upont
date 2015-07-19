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
        decimalPoint: ',',
        drillUpText: 'Retour à {series.name}'
    }
});

alertify.set({ labels: {
    ok     : 'Ok !',
    cancel : 'Annuler'
}});


angular.module('upont', ['ui.router', 'ngResource', 'ngAnimate', 'mgcrea.ngStrap', 'ngSanitize', 'angular-jwt', 'angular.filter', 'naif.base64', 'infinite-scroll', 'ui.bootstrap.datetimepicker', 'monospaced.elastic', 'youtube-embed', 'angular-redactor', 'piwik'])
    .config(function(redactorOptions) {
        redactorOptions.buttons = ['html', 'formatting', 'bold', 'italic', 'underline', 'deleted', 'unorderedlist', 'image', 'file', 'link', 'alignment', 'horizontalrule'];
        redactorOptions.lang = 'fr';
        redactorOptions.plugins = ['video', 'table', 'imagemanager'];
    });

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
var faIcons = [
'adjust','adn','align-center','align-justify','align-left','align-right',
'ambulance','anchor','android','angellist','angle-double-down',
'angle-double-left','angle-double-right','angle-double-up','angle-down',
'angle-left','angle-right','angle-up','apple','archive','area-chart',
'arrow-circle-down','arrow-circle-left','arrow-circle-o-down',
'arrow-circle-o-left','arrow-circle-o-right','arrow-circle-o-up',
'arrow-circle-right','arrow-circle-up','arrow-down','arrow-left','arrow-right',
'arrow-up','arrows','arrows-alt','arrows-h','arrows-v','asterisk','at',
'backward','ban','bar-chart','barcode','bars','bed','beer','behance',
'behance-square','bell','bell-o','bell-slash','bell-slash-o','bicycle',
'binoculars','birthday-cake','bitbucket','bitbucket-square','bold','bolt',
'bomb','book','bookmark','bookmark-o','briefcase','btc','bug','building',
'building-o','bullhorn','bullseye','bus','buysellads','calculator','calendar',
'calendar-o','camera','camera-retro','car','caret-down','caret-left',
'caret-right','caret-square-o-down','caret-square-o-left',
'caret-square-o-right','caret-square-o-up','caret-up','cart-arrow-down',
'cart-plus','cc','cc-amex','cc-discover','cc-mastercard','cc-paypal',
'cc-stripe','cc-visa','certificate','chain-broken','check','check-circle',
'check-circle-o','check-square','check-square-o','chevron-circle-down',
'chevron-circle-left','chevron-circle-right','chevron-circle-up','chevron-down',
'chevron-left','chevron-right','chevron-up','child','circle','circle-o',
'circle-o-notch','circle-thin','clipboard','clock-o','cloud','cloud-download',
'cloud-upload','code','code-fork','codepen','coffee','cog','cogs','columns',
'comment','comment-o','comments','comments-o','compass','compress',
'connectdevelop','copyright','credit-card','crop','crosshairs','css3','cube',
'cubes','cutlery','dashcube','database','delicious','desktop','deviantart',
'diamond','digg','dot-circle-o','download','dribbble','dropbox','drupal',
'eject','ellipsis-h','ellipsis-v','empire','envelope','envelope-o',
'envelope-square','eraser','eur','exchange','exclamation','exclamation-circle',
'exclamation-triangle','expand','external-link','external-link-square','eye',
'eye-slash','eyedropper','facebook','facebook-official','facebook-square',
'fast-backward','fast-forward','fax','female','fighter-jet','file',
'file-archive-o','file-audio-o','file-code-o','file-excel-o','file-image-o',
'file-o','file-pdf-o','file-powerpoint-o','file-text','file-text-o',
'file-video-o','file-word-o','files-o','film','filter','fire',
'fire-extinguisher','flag','flag-checkered','flag-o','flask','flickr',
'floppy-o','folder','folder-o','folder-open','folder-open-o','font','forumbee',
'forward','foursquare','frown-o','futbol-o','gamepad','gavel','gbp','gift',
'git','git-square','github','github-alt','github-square','glass','globe',
'google','google-plus','google-plus-square','google-wallet','graduation-cap',
'gratipay','h-square','hacker-news','hand-o-down','hand-o-left','hand-o-right',
'hand-o-up','hdd-o','header','headphones','heart','heart-o','heartbeat',
'history','home','hospital-o','html5','ils','inbox','indent','info',
'info-circle','inr','instagram','ioxhost','italic','joomla','jpy','jsfiddle',
'key','keyboard-o','krw','language','laptop','lastfm','lastfm-square','leaf',
'leanpub','lemon-o','level-down','level-up','life-ring','lightbulb-o',
'line-chart','link','linkedin','linkedin-square','linux','list','list-alt',
'list-ol','list-ul','location-arrow','lock','long-arrow-down','long-arrow-left',
'long-arrow-right','long-arrow-up','magic','magnet','male','map-marker','mars',
'mars-double','mars-stroke','mars-stroke-h','mars-stroke-v','maxcdn','meanpath',
'medium','medkit','meh-o','mercury','microphone','microphone-slash','minus',
'minus-circle','minus-square','minus-square-o','mobile','money','moon-o',
'motorcycle','music','neuter','newspaper-o','openid','outdent','pagelines',
'paint-brush','paper-plane','paper-plane-o','paperclip','paragraph','pause',
'paw','paypal','pencil','pencil-square','pencil-square-o','phone',
'phone-square','picture-o','pie-chart','pied-piper','pied-piper-alt',
'pinterest','pinterest-p','pinterest-square','plane','play','play-circle',
'play-circle-o','plug','plus','plus-circle','plus-square','plus-square-o',
'power-off','print','puzzle-piece','qq','qrcode','question','question-circle',
'quote-left','quote-right','random','rebel','recycle','reddit','reddit-square',
'refresh','renren','repeat','reply','reply-all','retweet','road','rocket','rss',
'rss-square','rub','scissors','search','search-minus','search-plus','sellsy',
'server','share','share-alt','share-alt-square','share-square','share-square-o',
'shield','ship','shirtsinbulk','shopping-cart','sign-in','sign-out','signal',
'simplybuilt','sitemap','skyatlas','skype','slack','sliders','slideshare',
'smile-o','sort','sort-alpha-asc','sort-alpha-desc','sort-amount-asc',
'sort-amount-desc','sort-asc','sort-desc','sort-numeric-asc',
'sort-numeric-desc','soundcloud','space-shuttle','spinner','spoon','spotify',
'square','square-o','stack-exchange','stack-overflow','star','star-half',
'star-half-o','star-o','steam','steam-square','step-backward','step-forward',
'stethoscope','stop','street-view','strikethrough','stumbleupon',
'stumbleupon-circle','subscript','subway','suitcase','sun-o','superscript',
'table','tablet','tachometer','tag','tags','tasks','taxi','tencent-weibo',
'terminal','text-height','text-width','th','th-large','th-list','thumb-tack',
'thumbs-down','thumbs-o-down','thumbs-o-up','thumbs-up','ticket','times',
'times-circle','times-circle-o','tint','toggle-off','toggle-on','train',
'transgender','transgender-alt','trash','trash-o','tree','trello','trophy',
'truck','try','tty','tumblr','tumblr-square','twitch','twitter',
'twitter-square','umbrella','underline','undo','university','unlock',
'unlock-alt','upload','usd','user','user-md','user-plus','user-secret',
'user-times','users','venus','venus-double','venus-mars','viacoin',
'video-camera','vimeo-square','vine','vk','volume-down','volume-off',
'volume-up','weibo','weixin','whatsapp','wheelchair','wifi','windows',
'wordpress','wrench','xing','xing-square','yahoo','yelp','youtube',
'youtube-play','youtube-square'
];
