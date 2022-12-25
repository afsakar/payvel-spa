<script setup>
import { onMounted } from 'vue';
import { useAuthStore } from '../composables/auth';

const auth = useAuthStore();

onMounted(async () => {
    await auth.getUser();
});
</script>

<template>
    <div class="grid">
        <div class="col-12">
            <div class="card">
                <h5>{{ auth.user?.name }}</h5>
                <p>{{ auth.user?.email }}</p>
            </div>
        </div>
    </div>
</template>
