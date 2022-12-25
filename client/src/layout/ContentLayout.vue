<script setup>
import AppLayout from '@/layout/AppLayout.vue';
</script>

<template>
    <AppLayout>
        <div class="grid">
            <div class="col-12">
                <div class="card">
                    <router-view />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
