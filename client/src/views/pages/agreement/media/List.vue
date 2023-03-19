<script setup>
import { useAgreementStore } from '@/composables/agreement';
import { computed, onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import { useToast } from 'primevue/usetoast';
import { selectedCompany } from '@/composables/utils';

const route = useRoute();
const agreementID = route.params.agreementID;
const mediaList = ref([]);

const toast = useToast();
const agreementStore = useAgreementStore();
const file = ref(null);
const name = ref(null);
const loading = ref(false);
const defaultFile = ref({
    name: '',
    url: null
});

const changePreview = (event) => {
    const preview = event.target.files[0];
    defaultFile.value.url = URL.createObjectURL(preview);
    defaultFile.value.name = preview.name;
};

const clearLogo = () => {
    file.value = null;
    defaultFile.value.url = null;
    defaultFile.value.name = '';
};

onMounted(async () => {
    loading.value = true;
    await agreementStore.getAgreements(selectedCompany.value.id);
    await agreementStore.getMedias(selectedCompany.value.id, agreementID);
    mediaList.value = agreementStore.medias;
    loading.value = false;
});

function uploadFile() {
    const formData = new FormData();
    formData.append('file', file.value);
    formData.append('name', name.value);
    if (!name.value && name.value.length < 3) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Please enter a file name greater than 3 characters!', life: 3000 });
    } else {
        agreementStore.uploadMedia(selectedCompany.value.id, agreementID, formData).then(() => {
            if (agreementStore.respStatus) {
                clearLogo();
                toast.add({ severity: 'success', summary: 'Successful', detail: 'File uploaded successfully!', life: 3000 });
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            }
        });
    }
}

function calculateFilesize(size) {
    if (size < 1024) {
        return size + ' bytes';
    } else if (size > 1024 && size < 1048576) {
        return (size / 1024).toFixed(2) + ' KB';
    } else if (size > 1048576) {
        return (size / 1048576).toFixed(2) + ' MB';
    }
}
const selectedFile = ref(null);
const showModal = ref(false);
function showMedia(file) {
    showModal.value = true;
    selectedFile.value = file;
}

const label = computed(() => {
    if (name.value && name.value.length > 3) {
        return 'Upload';
    } else {
        return 'Please enter a file name greater than 3 characters';
    }
});

function deleteItem(id) {
    console.log(id);
    agreementStore.deleteMedia(selectedCompany.value.id, agreementID, id).then(() => {
        if (agreementStore.respStatus) {
            toast.add({ severity: 'success', summary: 'Successful', detail: 'File deleted successfully!', life: 3000 });
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        }
    });
}

const mimes = ref(['pdf', 'doc', 'docx', 'xls', 'xlsx']);
</script>

<template>
    <Toolbar class="col-12 my-3">
        <template #start>
            <p class="text-xl font-bold">Agreement Medias</p>
        </template>

        <template #end>
            <router-link to="/agreements">
                <Button label="Go back" icon="pi pi-arrow-left" class="mr-2 p-button-success" />
            </router-link>
        </template>
    </Toolbar>

    <div class="card">
        <div class="mb-5">
            <div class="grid">
                <label class="md:col-6 col-12">
                    <InputText v-model="name" class="w-full" placeholder="File name" />
                    <span v-if="agreementStore.errors.name" id="name" class="block p-error">{{ agreementStore.errors.name[0] }}</span>
                </label>
                <label class="md:col-6 col-12">
                    <span class="bg-primary py-2 border-round text-center cursor-pointer font-bold hover:bg-primary-600 block">{{ defaultFile.name ? defaultFile.name : 'Select File' }}</span>
                    <input type="file" id="logo" class="p-sr-only" @change="changePreview" @input="file = $event.target.files[0]" />
                    <span v-if="agreementStore.errors.file" id="file" class="block p-error">{{ agreementStore.errors.file[0] }}</span>
                </label>
            </div>
            <Button :disabled="!name || name.length < 3" :label="label" class="font-bold mt-5 block w-full" @click="uploadFile" />
        </div>
    </div>

    <div class="card">
        <DataTable :loading="loading" :value="mediaList" stripedRows responsiveLayout="scroll" dataKey="order" class="p-datatable-sm">
            <Column columnKey="order" style="width: 30rem" field="file_name" header="File Name" />
            <Column columnKey="order" style="width: 10rem" field="name" header="Original Name" />
            <Column columnKey="order" field="extension" header="File Type" />
            <Column columnKey="order" field="size" header="File Size">
                <template #body="slotProps">
                    {{ calculateFilesize(slotProps.data.size) }}
                </template>
            </Column>
            <Column header="" style="width: 15rem">
                <template #body="slotProps">
                    <div class="flex justify-items-end items-end gap-3">
                        <Button v-if="!mimes.includes(slotProps.data.extension)" icon="pi pi-eye" @click="showMedia(slotProps.data.original_url)" class="p-button-success" />
                        <a :href="slotProps.data.original_url" v-else target="_blank" class="p-button-success">
                            <Button icon="pi pi-download" />
                        </a>
                        <Button icon="pi pi-trash" @click="deleteItem(slotProps.data.uuid)" class="p-button-danger" />
                    </div>
                </template>
            </Column>
        </DataTable>
    </div>

    <Dialog v-model:visible="showModal" :modal="true" contentClass="overflow-auto px-2 py-3" :closable="false" :dismissableMask="true" :showHeader="false" :contentStyle="{ width: '80vw', height: 'auto' }">
        <!-- <object :data="selectedFile" type="application/pdf" width="100%" height="100%">
            <embed :src="selectedFile" type="application/pdf" />
        </object> -->
        <!-- <iframe :src="selectedFile" class="w-full h-full overflow-hidden" /> -->
        <img :src="selectedFile" class="w-full h-full overflow-auto" />
        <template #footer>
            <Button label="Close" icon="pi pi-times" class="p-button-danger" @click="showModal = false" />
        </template>
    </Dialog>
</template>
