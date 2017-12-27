import PublicRouter from './public';
import UsersRouter from './users';

export const Router = $stateProvider => {
    $stateProvider.state('root', {
        abstract: true,
        url: '/',
        template: '<div ui-view></div>'
    });

    PublicRouter($stateProvider);
    UsersRouter($stateProvider);
};

export default Router;
