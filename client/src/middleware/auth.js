export default function auth({ next, router }) {
    if (!localStorage.getItem('isLoggedIn')) {
        next({path: '/login'});
    } else {
        next();
    }
}
