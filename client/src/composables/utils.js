import { computed } from 'vue';
import axios from 'axios';

export function formatPrice(price) {
    return price.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

export function dateFormat(date) {
    const d = new Date(date);
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${day}/${month}/${year}`;
}

export function formatPhoneNumber(phoneNumberString) {
    const cleaned = ('' + phoneNumberString).replace(/\D/g, '');
    const match = cleaned.match(/^(1|)?(\d{3})(\d{3})(\d{4})$/);
    if (match) {
        const intlCode = match[1] ? '+1 ' : '';
        return [intlCode, '(', match[2], ') ', match[3], '-', match[4]].join('');
    }
    return null;
}

export const selectedCompany = computed(() => {
    return JSON.parse(localStorage.getItem('selectedCompany'));
});

export async function getExchangeRate() {
    const response = await axios.get('/api/exchange-rates');
    return response.data;
}
