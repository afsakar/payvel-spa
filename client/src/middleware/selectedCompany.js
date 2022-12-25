export default function selectedCompany({ next, router }) {
    if (!localStorage.getItem('selectedCompany')) {
        router.push({ name: 'SelectCompany' });
    }
}