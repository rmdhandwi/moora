<script setup>
import TemplateLayout from "@/Layouts/TemplateLayout.vue";
import {
    DataTable,
    Column,
    Card,
    TabView,
    TabPanel,
    Tag,
    Button,
    InputIcon,
    InputText,
    IconField,
} from "primevue";
import { defineProps, ref, computed, reactive } from "vue";
import { FilterMatchMode } from "@primevue/core/api";

const props = defineProps({
    auth: Object,
    title: String,
    normalizationData: Array, // Data normalisasi dari backend
    optimizationData: Array, // Data optimasi bobot dan MOORA dari backend
});

const filters = reactive({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const dtAman = ref(null);
const dtHatiHati = ref(null);
const dtDoPindah = ref(null);

// Fungsi reset filter
const resetFilters = () => {
    filters.global.value = null;
};

const activeGolongan = ref(0);

// Filter data berdasarkan golongan
const filteredData = (golongan) =>
    props.optimizationData
        .filter((item) => item.golongan === golongan)
        .map((item, index) => ({
            ...item,
            index: index + 1, // Tambahkan indeks
        }));

// Fungsi untuk mengekspor DataTable ke CSV berdasarkan tab aktif
const exportCSV = () => {
    switch (activeGolongan.value) {
        case 0: // Tab Aman
            if (dtAman.value) {
                dtAman.value.exportCSV();
            }
            break;
        case 1: // Tab Hati-Hati
            if (dtHatiHati.value) {
                dtHatiHati.value.exportCSV();
            }
            break;
        case 2: // Tab DO/Pindah
            if (dtDoPindah.value) {
                dtDoPindah.value.exportCSV();
            }
            break;
    }
};

// Fungsi untuk memberi class CSS berdasarkan nilai golongan
const getGolonganClass = (golongan) => {
    switch (golongan) {
        case "Aman":
            return "success";
        case "Hati-Hati":
            return "warn";
        case "DO/Pindah":
            return "danger";
        default:
            return "info";
    }
};

// Fungsi untuk menentukan ikon berdasarkan golongan
const getGolonganIcon = (golongan) => {
    switch (golongan) {
        case "Aman":
            return "pi pi-check-circle";
        case "Hati-Hati":
            return "pi pi-exclamation-triangle";
        case "DO/Pindah":
            return "pi pi-times-circle";
        default:
            return "pi pi-info-circle";
    }
};

// // Ambil kriteria unik dari data normalisasi
// const uniqueCriteria = computed(() => {
//     if (!props.normalizationData.length) return [];
//     return Object.keys(props.normalizationData[0].nilai_normalisasi); // Mengambil kriteria dari data pertama
// });
</script>

<template>
    <TemplateLayout :auth="auth" :title="title">
        <template #content>
            <div class="p-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold">
                        Data Hasil Perhitungan
                    </h3>
                    <div class="flex justify-center items-center gap-2">
                        <Button
                            unstyled
                            @click="exportCSV"
                            class="px-4 py-2 bg-blue-500 hover:-translate-x-1 text-white rounded-md transition-all hover:bg-blue-600"
                        >
                            <div class="flex gap-2 items-center">
                                <i class="pi pi-external-link"></i>
                                <span>Export</span>
                            </div>
                        </Button>
                    </div>
                </div>

                <!-- Global Search -->
                <div class="mb-4 flex justify-between items-center">
                    <div class="flex justify-between items-center">
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
                        <Button
                            label="Reset"
                            icon="pi pi-refresh"
                            class="p-button-text p-button-sm ml-2"
                            severity="danger"
                            @click="resetFilters"
                        />
                    </div>
                    <Button
                        label="kembali"
                        icon="pi pi-arrow-left"
                        class="p-button-text p-button-sm ml-2"
                        severity="danger"
                        as="a"
                        :href="route('perhitunganPage')"
                    />
                </div>

                <TabView v-model:activeIndex="activeGolongan">
                    <!-- Tab Aman -->
                    <TabPanel header="Golongan: Aman">
                        <DataTable
                            ref="dtAman"
                            scrollable
                            responsiveLayout="scroll"
                            :value="filteredData('Aman')"
                            :filters="filters"
                            :paginator="true"
                            :rows="10"
                            :rowsPerPageOptions="[5, 10, 25, 50]"
                        >
                            <Column field="index" header="No" />
                            <Column
                                field="nama_mahasiswa"
                                header="Nama Mahasiswa"
                            />
                            <Column field="npm" header="NPM" />
                            <Column field="moora" header="Nilai Akhir" />
                            <Column header="Golongan" field="golongan">
                                <template #body="slotProps">
                                    <Tag
                                        :severity="
                                            getGolonganClass(
                                                slotProps.data.golongan
                                            )
                                        "
                                        :icon="
                                            getGolonganIcon(
                                                slotProps.data.golongan
                                            )
                                        "
                                    >
                                        {{ slotProps.data.golongan }}
                                    </Tag>
                                </template>
                            </Column>
                        </DataTable>
                    </TabPanel>

                    <!-- Tab Hati-Hati -->
                    <TabPanel header="Golongan: Hati-Hati">
                        <DataTable
                            ref="dtHatiHati"
                            scrollable
                            responsiveLayout="scroll"
                            :value="filteredData('Hati-Hati')"
                            :filters="filters"
                            :paginator="true"
                            :rows="10"
                            :rowsPerPageOptions="[5, 10, 25, 50]"
                        >
                            <Column field="index" header="No" />
                            <Column
                                field="nama_mahasiswa"
                                header="Nama Mahasiswa"
                            />
                            <Column field="npm" header="NPM" />
                            <Column field="moora" header="Nilai Akhir" />
                            <Column header="Golongan" field="golongan">
                                <template #body="slotProps">
                                    <Tag
                                        :severity="
                                            getGolonganClass(
                                                slotProps.data.golongan
                                            )
                                        "
                                        :icon="
                                            getGolonganIcon(
                                                slotProps.data.golongan
                                            )
                                        "
                                    >
                                        {{ slotProps.data.golongan }}
                                    </Tag>
                                </template>
                            </Column>
                        </DataTable>
                    </TabPanel>

                    <!-- Tab DO/Pindah -->
                    <TabPanel header="Golongan: DO/Pindah">
                        <DataTable
                            ref="dtDoPindah"
                            scrollable
                            responsiveLayout="scroll"
                            :value="filteredData('DO/Pindah')"
                            :filters="filters"
                            :paginator="true"
                            :rows="10"
                            :rowsPerPageOptions="[5, 10, 25, 50, 100]"
                        >
                            <Column field="index" header="No" />
                            <Column
                                field="nama_mahasiswa"
                                header="Nama Mahasiswa"
                            />
                            <Column field="npm" header="NPM" />
                            <Column field="moora" header="Nilai Akhir" />
                            <Column header="Golongan" field="golongan">
                                <template #body="slotProps">
                                    <Tag
                                        :severity="
                                            getGolonganClass(
                                                slotProps.data.golongan
                                            )
                                        "
                                        :icon="
                                            getGolonganIcon(
                                                slotProps.data.golongan
                                            )
                                        "
                                    >
                                        {{ slotProps.data.golongan }}
                                    </Tag>
                                </template>
                            </Column>
                        </DataTable>
                    </TabPanel>
                </TabView>
            </div>
        </template>
    </TemplateLayout>
</template>

<style scoped>
.p-4 {
    padding: 1rem;
}
.text-xl {
    font-size: 1.25rem;
}
.font-semibold {
    font-weight: 600;
}
.mb-2 {
    margin-bottom: 0.5rem;
}
.mt-4 {
    margin-top: 1rem;
}
</style>

<!-- <h3 class="text-xl font-semibold mb-4">Data Normalisasi</h3>
                <Card>
                    <template #content>
                        <DataTable
                            scrollable
                            responsiveLayout="scroll"
                            resizableColumns
                            columnResizeMode="fit"
                            :value="normalizationData"
                            :paginator="true"
                            :rows="10"
                            :rowsPerPageOptions="[5, 10, 25]"
                        >
                            <Column
                                field="mahasiswa_id"
                                header="ID Mahasiswa"
                            />
                            <Column
                                field="nama_mahasiswa"
                                header="Nama Mahasiswa"
                            />
                            <Column field="npm" header="NPM" />
                            <template
                                v-for="(kriteria, index) in uniqueCriteria"
                                :key="index"
                            >
                                <Column
                                    :header="kriteria"
                                    :field="
                                        (rowData) =>
                                            rowData.nilai_normalisasi[kriteria]
                                    "
                                />
                            </template>
                        </DataTable>
                    </template>
                </Card>

                <h3 class="text-xl font-semibold mb-4 mt-4">
                    Data Optimasi Bobot dan MOORA
                </h3>
                <Card>
                    <template #content>
                        <DataTable
                            scrollable
                            responsiveLayout="scroll"
                            resizableColumns
                            columnResizeMode="fit"
                            :value="optimizationData"
                            :paginator="true"
                            :rows="10"
                            :rowsPerPageOptions="[5, 10, 25]"
                        >
                            <Column
                                field="mahasiswa_id"
                                header="ID Mahasiswa"
                            />
                            <Column
                                field="nama_mahasiswa"
                                header="Nama Mahasiswa"
                            />
                            <Column field="npm" header="NPM" frozen />

                            <template
                                v-for="(kriteria, index) in uniqueCriteria"
                                :key="index"
                            >
                                <Column
                                    :header="kriteria"
                                    :field="
                                        (rowData) =>
                                            rowData.optimized_values[kriteria]
                                    "
                                />
                            </template>

                            <Column field="moora" header="Nilai MOORA" />
                        </DataTable>
                    </template>
                </Card> -->
