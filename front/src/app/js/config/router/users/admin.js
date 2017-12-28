import template_admin from 'upont/js/controllers/users/admin/index.html';

import template_admin_assos from 'upont/js/controllers/users/admin/assos.html';
import Admin_Assos_Ctrl from 'upont/js/controllers/users/admin/assos';
import template_admin_students from 'upont/js/controllers/users/admin/students.html';
import Admin_Students_Ctrl from 'upont/js/controllers/users/admin/students';

export const UsersAdminRouter = $stateProvider => {
    $stateProvider.state('root.users.admin', {
        url: 'admin',
        templateUrl: template_admin,
        abstract: true,
        data: {
            title: 'Administration - uPont',
            top: true
        }
    }).state('root.users.admin.assos', {
        url: '/assos',
        templateUrl: template_admin_assos,
        controller: Admin_Assos_Ctrl,
        data: {
            title: 'Administration des assos - uPont',
            top: true
        }
    }).state('root.users.admin.students', {
        url: '/eleves',
        templateUrl: template_admin_students,
        controller: Admin_Students_Ctrl,
        data: {
            title: 'Administration des élèves - uPont',
            top: true
        }
    });
};

export default UsersAdminRouter;
