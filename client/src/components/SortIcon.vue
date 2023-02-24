<script setup>
import { computed } from 'vue';

const props = defineProps({
    name: {
        type: String,
        required: true,
        default: ''
    },
    sort: {
        type: String,
        required: true,
        default: 'asc'
    },
    column: {
        type: String,
        required: true,
        default: ''
    },
    activeColumn: {
        type: String,
        required: true,
        default: ''
    }
});

const icon = computed(() => {
    if (props.column !== props.activeColumn) {
        return 'pi pi-sort-alt';
    } else if (props.sort === 'asc') {
        return 'pi pi-sort-amount-up-alt';
    } else {
        return 'pi pi-sort-amount-down-alt';
    }
});
</script>

<template>
    <div @click="sa" :class="{ 'text-[var(--primary-color)]': props.column === props.activeColumn }" class="flex gap-2 items-center justify-start group hover:bg-[var(--surface-hover)] transition-all duration-300 px-2 py-2 rounded cursor-pointer">
        <span class="p-column-title">{{ name }}</span>
        <i :class="icon" />
    </div>
</template>
