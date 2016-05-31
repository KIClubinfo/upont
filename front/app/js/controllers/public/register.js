angular.module('upont')
    .controller('Register_Ctrl', ['$scope', '$http', '$location', function($scope, $http, $location) {
        $('#first-name-input').focus();

        $scope.submit = function(firstName, lastName, email) {
            if (firstName === undefined || lastName === undefined || email === undefined) {
                alertify.error('Au moins un des champs n\'est pas rempli');
                return;
            }

            var regex = /@eleves\.enpc\.fr$/;
            if (!regex.test(email)) {
                alertify.error('Désolé, seules les adresses des Ponts sont acceptées !');
            }

            $http.post(apiPrefix + 'users', {email: email, lastName: lastName, firstName: firstName})
                .success(function(){
                    alertify.success('Mail envoyé ! Redirection...');
                    $location.path('/');
                })
                .error(function(){
                    alertify.error('Un utilisateur avec cette adresse existe déjà');
                })
            ;
        };
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.register', {
                url: 'inscription',
                controller: 'Register_Ctrl',
                templateUrl: 'controllers/public/register.html',
            })
        ;
    }]);
