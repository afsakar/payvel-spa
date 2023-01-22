<script setup>
import { useHead } from '@unhead/vue';

useHead({
    titleTemplate: (title) => (title ? `${title} - Payvel` : 'Payvel'),
    link: [
        {
            rel: 'stylesheet',
            href: `/themes/vela-blue/theme.css`
        }
    ]
});
</script>

<template>
    <router-view />
</template>
