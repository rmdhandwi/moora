<script setup>
import { onMounted, ref } from "vue";
import { Head, router, useForm } from "@inertiajs/vue3";
import {
    Toast,
    useToast,
    Card,
    Button,
    Dialog,
    DataTable,
    Column,
    FloatLabel,
    Message,
    IconField,
    InputIcon,
    InputText,
    Select,
    ConfirmPopup,
    Tag,
    useConfirm,
    InputNumber,
} from "primevue";
import TemplateLayout from "@/Layouts/TemplateLayout.vue";
import { FilterMatchMode } from "@primevue/core/api";

// cek data dari controller
const props = defineProps({
    flash: Object,
    title: String,
    auth: Object,
    kriteria: Object,
});

// ceknotif
onMounted(() => {
    ShowToast();
    dataKriteria.value = props.kriteria.map((kriteria, index) => ({
        index: index + 1,
        ...kriteria,
    }));
});

const type = ref([{ name: "Benefit" }, { name: "Cost" }]);

const dt = ref([]);
const dataKriteria = ref([]);

// Filter untuk DataTable
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

// Fungsi untuk mengekspor DataTable ke CSV
const exportCSV = () => {
    dt.value.exportCSV();
};

// notif
const toast = useToast();
const ShowToast = () => {
    if (props.flash.notif && props.flash.message) {
        toast.add({
            severity: props.flash.notif || "info", // Gunakan 'info' sebagai default jika tidak ada notif
            summary:
                props.flash.notif.charAt(0).toUpperCase() +
                props.flash.notif.slice(1), // Kapitalisasi pertama
            detail: props.flash.message,
            life: 4000,
            group: "tc",
        });
    }
};

const showDialog = ref(false);
const dataDialog = ref(null);

const formKriteria = useForm({
    kriteria_id: "",
    nama_kriteria: "",
    bobot: null,
    type: "",
});

const addDialog = () => {
    formKriteria.reset();
    formKriteria.clearErrors();
    dataDialog.value = "add";
    showDialog.value = true;
};

const refresh = () => {
    return new Promise((resolve) => {
        router.visit(route("kriteriaPage"), {
            onSuccess: () => {
                resolve();
            },
        });
    });
};

const creteKriteria = () => {
    formKriteria.post(route("create.kriteria"), {
        onSuccess: async () => {
            await refresh();
            ShowToast();
            showDialog.value = false;
        },
        onError: () => {
            toast.add({
                severity: "error",
                summary: "Kesalahan",
                detail: "Terjadi kesalah",
                life: 3000,
                group: "tc",
            });
            showDialog.value = true;
        },
    });
};

const confirm = useConfirm();
const confirmEdit = (data) => {
    confirm.require({
        message: `Anda ingin mengedit user : ${data.nama_kriteria}`,
        icon: "pi pi-exclamation-triangle",
        rejectProps: {
            icon: "pi pi-times",
            label: "Cancel",
            severity: "secondary",
            outlined: true,
        },
        acceptProps: {
            icon: "pi pi-check",
            label: "Edit",
        },
        accept: () => {
            formKriteria.reset();
            formKriteria.clearErrors();
            dataDialog.value = "edit";
            formKriteria.kriteria_id = data.kriteria_id;
            formKriteria.bobot = data.bobot;
            formKriteria.type = data.type;
            formKriteria.nama_kriteria = data.nama_kriteria;
            showDialog.value = true;
        },
        reject: () => {
            toast.add({
                severity: "info",
                summary: "Dibatalkan",
                detail: "Pembaharuan data dibatalkan.",
                life: 3000,
                group: "tc",
            });
        },
    });
};
const updateKriteria = () => {
    formKriteria.put(route("update.kriteria", formKriteria.kriteria_id), {
        onSuccess: async () => {
            await refresh();
            ShowToast();
            showDialog.value = false;
        },
        onError: () => {
            toast.add({
                severity: "error",
                summary: "Kesalahan",
                detail: "Terjadi kesalah",
                life: 3000,
                group: "tc",
            });
            showDialog.value = true;
        },
    });
};

const confirmDelete = (data) => {
    confirm.require({
        message: `Anda ingin menghapus user : ${data.nama_kriteria}`,
        icon: "pi pi-exclamation-triangle",
        rejectProps: {
            icon: "pi pi-times",
            label: "Cancel",
            severity: "secondary",
            outlined: true,
        },
        acceptProps: {
            severity: "danger",
            icon: "pi pi-trash",
            label: "Hapus",
        },
        accept: () => {
            router.delete(route("destroy.kriteria", data.kriteria_id), {
                onSuccess: async () => {
                    await refresh();
                    ShowToast();
                    showDialog.value = false;
                },
                onError: () => {
                    ShowToast();
                    showDialog.value = true;
                },
            });
        },
        reject: () => {
            toast.add({
                severity: "info",
                summary: "Dibatalkan",
                detail: "Penghapusan data dibatalkan.",
                life: 3000,
                group: "tc",
            });
        },
    });
};
</script>

<template>
    <Head :title="props.title" />
    <Toast position="top-center" group="tc" />
    <ConfirmPopup></ConfirmPopup>
    <TemplateLayout :auth="props.auth" :title="props.title">
        <template #content>
            <div class="flex items-center justify-between mb-4">
                <span class="text-2xl font-bold">Data {{ props.title }}</span>
                <Button
                    @click="addDialog"
                    unstyled
                    class="px-4 py-2 bg-blue-500 hover:-translate-x-1 text-white rounded-md transition-all hover:bg-blue-600"
                >
                    <template #default>
                        <div class="flex gap-2 items-center">
                            <i class="pi pi-plus-circle"></i>
                            <span>Tambah data</span>
                        </div>
                    </template>
                </Button>
            </div>

            <!-- FormDialog -->
            <Dialog
                v-model:visible="showDialog"
                modal
                :header="
                    dataDialog === 'add'
                        ? 'Tambah Data Kriteria'
                        : 'Edit Data Kriteria'
                "
                :style="{ width: '30vw' }"
                :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            >
                <form
                    @submit.prevent="
                        dataDialog === 'add'
                            ? creteKriteria()
                            : updateKriteria()
                    "
                >
                    <div class="space-y-6">
                        <div class="mt-2">
                            <FloatLabel variant="on">
                                <InputText
                                    id="nama_kriteria"
                                    fluid
                                    v-model="formKriteria.nama_kriteria"
                                    :invalid="
                                        !!formKriteria.errors.nama_kriteria
                                    "
                                />
                                <label for="nama_kriteria">Nama Kriteria</label>
                            </FloatLabel>
                            <Message
                                v-if="!!formKriteria.errors.nama_kriteria"
                                severity="error"
                                size="small"
                                variant="simple"
                                >{{
                                    formKriteria.errors.nama_kriteria
                                }}</Message
                            >
                        </div>

                        <div class="mt-2">
                            <FloatLabel variant="on">
                                <InputNumber
                                    inputId="bobot"
                                    mode="decimal"
                                    showButtons
                                    :step="10"
                                    :min="0"
                                    :max="100"
                                    fluid
                                    v-model="formKriteria.bobot"
                                    :invalid="!!formKriteria.errors.bobot"
                                />
                                <label for="bobot">Bobot (%)</label>
                            </FloatLabel>
                            <Message
                                v-if="!!formKriteria.errors.bobot"
                                severity="error"
                                size="small"
                                variant="simple"
                                >{{ formKriteria.errors.bobot }}</Message
                            >
                        </div>

                        <div class="mt-2">
                            <FloatLabel variant="on">
                                <Select
                                    v-model="formKriteria.type"
                                    inputId="type"
                                    :options="type"
                                    optionLabel="name"
                                    optionValue="name"
                                    fluid
                                    :invalid="!!formKriteria.errors.type"
                                />
                                <label for="type">Type</label>
                            </FloatLabel>
                            <Message
                                v-if="formKriteria.errors.type"
                                severity="error"
                                size="small"
                                variant="simple"
                                >{{ formKriteria.errors.type }}</Message
                            >
                        </div>

                        <div class="flex justify-end items-center gap-2">
                            <Button
                                @click="showDialog = false"
                                unstyled
                                class="flex text-red-500 justify-between items-center gap-2 px-4 py-1 border border-red-500 rounded-md transition-all hover:bg-red-500 hover:text-white"
                            >
                                <span>Batal</span>
                                <i class="pi pi-times"></i>
                            </Button>
                            <Button
                                type="submit"
                                unstyled
                                class="flex text-blue-500 justify-between items-center gap-2 px-4 py-1 border border-blue-500 rounded-md transition-all hover:bg-blue-500 hover:text-white"
                            >
                                <span>Simpan</span>
                                <i class="pi pi-check-circle"></i>
                            </Button>
                        </div>
                    </div>
                </form>
            </Dialog>
            <!-- endForm -->

            <!-- Tabel User -->
            <Card>
                <template #content>
                    <DataTable
                        ref="dt"
                        :value="dataKriteria"
                        stripedRows
                        paginator
                        :rows="10"
                        scrollable
                        responsiveLayout="scroll"
                        :filters="filters"
                        resizableColumns
                        :rowsPerPageOptions="[5, 10, 20, 50, 100]"
                        columnResizeMode="fit"
                        size="large"
                    >
                        <!-- Pesan jika data kosong -->
                        <template #empty>
                            <div class="text-center">
                                Data Kriteria tidak Ditemukan
                            </div>
                        </template>

                        <!-- Header tabel -->
                        <template #header>
                            <div class="flex justify-between items-center">
                                <Button
                                    unstyled
                                    @click="exportCSV"
                                    class="px-4 py-2 bg-blue-500 hover:-translate-x-1 text-white rounded-md transition-all hover:bg-blue-600"
                                >
                                    <template #default>
                                        <div
                                            class="flex gap-2 items-center text-sm"
                                        >
                                            <i class="pi pi-external-link"></i>
                                            <span>Export</span>
                                        </div>
                                    </template>
                                </Button>
                                <IconField>
                                    <InputIcon>
                                        <i class="pi pi-search me-4" />
                                    </InputIcon>
                                    <InputText
                                        v-model="filters.global.value"
                                        placeholder="Cari Data Kriteria"
                                    />
                                </IconField>
                            </div>
                        </template>

                        <Column field="index" header="No" />
                        <Column field="kriteria_id" header="Kode Kriteria" />
                        <Column field="nama_kriteria" header="Nama Kriteria" />
                        <Column field="bobot" header="Bobot (%)" />
                        <Column field="type" header="Type" />

                        <Column header="Opsi" frozen alignFrozen="right">
                            <template #body="{ data }">
                                <div class="flex gap-2 items-center">
                                    <Button
                                        size="small"
                                        @click="confirmEdit(data)"
                                        icon="pi pi-pen-to-square"
                                        severity="info"
                                        outlined
                                    />
                                    <Button
                                        size="small"
                                        @click="confirmDelete(data)"
                                        icon="pi pi-trash"
                                        severity="danger"
                                        outlined
                                    />
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </template>
            </Card>
            <!-- endTabel -->
        </template>
    </TemplateLayout>
</template>

<style scoped></style>
