<script setup>
import { onMounted, ref, watch } from "vue";
import { Head, router, useForm } from "@inertiajs/vue3";
import {
    Toast,
    useToast,
    Card,
    Button,
    DataTable,
    Column,
    FloatLabel,
    Message,
    IconField,
    InputIcon,
    InputText,
    Select,
    ConfirmPopup,
} from "primevue";
import TemplateLayout from "@/Layouts/TemplateLayout.vue";
import { FilterMatchMode } from "@primevue/core/api";

// cek data dari controller
const props = defineProps({
    flash: Object,
    title: String,
    auth: Object,
    angkatan: Object,
    mahasiswa: Object,
    username: String,
    tahun: String,
});

// ceknotif
onMounted(() => {
    ShowToast();
    originalDataMahasiswa.value = props.mahasiswa.map((mahasiswa, index) => ({
        index: index + 1, // Tambahkan indeks awal dari 1
        ...mahasiswa,
    }));
    dataMahasiswa.value = [...originalDataMahasiswa.value]; // Set initial data
});

const originalDataMahasiswa = ref([]); // Store original data
const dataMahasiswa = ref([]);

// Filter untuk DataTable
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

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

const formPerhitungan = useForm({
    angkatan_id: "",
});

// Watch for changes in angkatan_id
watch(
    () => formPerhitungan.angkatan_id,
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

const hitung = () => {
    // Check role of the authenticated user
    if (props.auth.user.role === 1) {
        // Filter mahasiswa data based on angkatan_id
        const filteredData = dataMahasiswa.value.filter(
            (mahasiswa) => mahasiswa.angkatan_id === formPerhitungan.angkatan_id
        );

        // Show error toast if no data found
        if (filteredData.length === 0) {
            toast.add({
                severity: "error",
                summary: "Kesalahan",
                detail: "Tidak ada data mahasiswa untuk tahun angkatan yang dipilih.",
                life: 3000,
                group: "tc",
            });
            return;
        }

        // Submit data for role 1
        formPerhitungan.put(
            route("put.perhitungan", formPerhitungan.angkatan_id),
            {
                onSuccess: ShowToast,
                onError: handleErrorToast,
            }
        );
    } else {
        // Submit data for other roles
        formPerhitungan.put(
            route("create.perhitungan", props.auth.user.user_id),
            {
                onSuccess: ShowToast,
                onError: handleErrorToast,
            }
        );
    }
};

// Helper function to handle error toast
const handleErrorToast = () => {
    toast.add({
        severity: "error",
        summary: "Kesalahan",
        detail: "Terjadi kesalahan. Silakan coba lagi.",
        life: 3000,
        group: "tc",
    });
};
</script>

<template>
    <Head :title="props.title" />
    <Toast position="top-center" group="tc" />
    <ConfirmPopup></ConfirmPopup>
    <TemplateLayout
        :auth="props.auth"
        :title="props.title"
        :username="props.username"
    >
        <template #content>
            <div class="flex items-center justify-between mb-4">
                <span class="text-2xl font-bold">Data {{ props.title }}</span>
            </div>

            <template v-if="props.auth.user.role === 3">
                <div class="grid md:grid-cols-2 sm:grid-cols-1 gap-4 mb-4">
                    <!-- Card for Dosen -->
                    <Card>
                        <template #title>
                            <span class="text-xl font-bold">Dosen</span>
                        </template>
                        <template #content>
                            <p class="text-lg">{{ props.username }}</p>
                        </template>
                    </Card>
                    <!-- Card for Angkatan -->
                    <Card>
                        <template #title>
                            <span class="text-xl font-bold"
                                >Tahun Angkatan</span
                            >
                        </template>
                        <template #content>
                            <p class="text-lg">{{ props.tahun }}</p>
                        </template>
                    </Card>
                </div>
            </template>

            <!-- Tabel -->
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
                                    <div v-if="props.auth.user.role === 1">
                                        <FloatLabel variant="on">
                                            <Select
                                                v-model="
                                                    formPerhitungan.angkatan_id
                                                "
                                                inputId="angkatan"
                                                :options="props.angkatan"
                                                optionLabel="tahun_angkatan"
                                                optionValue="angkatan_id"
                                                fluid
                                                :invalid="
                                                    !!formPerhitungan.errors
                                                        .angkatan_id
                                                "
                                            />
                                            <label for="angkatan"
                                                >Tahun Angkatan</label
                                            >
                                        </FloatLabel>
                                        <Message
                                            v-if="
                                                formPerhitungan.errors
                                                    .angkatan_id
                                            "
                                            severity="error"
                                            size="small"
                                            variant="simple"
                                        >
                                            {{
                                                formPerhitungan.errors
                                                    .angkatan_id
                                            }}
                                        </Message>
                                    </div>

                                    <div class="flex items-center">
                                        <Button
                                            @click="hitung"
                                            unstyled
                                            class="px-4 py-2 bg-blue-500 hover:-translate-x-1 text-white rounded-md transition-all hover:bg-blue-600"
                                        >
                                            <template #default>
                                                <div
                                                    class="flex gap-2 items-center"
                                                >
                                                    <i
                                                        class="pi pi-calculator"
                                                    ></i>
                                                    <span>Hitung</span>
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
                        <Column frozen field="npm" header="NPM" />
                        <Column
                            :hidden="props.auth.user.role === 3"
                            field="angkatan.tahun_angkatan"
                            header="Tahun Angkatan"
                        />
                        <Column
                            :hidden="props.auth.user.role === 3"
                            field="dosen.nama_dosen"
                            header="Dosen Pembimbing"
                        />
                        <Column
                            frozen
                            field="nama_mahasiswa"
                            header="Nama Mahasiswa"
                        />
                        <Column
                            :hidden="props.auth.user.role === 3"
                            field="sks_total"
                            header="Total SKS"
                        />
                        <Column field="sks_tempuh" header="SKS Tempuh" />
                        <Column field="sks_sisa" header="SKS Sisa" />
                        <Column
                            :hidden="props.auth.user.role === 3"
                            field="studi_total"
                            header="Total Studi"
                        />
                        <Column
                            field="studi_tempuh"
                            header="Studi Tempuh"
                        />
                        <Column field="studi_sisa" header="Studi Sisa" />
                    </DataTable>
                </template>
            </Card>
            <!-- endTabel -->
        </template>
    </TemplateLayout>
</template>
