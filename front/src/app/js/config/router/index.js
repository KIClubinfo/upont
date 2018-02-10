import PublicRouter from './public';
import UsersRouter from './users';

export const Router = ['$stateProvider', $stateProvider => {
    $stateProvider.state('root', {
        abstract: true,
        url: '/',
        template: '<div ui-view></div>'
    });

    UsersRouter($stateProvider);
    PublicRouter($stateProvider);
}];

export default Router;
