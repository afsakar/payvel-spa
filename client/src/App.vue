<script setup>
import { useHead } from '@unhead/vue';
import { onMounted, ref } from 'vue';
import { useAuthStore } from '@/composables/auth';
import Loader from '@/components/Loader.vue';

useHead({
    titleTemplate: (title) => (title ? `${title} - Payvel` : 'Payvel'),
    link: [
        {
            rel: 'stylesheet',
            href: `/themes/vela-blue/theme.css`
        }
    ]
});

const auth = useAuthStore();
const loading = ref(true);

onMounted(async () => {
    await auth.getUser();
    loading.value = false;
});
</script>

<template>
    <Loader v-if="loading" />
    <router-view v-else />
</template>
