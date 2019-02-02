import alertify from 'alertifyjs';

import { API_PREFIX } from 'upont/js/config/constants';

angular.module('upont')
    .controller('Messages_Ctrl', [
        '$scope', 'messages', ($scope, messages) => {
            $scope.messages = messages;
        },
    ])
    .controller('Publications_List_Messages_Ctrl', [
        '$scope',
        '$resource',
        '$http',
        'messages',
        'Paginate',
        ($scope, $resource, $http, messages, Paginate) => {
            $scope.messages = messages;
            $scope.next = function () {
                Paginate.next($scope.messages).then((data) => {
                    $scope.messages = data;
                });
            };

            // Modification/suppression des messages
            $scope.modify = function (message) {
                const index = $scope.messages.data.indexOf(message);

                // On demande confirmation
                alertify.prompt('Tu peux modifier ton message :', '', function (e, str) {
                    if (e) {
                        $http.patch(API_PREFIX + 'newsitems/' + $scope.messages.data[index].slug, { text: str }).then(function () {
                            $scope.messages.data[index].text = str;
                            alertify.success('Message correctement édité');
                        });
                    }
                }, $scope.messages.data[index].text);
            };

            $scope.delete = function (message) {
                const index = $scope.messages.data.indexOf(message);

                // On demande confirmation
                alertify.confirm('Est-ce vraiment ce que tu veux ?', () => {
                    $resource(API_PREFIX + 'newsitems/' + $scope.messages.data[index].slug).delete(function () {
                        $scope.messages.data.splice(index, 1);
                    });
                });
            };

            $scope.$on('newMessage', () => {
                Paginate.first($scope.messages).then(data => {
                    $scope.messages = data;
                });
            });
        },
    ]);
