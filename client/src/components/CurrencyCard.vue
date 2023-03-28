<script setup>
import { computed } from 'vue';

const props = defineProps({
    rate: {
        type: Object,
        required: true,
        validator: (value) => ['caret-up', 'caret-down', 'minus'].includes(value.status),
        default: () => ({ status: 'minus' })
    }
});

const rateClass = computed(() => {
    const iconClass = {
        'caret-up': 'ti ti-arrow-up text-green-500',
        'caret-down': 'ti ti-arrow-down text-red-500',
        minus: 'ti ti-minus text-gray-500'
    };

    return iconClass[props.rate.status];
});

const symbolClass = computed(() => {
    const iconClass = {
        EUR: 'ti ti-currency-euro text-blue-500',
        USD: 'ti ti-currency-dollar text-yellow-500',
        GBP: 'ti ti-currency-pound text-green-500'
    };

    return iconClass[props.rate.code];
});
</script>

<template>
    <!-- Component Start -->
    <div class="col-12 md:col-4 card">
        <div class="flex items-center justify-between gap-3">
            <span class="flex items-center justify-center gap-2 text-4xl font-bold">
                <i :class="`${symbolClass}`" /> <span>{{ props.rate.code }}</span> <i :class="`${rateClass}`"></i>
            </span>
            <div class="ml-3 text-right">
                <p>Buying: {{ props.rate.buying }}</p>
                <p>Sales: {{ props.rate.sales }}</p>
                <p class="text-sm">{{ props.rate.change }} ({{ props.rate.percent }}%)</p>
            </div>
        </div>
    </div>
    <!-- Component End  -->
</template>

<style></style>
