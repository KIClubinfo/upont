import alertify from 'alertifyjs';

import { API_PREFIX } from 'upont/js/config/constants';

import './ki-fix.html';

/* @ngInject */
class Assos_KI_Ctrl {
    constructor($scope, $rootScope, $resource, $http, fixs, ownFixs, Paginate, Achievements) {
        $scope.fixs = this.assignFixs(fixs);
        $scope.ownFixs = this.assignFixs(ownFixs);
        $rootScope.displayTabs = true;

        $scope.reload = function() {
            Paginate.first($scope.ownFixs).then(function(response){
                $scope.ownFixs = this.assignFixs(response.data);
            });
            Paginate.first($scope.fixs).then(function(response){
                $scope.fixs = this.assignFixs(response.data);
            });
        };

        $scope.post = function(msg, isFix) {
            if($rootScope.isAdmissible)
                return;

            var params  = {
                problem: msg,
                name: msg.substring(0, 20),
                fix: isFix
            };

            $http.post(API_PREFIX + 'fixs', params).then(function(){
                $scope.fix = '';
                $scope.msg = '';
                alertify.success('Demande correctement envoyée !');
                Achievements.check();
                $scope.reload();
            });
        };

        $scope.changeStatus = function(fix) {
            $http.patch(API_PREFIX + 'fixs/' + fix.slug, {status: fix.status}).then(function(){
                $scope.reload();
            });
        };

        $scope.delete = function(fix) {
            alertify.confirm('Veux-tu vraiment supprimer le ticket ?', function(e) {
                if (e) {
                    $http.delete(API_PREFIX + 'fixs/' + fix.slug).then(function(){
                        $scope.reload();
                    });
                }
            });
        };
    }

    assignFixs(fixs) {
        fixs.data = this.reorderFixsByStatus(fixs.data);
        return fixs;
    }

    reorderFixsByStatus(fixs) {
        const fixs_buckets = {
            unseen: [],
            waiting: [],
            doing: [],
            done: [],
            closed: []
        };

        for (const fix of fixs) {
            switch (fix.status) {
                case 'Non vu': fixs_buckets.unseen.push(fix); break;
                case 'En attente': fixs_buckets.waiting.push(fix); break;
                case 'En cours': fixs_buckets.doing.push(fix); break;
                case 'Résolu': fixs_buckets.done.push(fix); break;
                case 'Fermé': fixs_buckets.closed.push(fix); break;
            }
        }

        return [
            ...fixs_buckets.unseen,
            ...fixs_buckets.waiting,
            ...fixs_buckets.doing,
            ...fixs_buckets.done,
            ...fixs_buckets.closed,
        ];
    }
}

export default Assos_KI_Ctrl;
