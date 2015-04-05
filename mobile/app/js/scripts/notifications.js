// Configuration des notifications push
var pushNotification;
var gcmExpeditor = '124672424252';

//if(window.plugins) {
    module
        .factory('PushNotifications', ['$http', '$rootScope', 'StorageService', function ($http, $rootScope, StorageService) {
            return {
                initialize : function () {
                    pushNotification = window.plugins.pushNotification;

                    if (!StorageService.get('registeredId')) {
                        // On demande si l'utilisateur veut recevoir des notifications push
                        ons.notification.confirm({
                            title: 'Notifications Push',
                            message: 'Activer les notifications push te permettra de rester au courant de ce qui se passe même l\'appli éteinte.',
                            buttonLabels: ['>> Activer <<', 'Non merci'],
                            animation: 'default',
                            primaryButtonIndex: 1,
                            callback: function(index) {
                                if(index === 0) {
                                     if (device.platform == 'android' || device.platform == 'Android' || device.platform == "amazon-fireos") {
                                        pushNotification.register(
                                            function() {
                                                StorageService.set('registered', true);
                                                $rootScope.registered = true;
                                            },
                                            function (error) { onsAlert('Erreur', error); },
                                            {'senderID': gcmExpeditor, 'ecb': 'onNotificationGCM' }
                                        );
                                    }
                                    else if(device.platform == 'Win32NT'){
                                        pushNotification.register(
                                            function(result) {
                                                StorageService.set('registered', true);
                                                $rootScope.registered = true;
                                                StorageService.set('registeredId', result.uri);
                                                $http.post(url + '/own/devices', {device: result.uri, type: 'iOS'});
                                            },
                                            function (error) { onsAlert('Erreur', error); },
                                            {'channelName': channelName, 'ecb': 'onNotificationWP8'}
                                        );
                                    /*} else {
                                        pushNotification.register(
                                            function(token) {
                                                StorageService.set('registered', true);
                                                $rootScope.registered = true;
                                                StorageService.set('registeredId', token);
                                                $http.post(url + '/own/devices', {device: token, type: 'iOS'});
                                            },
                                            function (error) { onsAlert('Erreur', error); },
                                            {'badge': 'true', 'sound': 'true', 'alert': 'true', 'ecb': 'onNotificationAPN'}
                                        );*/
                                    }
                                } else {
                                    StorageService.set('registered', false);
                                    $rootScope.registered = false;
                                }
                            }
                        });
                    }
                },
                registerID : function (id) {
                    StorageService.set('registeredId', id);
                    $http.post(url + '/own/devices', {device: id, type: 'Android'});
                },
                unregister : function () {
                    pushNotification = window.plugins.pushNotification;

                    pushNotification.unregister(function () {
                        StorageService.set('registered', false);
                        $rootScope.registered = false;

                        if(StorageService.get('registeredId')) {
                            $http.delete(url + '/own/devices/' + StorageService.get('registeredId')).success(function() { onsAlert('Ok', 'Notifications Push désactivées'); });
                        }
                    });
                }
            };
        }]);
/*} else {
    module
        .factory('PushNotifications', ['$http', '$rootScope', 'StorageService', function ($http, $rootScope, StorageService) {
            return {
                initialize : function () {
                },
            };
        }]);
}*/

document.addEventListener('deviceready', function() {
    var elem = angular.element(document.querySelector('[ng-app]'));
    var injector = elem.injector();
    var service = injector.get('PushNotifications');
    service.initialize();
}, false);



// Fonctions gérant la récéption d'une notification
// iOS
/*function onNotificationAPN (event) {
    if (event.alert) {
        navigator.notification.alert(event.alert);
    }

    if (event.sound) {
        var snd = new Media(event.sound);
        snd.play();
    }

    if (event.badge) {
        pushNotification.setApplicationIconBadgeNumber(successHandler, errorHandler, event.badge);
    }
}*/

// Android et Amazon Fire OS
function onNotificationGCM(e) {
    switch(e.event) {
    case 'registered':
        if (e.regid.length > 0) {
            var elem = angular.element(document.querySelector('[ng-app]'));
            var injector = elem.injector();
            var service = injector.get('PushNotifications');
            service.registerID(e.regid);
        }
        break;

    case 'message':
        // Appli en cours d'utilisation
        if (e.foreground) {
            alert('');
        } else {
            if (e.coldstart) {
                // Appli lancée depuis le pannel des notifs
                alert('');
            } else {
                // Appli en background
                alert('');
            }
        }
        break;

    case 'error':
        console.log('ERROR -&gt; MSG:' + e.msg + '');
        break;

    default:
        console.log('EVENT -&gt; Unknown, an event was received and we do not know what it is');
        break;
    }
}

// Windows Phone
function onNotificationWP8(e) {
    if (e.type == 'toast' && e.jsonContent) {
        pushNotification.showToastNotification(successHandler, errorHandler, {
            'Title': e.jsonContent['wp:Text1'], 'Subtitle': e.jsonContent['wp:Text2'], 'NavigationUri': e.jsonContent['wp:Param']
        });
    }
}
