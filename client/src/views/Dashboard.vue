<script setup>
import { onMounted, ref } from 'vue';
import { useAuthStore } from '@/composables/auth';
import { useHead } from '@unhead/vue';
import { getExchangeRate } from '@/composables/utils';
import CurrencyCard from '@/components/CurrencyCard.vue';

useHead({
    title: 'Dashboard'
});

const auth = useAuthStore();
const rates = ref([]);
const rateList = ref([]);

onMounted(async () => {
    await auth.getUser();
    rates.value = await getExchangeRate();
    rateList.value = Object.keys(rates.value[0].rates).map((key) => ({
        code: key,
        buying: rates.value[0].rates[key]['alis'],
        sales: rates.value[0].rates[key]['satis'],
        status: rates.value[0].rates[key]['d_yon'],
        change: rates.value[0].rates[key]['degisim'],
        percent: rates.value[0].rates[key]['d_oran']
    }));
});
</script>

<template>
    <div class="grid">
        <div class="col-12">
            <div class="card">
                <h5>{{ auth.user?.name }}</h5>
                <p>{{ auth.user?.email }}</p>
                <div class="md:flex md:items-start md:gap-2">
                    <currency-card v-for="(rate, index) in rateList" :key="index" :rate="rate" />
                </div>
            </div>
        </div>
    </div>
</template>
