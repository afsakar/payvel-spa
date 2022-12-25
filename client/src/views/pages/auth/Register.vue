<script setup>
import { useLayout } from '@/layout/composables/layout';
import { ref, computed } from 'vue';
import { useAuthStore } from '@/composables/auth';

const auth = useAuthStore();

const { layoutConfig, contextPath } = useLayout();
const form = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: ''
});

const logoUrl = computed(() => {
    return `${contextPath}layout/images/${layoutConfig.darkTheme.value ? 'logo-white' : 'logo-dark'}.svg`;
});

const handleRegister = async () => {
    await auth.register(form.value);
};
</script>

<template>
    <div class="surface-ground flex align-items-center justify-content-center min-h-screen min-w-screen overflow-hidden">
        <div class="flex flex-column align-items-center justify-content-center">
            <img :src="logoUrl" alt="Sakai logo" class="mb-5 w-6rem flex-shrink-0" />
            <div class="w-full surface-card py-8 px-5 sm:px-8" style="border-radius: 53px">
                <form @submit.prevent="handleRegister" class="flex-row space-y-10">
                    <div class="field">
                        <label for="name" class="block text-900 text-xl font-medium mb-2">Name</label>
                        <InputText id="name" type="text" placeholder="Name" class="w-full md:w-30rem" style="padding: 1rem" v-model="form.name" />
                        <span v-if="auth.errors.name" id="name" class="block p-error">{{ auth.errors.name[0] }}</span>
                    </div>

                    <div class="field">
                        <label for="email" class="block text-900 text-xl font-medium mb-2">Email</label>
                        <InputText id="email" type="email" placeholder="Email address" class="w-full md:w-30rem" style="padding: 1rem" v-model="form.email" />
                        <span v-if="auth.errors.email" id="email" class="block p-error">{{ auth.errors.email[0] }}</span>
                    </div>
                    <div class="field">
                        <label for="password" class="block text-900 font-medium text-xl mb-2">Password</label>
                        <Password id="password" v-model="form.password" placeholder="Password" :toggleMask="true" class="w-full" inputClass="w-full" inputStyle="padding:1rem" />
                        <span v-if="auth.errors.password" id="email" class="block p-error">{{ auth.errors.password[0] }}</span>
                    </div>
                    <div class="field">
                        <label for="password1" class="block text-900 font-medium text-xl mb-2">Password Confirm</label>
                        <Password id="password1" v-model="form.password_confirmation" placeholder="Password Confirm" :toggleMask="true" class="w-full" inputClass="w-full" inputStyle="padding:1rem" />
                    </div>

                    <Button type="submit" label="Sign In" class="w-full p-3 text-xl bg-primary hover:bg-primary-600"></Button>
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
