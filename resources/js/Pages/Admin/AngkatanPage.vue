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
} from "primevue";
import TemplateLayout from "@/Layouts/TemplateLayout.vue";
import { FilterMatchMode } from "@primevue/core/api";

// cek data dari controller
const props = defineProps({
    flash: Object,
    title: String,
    auth: Object,
    angkatan: Object,
});

// ceknotif
onMounted(() => {
    ShowToast();

    dataAngkatan.value = props.angkatan.map((angkatan, index) => ({
        index: index + 1,
        ...angkatan,
    }));

    getYears();
});

const dt = ref([]);
const dataAngkatan = ref([]);
const years = ref([]);
// Method to generate years
const getYears = () => {
    const currentYear = new Date().getFullYear();
    const startYear = currentYear - 6;
    const endYear = currentYear;

    years.value = [];
    for (let year = startYear; year <= endYear; year++) {
        years.value.push({ tahun: year });
    }
};

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

const formAngkatan = useForm({
    angkatan_id: "",
    tahun_angkatan: "",
    jurusan: "Teknik Informatika",
});

const addDialog = () => {
    formAngkatan.reset();
    formAngkatan.clearErrors();
    dataDialog.value = "add";
    showDialog.value = true;
};

const creteAngkatan = () => {
    formAngkatan.post(route("create.angkatan"), {
        onSuccess: () => {
            refresh();
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
        message: `Anda ingin mengedit Angkatan : ${data.tahun_angkatan}?`,
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
            formAngkatan.reset();
            formAngkatan.clearErrors();
            dataDialog.value = "edit";
            formAngkatan.angkatan_id = data.angkatan_id;
            formAngkatan.jurusan = data.jurusan;
            formAngkatan.tahun_angkatan = data.tahun_angkatan;
            showDialog.value = true;
        },
        reject: () => {
            toast.add({
                severity: "info",
                summary: "Dibatalkan",
                detail: "Pembaharuan data dibatalkan",
                life: 3000,
                group: "tc",
            });
        },
    });
};

const refresh = () => {
    router.visit(route("angkatanPage"));
};

const updateAngkatan = () => {
    formAngkatan.put(route("update.angkatan", formAngkatan.angkatan_id), {
        onSuccess: () => {
            refresh();
            setTimeout(() => {
                ShowToast();
            }, 500);
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
        message: `Anda ingin menghapus user : ${data.tahun_angkatan}`,
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
            router.delete(route("destroy.angkatan", data.angkatan_id), {
                onSuccess: () => {
                    refresh();
                    setTimeout(() => {
                        ShowToast();
                    }, 500);
                },
            });
        },
        reject: () => {
            toast.add({
                severity: "info",
                summary: "dibatalkan",
                detail: "Penghapusan data dibatalkan",
                life: 3000,
                group: "tc",
            });
        },
    });
};
</script>

<template>
    <Toast position="top-center" group="tc" />
    <ConfirmPopup></ConfirmPopup>
    <Head :title="props.title" />
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
                        ? 'Tambah Data Angkatan'
                        : 'Edit Data Angkatan'
                "
                :style="{ width: '30vw' }"
                :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            >
                <form
                    @submit.prevent="
                        dataDialog === 'add'
                            ? creteAngkatan()
                            : updateAngkatan()
                    "
                >
                    <div class="space-y-6">
                        <div class="mt-2">
                            <FloatLabel variant="on">
                                <Select
                                    v-model="formAngkatan.tahun_angkatan"
                                    inputId="user"
                                    :options="years"
                                    optionLabel="tahun"
                                    optionValue="tahun"
                                    fluid
                                    :invalid="
                                        !!formAngkatan.errors.tahun_angkatan
                                    "
                                />
                                <label for="user">Tahun Pengguna</label>
                            </FloatLabel>
                            <Message
                                v-if="formAngkatan.errors.tahun_angkatan"
                                severity="error"
                                size="small"
                                variant="simple"
                                >{{
                                    formAngkatan.errors.tahun_angkatan
                                }}</Message
                            >
                        </div>

                        <div class="mt-2">
                            <FloatLabel variant="on">
                                <InputText
                                    id="jurusan"
                                    fluid
                                    disabled
                                    v-model="formAngkatan.jurusan"
                                    :invalid="!!formAngkatan.errors.jurusan"
                                />
                                <label for="jurusan">Jurusan</label>
                            </FloatLabel>
                            <Message
                                v-if="formAngkatan.errors.jurusan"
                                severity="error"
                                size="small"
                                variant="simple"
                                >{{ formAngkatan.errors.jurusan }}</Message
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
                        :value="dataAngkatan"
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
                                Data Angkatan tidak Ditemukan
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
                                        placeholder="Cari Data Angkatan"
                                    />
                                </IconField>
                            </div>
                        </template>

                        <Column field="index" header="No" />
                        <Column field="angkatan_id" header="Kode Angkatan" />
                        <Column
                            field="tahun_angkatan"
                            header="Tahun Angkatan"
                        />
                        <Column field="jurusan" header="Jurusan" />

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
