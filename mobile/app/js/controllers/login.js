module
    .controller('LoginController', ['$scope', 'StorageService', '$http', 'PushNotifications', function($scope, StorageService, $http, PushNotifications) {
        $scope.username = '';
        $scope.password = '';

        $scope.signIn = function(username, password) {
            $http({method: 'POST', url: url + '/login', data: {'username': username, 'password': password}})
                .success(function (data, status, headers, config) {
                    var tokenData = (data.token).split('.')[1].replace(/-/g, '+').replace(/_/g, '/');
                    switch (tokenData.length % 4) {
                        case 0: { break; }
                        case 2: { tokenData += '=='; break; }
                        case 3: { tokenData += '='; break; }
                        default: return false;
                    }
                    tokenData = window.atob(tokenData);
                    StorageService.set('token_exp', tokenData.exp);
                    StorageService.set('token', data.token);

                    // On regarde si l'utilisateur a déjà répondu pour les notifications push
                    // Sinon on lui souhaite la bienvenue
                    if (StorageService.get('registered')) {
                        onsAlert('Connexion', 'Connecté avec succès !');
                    } else {
                        PushNotifications.initialize();
                    }

                    menu.setMainPage('views/events.html', {closeMenu: true});
                    menu.setSwipeable(true);

                    // On met en cache les données de l'utilisateur actuel
                    $http.get(url + '/users/' + username).success(function(data) {
                        StorageService.set('user', data);
                    });
                })
                .error(function (data, status, headers, config) {
                    // Supprime tout token en cas de mauvaise identification
                    if(StorageService.get('token')){
                        StorageService.remove('token');
                        StorageService.remove('token_exp');
                        StorageService.remove('user');
                    }
                    onsAlert('Connexion', 'Mauvaise combinaison identifiant/mot de passe !');
                });
        };

        $scope.reset = function(username) {
            if(username !== '') {
                $http({method: 'POST', url: url + '/resetting/request', data: {'username': username}})
                    .success(function () {
                        onsAlert('Succès', 'Un mail avec un lien pour réinitialiser le mot de passe a bien été envoyé à l\'adresse du compte.');
                        nav.popPage();
                    });
            } else {
                onsAlert('Erreur', 'Identifiant non fourni !');
            }
        };
    }
]);
