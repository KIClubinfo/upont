import alertify from 'alertifyjs';

import { API_PREFIX, DOOR_SERVICE_API } from '../../../config/constants';

import './ki-fix.html';

const DOOR_HISTORY_URL = DOOR_SERVICE_API + 'timetable';

/* @ngInject */
class Assos_KI_Ctrl {
    constructor($scope, $rootScope, $resource, $http, $sce, fixs, ownFixs, Paginate, Achievements) {
        $scope.fixs = this.assignFixs(fixs);
        $scope.ownFixs = this.assignFixs(ownFixs);
        $rootScope.displayTabs = true;
        $scope.kiTimetableUrl = DOOR_HISTORY_URL;

        $scope.doorServiceUp = false;
        $resource(DOOR_HISTORY_URL).get(
            () => {
                $scope.doorServiceUp = true;
            },
        );

        $scope.trustSrc = function(src) {
            return $sce.trustAsResourceUrl(src);
        };

        $scope.reload = function() {
            Paginate.first($scope.ownFixs).then((data) => {
                $scope.ownFixs = this.assignFixs(data);
            });
            Paginate.first($scope.fixs).then((data) => {
                $scope.fixs = this.assignFixs(data);
            });
        };

        $scope.post = function(msg, isFix) {
            if ($rootScope.isAdmissible)
                return;

            var params = {
                problem: msg,
                name: msg.substring(0, 20),
                fix: isFix,
            };

            $http.post(API_PREFIX + 'fixs', params).then(function() {
                $scope.fix = '';
                $scope.msg = '';
                alertify.success('Demande correctement envoyée !');
                Achievements.check();
                $scope.reload();
            });
        };

        $scope.changeStatus = function(fix) {
            $http.patch(API_PREFIX + 'fixs/' + fix.slug, { status: fix.status }).then(() => {
                $scope.reload();
            });
        };

        $scope.delete = function(fix) {
            alertify.confirm('Veux-tu vraiment supprimer le ticket ?', () => {
                $http.delete(API_PREFIX + 'fixs/' + fix.slug).then(() => {
                    $scope.reload();
                });
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
            closed: [],
        };

        for (const fix of fixs) {
            switch (fix.status) {
            case 'Non vu':
                fixs_buckets.unseen.push(fix);
                break;
            case 'En attente':
                fixs_buckets.waiting.push(fix);
                break;
            case 'En cours':
                fixs_buckets.doing.push(fix);
                break;
            case 'Résolu':
                fixs_buckets.done.push(fix);
                break;
            case 'Fermé':
                fixs_buckets.closed.push(fix);
                break;
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
