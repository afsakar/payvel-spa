import axios from 'axios';

axios.defaults.withCredentials = true;
axios.defaults.baseURL = 'http://localhost:8000';
if (localStorage.getItem('selectedCompany')) {
    axios.defaults.headers.common['company_id'] = JSON.parse(localStorage.getItem('selectedCompany')).id;
}
axios.defaults.headers.common['Content-Type'] = 'application/json';
