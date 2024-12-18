<script setup>
import { onMounted, ref, watch, computed } from "vue";
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
    InputNumber,
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
    dosen: Object,
    angkatan: Object,
    mahasiswa: Object,
});

// ceknotif
onMounted(() => {
    ShowToast();
    originalDataMahasiswa.value = props.mahasiswa.map((mahasiswa, index) => ({
        index: index + 1,
        ...mahasiswa,
    }));
    dataMahasiswa.value = [...originalDataMahasiswa.value]; // Set initial data
});

const dt = ref([]);
const originalDataMahasiswa = ref([]); // Store original data
const dataMahasiswa = ref([]);

const formSelect = useForm({
    angkatan_id: "",
});

// Watch for changes in angkatan_id
watch(
    () => formSelect.angkatan_id,
    (newValue) => {
        if (newValue) {
            // Filter data berdasarkan angkatan_id
            const filteredData = originalDataMahasiswa.value.filter(
                (mahasiswa) => mahasiswa.angkatan_id === newValue
            );
            
            // Tambahkan index ulang dari 1
            dataMahasiswa.value = filteredData.map((mahasiswa, index) => ({
                ...mahasiswa,
                index: index + 1,
            }));
        } else {
            // Reset ke data asli dengan indeks awal
            dataMahasiswa.value = originalDataMahasiswa.value.map(
                (mahasiswa, index) => ({
                    ...mahasiswa,
                    index: index + 1,
                })
            );
        }
    }
);

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
const sks_total = ref(null);
const studi_total = ref(null);

const formMahasiswa = useForm({
    dosen_id: "",
    angkatan_id: "",
    mahasiswa_id: "",
    nama_mahasiswa: "",
    npm: "",
    sks_total: 144,
    sks_tempuh: null,
    sks_sisa: null,
    studi_total: 14,
    studi_tempuh: null,
    studi_sisa: null,
});

// Computed properties for sks_sisa and studi_sisa with minimum value of 0
const sks_sisa = computed(() => {
    const remaining = sks_total.value - (formMahasiswa.sks_tempuh || 0);
    return remaining < 0 ? 0 : remaining;
});

const studi_sisa = computed(() => {
    const remaining = studi_total.value - (formMahasiswa.studi_tempuh || 0);
    return remaining < 0 ? 0 : remaining;
});

const addDialog = () => {
    formMahasiswa.reset();
    formMahasiswa.clearErrors();
    dataDialog.value = "add";
    showDialog.value = true;
    sks_total.value = formMahasiswa.sks_total;
    studi_total.value = formMahasiswa.studi_total;
    formMahasiswa.sks_sisa = sks_sisa.value;
    formMahasiswa.studi_sisa = studi_sisa.value;
};

watch(
    () => formMahasiswa.sks_tempuh,
    (newValue) => {
        formMahasiswa.sks_sisa = sks_sisa.value;
    }
);

watch(
    () => formMahasiswa.studi_tempuh,
    (newValue) => {
        formMahasiswa.studi_sisa = studi_sisa.value;
    }
);

const refresh = () => {
    return new Promise((resolve) => {
        router.visit(route("mahasiswaPage"), {
            onSuccess: () => {
                resolve();
            },
        });
    });
};

const creteMahasiswa = () => {
    formMahasiswa.post(route("create.mahasiswa"), {
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
        message: `Anda ingin mengedit user : ${data.nama_dosen}`,
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
            formMahasiswa.reset();
            formMahasiswa.clearErrors();
            dataDialog.value = "edit";
            formMahasiswa.mahasiswa_id = data.mahasiswa_id;
            formMahasiswa.nama_mahasiswa = data.nama_mahasiswa;
            formMahasiswa.dosen_id = data.dosen_id;
            formMahasiswa.angkatan_id = data.angkatan_id;
            formMahasiswa.npm = data.npm;
            sks_total.value = formMahasiswa.sks_total;
            formMahasiswa.sks_tempuh = data.sks_tempuh;
            formMahasiswa.sks_sisa = data.sks_sisa;
            studi_total.value = formMahasiswa.studi_total;
            formMahasiswa.studi_tempuh = data.studi_tempuh;
            formMahasiswa.studi_sisa = data.studi_sisa;
            showDialog.value = true;
        },
        reject: () => {
            toast.add({
                severity: "error",
                summary: "Batal",
                detail: "Batal untuk edit data",
                life: 3000,
                group: "tc",
            });
        },
    });
};

const updateMahasiswa = () => {
    formMahasiswa.put(route("update.mahasiswa", formMahasiswa.mahasiswa_id), {
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
        message: `Anda ingin menghapus user : ${data.nama_mahasiswa}`,
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
            router.delete(route("destroy.mahasiswa", data.mahasiswa_id), {
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
        },
        reject: () => {
            toast.add({
                severity: "error",
                summary: "Batal",
                detail: "Batal untuk edit data",
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
                        ? 'Tambah Data Mahasiswa'
                        : 'Edit Data Mahasiswa'
                "
                :style="{ width: '50vw' }"
                :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            >
                <form
                    @submit.prevent="
                        dataDialog === 'add'
                            ? creteMahasiswa()
                            : updateMahasiswa()
                    "
                >
                    <div class="grid md:grid-cols-2 sm:grid-cols-1 gap-3 mt-2">
                        <div>
                            <FloatLabel variant="on">
                                <Select
                                    v-model="formMahasiswa.angkatan_id"
                                    inputId="angkatan"
                                    :options="props.angkatan"
                                    optionLabel="tahun_angkatan"
                                    optionValue="angkatan_id"
                                    fluid
                                    :invalid="
                                        !!formMahasiswa.errors.angkatan_id
                                    "
                                />
                                <label for="angkatan">Tahun Angkatan</label>
                            </FloatLabel>
                            <Message
                                v-if="formMahasiswa.errors.angkatan_id"
                                severity="error"
                                size="small"
                                variant="simple"
                                >{{ formMahasiswa.errors.angkatan_id }}</Message
                            >
                        </div>

                        <div>
                            <FloatLabel variant="on">
                                <Select
                                    v-model="formMahasiswa.dosen_id"
                                    inputId="dosen"
                                    :options="props.dosen"
                                    optionLabel="nama_dosen"
                                    optionValue="dosen_id"
                                    fluid
                                    :invalid="!!formMahasiswa.errors.dosen_id"
                                />
                                <label for="dosen">Dosen Pembimbing</label>
                            </FloatLabel>
                            <Message
                                v-if="formMahasiswa.errors.dosen_id"
                                severity="error"
                                size="small"
                                variant="simple"
                                >{{ formMahasiswa.errors.dosen_id }}</Message
                            >
                        </div>

                        <div>
                            <FloatLabel variant="on">
                                <InputText
                                    id="nama_mahasiswa"
                                    fluid
                                    v-model="formMahasiswa.nama_mahasiswa"
                                    :invalid="
                                        !!formMahasiswa.errors.nama_mahasiswa
                                    "
                                />
                                <label for="nama_mahasiswa"
                                    >Nama Mahasiswa</label
                                >
                            </FloatLabel>
                            <Message
                                v-if="!!formMahasiswa.errors.nama_mahasiswa"
                                severity="error"
                                size="small"
                                variant="simple"
                                >{{
                                    formMahasiswa.errors.nama_mahasiswa
                                }}</Message
                            >
                        </div>

                        <div>
                            <FloatLabel variant="on">
                                <InputText
                                    inputId="npm"
                                    fluid
                                    v-model="formMahasiswa.npm"
                                    :invalid="!!formMahasiswa.errors.npm"
                                />
                                <label for="npm">NPM</label>
                            </FloatLabel>
                            <Message
                                v-if="!!formMahasiswa.errors.npm"
                                severity="error"
                                size="small"
                                variant="simple"
                                >{{ formMahasiswa.errors.npm }}</Message
                            >
                        </div>

                        <div>
                            <FloatLabel variant="on">
                                <InputNumber
                                    inputId="sks_tempuh"
                                    mode="decimal"
                                    showButtons
                                    :step="1"
                                    :min="0"
                                    :max="9999"
                                    fluid
                                    v-model="formMahasiswa.sks_tempuh"
                                    :invalid="!!formMahasiswa.errors.sks_tempuh"
                                />
                                <label for="sks_tempuh">SKS Tempuh</label>
                            </FloatLabel>
                            <Message
                                v-if="!!formMahasiswa.errors.sks_tempuh"
                                severity="error"
                                size="small"
                                variant="simple"
                                >{{ formMahasiswa.errors.sks_tempuh }}</Message
                            >
                        </div>

                        <div>
                            <FloatLabel variant="on">
                                <InputNumber
                                    inputId="studi_tempuh"
                                    mode="decimal"
                                    showButtons
                                    :step="1"
                                    :min="0"
                                    :max="9999"
                                    fluid
                                    v-model="formMahasiswa.studi_tempuh"
                                    :invalid="
                                        !!formMahasiswa.errors.studi_tempuh
                                    "
                                />
                                <label for="studi_tempuh">Studi Tempuh</label>
                            </FloatLabel>
                            <Message
                                v-if="!!formMahasiswa.errors.studi_tempuh"
                                severity="error"
                                size="small"
                                variant="simple"
                                >{{
                                    formMahasiswa.errors.studi_tempuh
                                }}</Message
                            >
                        </div>

                        <div>
                            <FloatLabel variant="on">
                                <InputNumber
                                    inputId="sks_total"
                                    mode="decimal"
                                    :min="144"
                                    :max="144"
                                    disabled
                                    fluid
                                    v-model="sks_total"
                                    :invalid="!!formMahasiswa.errors.sks_total"
                                />
                                <label for="sks_total">SKS Total</label>
                            </FloatLabel>
                            <Message
                                v-if="!!formMahasiswa.errors.sks_total"
                                severity="error"
                                size="small"
                                variant="simple"
                                >{{ formMahasiswa.errors.sks_total }}</Message
                            >
                        </div>

                        <div>
                            <FloatLabel variant="on">
                                <InputNumber
                                    inputId="studi_total"
                                    mode="decimal"
                                    :min="14"
                                    :max="14"
                                    disabled
                                    fluid
                                    v-model="studi_total"
                                    :invalid="
                                        !!formMahasiswa.errors.studi_total
                                    "
                                />
                                <label for="studi_total">Studi Total</label>
                            </FloatLabel>
                            <Message
                                v-if="!!formMahasiswa.errors.studi_total"
                                severity="error"
                                size="small"
                                variant="simple"
                                >{{ formMahasiswa.errors.studi_total }}</Message
                            >
                        </div>

                        <div>
                            <FloatLabel variant="on">
                                <InputNumber
                                    inputId="sks_sisa"
                                    mode="decimal"
                                    :min="0"
                                    :max="144"
                                    disabled
                                    fluid
                                    v-model="sks_sisa"
                                    :invalid="!!formMahasiswa.errors.sks_sisa"
                                />
                                <label for="sks_sisa">SKS Sisa</label>
                            </FloatLabel>
                            <Message
                                v-if="!!formMahasiswa.errors.sks_sisa"
                                severity="error"
                                size="small"
                                variant="simple"
                                >{{ formMahasiswa.errors.sks_sisa }}</Message
                            >
                        </div>

                        <div>
                            <FloatLabel variant="on">
                                <InputNumber
                                    inputId="sks_sisa"
                                    mode="decimal"
                                    :min="0"
                                    :max="14"
                                    disabled
                                    fluid
                                    v-model="studi_sisa"
                                    :invalid="!!formMahasiswa.errors.studi_sisa"
                                />
                                <label for="studi_sisa">Studi Sisa</label>
                            </FloatLabel>
                            <Message
                                v-if="!!formMahasiswa.errors.studi_sisa"
                                severity="error"
                                size="small"
                                variant="simple"
                                >{{ formMahasiswa.errors.studi_sisa }}</Message
                            >
                        </div>
                    </div>
                    <div class="flex justify-end items-center gap-2 mt-6">
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
                </form>
            </Dialog>
            <!-- endForm -->

            <!-- Tabel User -->
            <Card>
                <template #content>
                    <DataTable
                        ref="dt"
                        :value="dataMahasiswa"
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
                                Data Mahasiswa tidak Ditemukan
                            </div>
                        </template>

                        <!-- Header tabel -->
                        <template #header>
                            <div
                                class="flex flex-col md:flex-row justify-between items-center"
                            >
                                <div
                                    class="grid md:grid-cols-2 sm:grid-cols-1 gap-4 w-full md:w-[50%]"
                                >
                                    <div>
                                        <FloatLabel variant="on">
                                            <Select
                                                v-model="formSelect.angkatan_id"
                                                inputId="angkatan"
                                                :options="props.angkatan"
                                                optionLabel="tahun_angkatan"
                                                optionValue="angkatan_id"
                                                fluid
                                                :invalid="
                                                    !!formSelect.errors
                                                        .angkatan_id
                                                "
                                            />
                                            <label for="angkatan"
                                                >Tahun Angkatan</label
                                            >
                                        </FloatLabel>
                                        <Message
                                            v-if="formSelect.errors.angkatan_id"
                                            severity="error"
                                            size="small"
                                            variant="simple"
                                        >
                                            {{ formSelect.errors.angkatan_id }}
                                        </Message>
                                    </div>
                                    <div class="flex items-center">
                                        <Button
                                            unstyled
                                            @click="exportCSV"
                                            class="px-4 py-2 bg-blue-500 hover:-translate-x-1 text-white rounded-md transition-all hover:bg-blue-600"
                                        >
                                            <template #default>
                                                <div
                                                    class="flex gap-2 items-center"
                                                >
                                                    <i
                                                        class="pi pi-external-link"
                                                    ></i>
                                                    <span>Export</span>
                                                </div>
                                            </template>
                                        </Button>
                                    </div>
                                </div>
                                <div class="mt-4 md:mt-0">
                                    <IconField>
                                        <InputIcon>
                                            <i class="pi pi-search me-4" />
                                        </InputIcon>
                                        <InputText
                                            v-model="filters.global.value"
                                            placeholder="Cari Data Mahasiswa"
                                            class="w-full md:w-auto"
                                        />
                                    </IconField>
                                </div>
                            </div>
                        </template>

                        <Column field="index" header="No" />
                        <Column field="mahasiswa_id" header="Kode Mahasiswa" />
                        <Column frozen field="npm" header="NPM" />
                        <Column
                            field="angkatan.tahun_angkatan"
                            header="Tahun Angkatan"
                        />
                        <Column
                            field="dosen.nama_dosen"
                            header="Dosen Pembimbing"
                        />
                        <Column
                            field="nama_mahasiswa"
                            header="Nama Mahasiswa"
                        />
                        <Column field="sks_total" header="Total SKS" />
                        <Column field="sks_tempuh" header="SKS Tempuh" />
                        <Column field="sks_sisa" header="SKS Sisa" />
                        <Column field="studi_total" header="Total Studi" />
                        <Column field="studi_tempuh" header="Studi Tempuh" />
                        <Column field="studi_sisa" header="Studi Sisa" />

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
