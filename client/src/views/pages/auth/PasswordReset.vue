<script setup>
import { useLayout } from '@/layout/composables/layout';
import { ref, computed } from 'vue';
import { useAuthStore } from '@/composables/auth';
import { useRoute } from 'vue-router';

const auth = useAuthStore();
const route = useRoute();

const { layoutConfig, contextPath } = useLayout();
const form = ref({
    password: '',
    password_confirmation: '',
    email: route.query.email,
    token: route.params.token
});

const logoUrl = computed(() => {
    return `${contextPath}layout/images/${layoutConfig.darkTheme.value ? 'logo-white' : 'logo-dark'}.svg`;
});

const handleResetPassword = async () => {
    await auth.resetPassword(form.value);
};
</script>

<template>
    <div class="surface-ground flex align-items-center justify-content-center min-h-screen min-w-screen overflow-hidden">
        <div class="flex flex-column align-items-center justify-content-center">
            <img :src="logoUrl" alt="Sakai logo" class="mb-5 w-6rem flex-shrink-0" />
            <div class="surface-card py-8 px-5" style="border-radius: 53px">
                <form @submit.prevent="handleResetPassword">
                    <div class="field mb-5">
                        <label for="password" class="block text-900 font-medium text-xl">Password</label>
                        <Password id="password" v-model="form.password" placeholder="Password" :toggleMask="true" class="w-full" inputClass="w-full" inputStyle="padding:1rem"></Password>
                        <span v-if="auth.errors.password" id="password" class="block p-error">{{ auth.errors.password[0] }}</span>
                    </div>
                    <div class="field mb-5">
                        <label for="password_confirmation" class="block text-900 font-medium text-xl">Password Confirm</label>
                        <Password id="password_confirmation" v-model="form.password_confirmation" placeholder="Password Confirm" :toggleMask="true" class="w-full" inputClass="w-full" inputStyle="padding:1rem"></Password>
                        <span v-if="auth.errors.password_confirmation" id="password_confirmation" class="block p-error">{{ auth.errors.password_confirmation[0] }}</span>
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
