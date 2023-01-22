<script setup>
import { ref } from 'vue';
import { useToast } from 'primevue/usetoast';
import { useCategoryStore } from '@/composables/category';

const props = defineProps({
    category: {
        type: Object,
        required: true,
        default: () => ({})
    },
    isEdit: {
        type: Boolean,
        required: true,
        default: false
    }
});

const options = ref([
    {
        label: 'Income',
        value: 'income'
    },
    {
        label: 'Expense',
        value: 'expense'
    }
]);

const category = useCategoryStore();
const toast = useToast();
const formData = ref(new FormData());
const emit = defineEmits(['toggleModal']);
const showModal = ref(false);

function toggleModal() {
    showModal.value = !showModal.value;
    emit('toggleModal', showModal.value);
}

const form = ref({
    name: props.isEdit && props.category.name !== null ? props.category.name : '',
    type: props.isEdit && props.category.type !== null ? props.category.type : ''
});

const submit = async () => {
    formData.value.append('name', form.value.name);
    formData.value.append('type', form.value.type);
    if (props.isEdit) {
        formData.value.append('_method', 'PUT');
        await category.updateCategory(formData.value, props.category.id).then(() => {
            formData.value = new FormData();
            if (category.respStatus) {
                toggleModal();
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Category updated successfully!', life: 3000 });
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            }
        });
    } else {
        await category.createCategory(formData.value).then(() => {
            formData.value = new FormData();
            if (category.respStatus) {
                toggleModal();
                emit('newCategory', form.value);
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Category created successfully!', life: 3000 });
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            }
        });
    }
};
</script>

<template>
    <form class="grid" @submit.prevent="submit">
        <div class="field col-12 m-0">
            <label>Name</label>
            <InputText class="w-full" placeholder="Name" v-model="form.name" />
            <span v-if="category.errors.name" id="name" class="block p-error">{{ category.errors.name[0] }}</span>
        </div>

        <div class="field col-12 m-0">
            <label>Type</label>
            <Dropdown :options="options" option-label="label" option-value="value" class="w-full" placeholder="Select Type" v-model="form.type" />
            <span v-if="category.errors.type" id="type" class="block p-error">{{ category.errors.type[0] }}</span>
        </div>

        <Button type="submit" label="Submit" class="w-full p-3 text-lg bg-primary hover:bg-primary-600 mt-2 mx-3"></Button>
    </form>
</template>
