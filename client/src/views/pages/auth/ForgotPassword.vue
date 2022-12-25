<script setup>
import { useLayout } from '@/layout/composables/layout';
import { ref, computed } from 'vue';
import { useAuthStore } from '@/composables/auth';

const auth = useAuthStore();

const { layoutConfig, contextPath } = useLayout();
const form = ref({
    email: ''
});

const logoUrl = computed(() => {
    return `${contextPath}layout/images/${layoutConfig.darkTheme.value ? 'logo-white' : 'logo-dark'}.svg`;
});

const handleForgotPassword = async () => {
    await auth.forgotPassword(form.value).then(() => (form.value.email = ''));
};
</script>

<template>
    <div class="surface-ground flex align-items-center justify-content-center min-h-screen min-w-screen overflow-hidden">
        <div class="flex flex-column align-items-center justify-content-center">
            <img :src="logoUrl" alt="Sakai logo" class="mb-5 w-6rem flex-shrink-0" />
            <Message class="w-full" severity="success" v-if="auth.successMessage">{{ auth.successMessage }}</Message>
            <div class="w-full surface-card py-8 px-5 sm:px-8" style="border-radius: 53px">
                <form @submit.prevent="handleForgotPassword">
                    <div class="field mb-5">
                        <label for="email" class="block text-900 text-xl font-medium">Email</label>
                        <InputText id="email" type="email" placeholder="Email address" class="w-full md:w-30rem" style="padding: 1rem" v-model="form.email" />
                        <span v-if="auth.errors.email" id="email" class="block p-error">{{ auth.errors.email[0] }}</span>
                    </div>

                    <div class="flex justify-content-end mb-5 gap-5">
                        <router-link class="flex align-items-center justify-content-center gap-2 font-medium no-underline text-right cursor-pointer" to="/login" style="color: var(--primary-color)">
                            <span class="pi pi-arrow-left"></span> <span>Go back</span>
                        </router-link>
                    </div>

                    <Button type="submit" label="Reset Password" class="w-full p-3 text-xl bg-primary hover:bg-primary-600"></Button>
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped>
.pi-eye {
    transform: scale(1.6);
    margin-right: 1rem;
}

.pi-eye-slash {
    transform: scale(1.6);
    margin-right: 1rem;
}
</style>
