export default function redirectIfAuthenticated({ next, router }) {
    if (localStorage.getItem('isLoggedIn')) {
        return router.go(-1);
    } else {
        return next();
    }
}
