<script setup>
import TemplateLayout from "@/Layouts/TemplateLayout.vue";
import { Head } from "@inertiajs/vue3";
import {
    DataTable,
    Column,
    TabView,
    TabPanel,
    Tag,
    Button,
    InputIcon,
    InputText,
    IconField,
} from "primevue";
import { defineProps, ref, reactive } from "vue";
import { FilterMatchMode } from "@primevue/core/api";

const props = defineProps({
    auth: Object,
    title: String,
    normalizationData: Array,
    optimizationData: Array,
    username: String,
});

const filters = reactive({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const dtAman = ref(null);
const dtHatiHati = ref(null);
const dtDoPindah = ref(null);
const activeGolongan = ref(0);

const resetFilters = () => {
    filters.global.value = null;
};

const searchAllTabs = () => {
    const globalFilter = filters.global.value?.toLowerCase() || "";
    if (!globalFilter) return;

    const firstMatch = props.optimizationData.find((item) => {
        return Object.values(item)
            .map((value) => value?.toString().toLowerCase())
            .some((val) => val.includes(globalFilter));
    });

    if (firstMatch) {
        switch (firstMatch.golongan) {
            case "Aman":
                activeGolongan.value = 0;
                break;
            case "Hati-Hati":
                activeGolongan.value = 1;
                break;
            case "DO/Pindah":
                activeGolongan.value = 2;
                break;
        }
    }
};

const filteredData = (golongan) => {
    const globalFilter = filters.global.value?.toLowerCase() || "";
    return props.optimizationData
        .filter((item) => {
            return (
                item.golongan === golongan &&
                Object.values(item)
                    .map((value) => value?.toString().toLowerCase())
                    .some((val) => val.includes(globalFilter))
            );
        })
        .map((item, index) => ({
            ...item,
            index: index + 1,
        }));
};

const exportCSV = () => {
    switch (activeGolongan.value) {
        case 0:
            dtAman.value?.exportCSV();
            break;
        case 1:
            dtHatiHati.value?.exportCSV();
            break;
        case 2:
            dtDoPindah.value?.exportCSV();
            break;
    }
};

const getGolonganClass = (golongan) => {
    return (
        {
            Aman: "success",
            "Hati-Hati": "warn",
            "DO/Pindah": "danger",
        }[golongan] || "info"
    );
};

const getGolonganIcon = (golongan) => {
    return (
        {
            Aman: "pi pi-check-circle",
            "Hati-Hati": "pi pi-exclamation-triangle",
            "DO/Pindah": "pi pi-times-circle",
        }[golongan] || "pi pi-info-circle"
    );
};

const expandedRows = ref([]);

const toggleRowExpansion = (data) => {
    const index = expandedRows.value.findIndex((row) => row.npm === data.npm);
    if (index === -1) {
        expandedRows.value.push(data);
    } else {
        expandedRows.value.splice(index, 1);
    }
};
</script>

<template>
    <Head :title="props.title" />
    <TemplateLayout :auth="auth" :title="title" :username="props.username">
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
                                placeholder="Cari Data"
                                class="w-full md:w-[500px]"
                                @input="searchAllTabs"
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
                            <!-- Pesan jika data kosong -->
                            <template #empty>
                                <div class="text-center">
                                    Data Mahasiswa tidak Ditemukan
                                </div>
                            </template>

                            <Column field="index" header="No" />
                            <Column
                                field="nama_mahasiswa"
                                header="Nama Mahasiswa"
                            />
                            <Column field="npm" header="NPM" />
                            <Column field="moora" header="Nilai Akhir" />
                            <Column header="Golongan" field="golongan">
                                <template #body="slotProps">
                                    <div
                                        @click="
                                            toggleRowExpansion(slotProps.data)
                                        "
                                        class="cursor-pointer"
                                    >
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
                                        <div
                                            v-if="
                                                expandedRows.includes(
                                                    slotProps.data
                                                )
                                            "
                                            class="p-2 bg-gray-100 rounded mt-2"
                                        >
                                            <p>
                                                <strong>SKS Tempuh:</strong>
                                                {{ slotProps.data.sks_tempuh }}
                                            </p>
                                            <p>
                                                <strong>SKS Sisa:</strong>
                                                {{ slotProps.data.sks_sisa }}
                                            </p>
                                            <p>
                                                <strong>Studi Tempuh:</strong>
                                                {{
                                                    slotProps.data.studi_tempuh
                                                }}
                                            </p>
                                            <p>
                                                <strong>Studi Sisa:</strong>
                                                {{ slotProps.data.studi_sisa }}
                                            </p>
                                            
                                        </div>
                                    </div>
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
                            <!-- Pesan jika data kosong -->
                            <template #empty>
                                <div class="text-center">
                                    Data Mahasiswa tidak Ditemukan
                                </div>
                            </template>

                            <Column field="index" header="No" />
                            <Column
                                field="nama_mahasiswa"
                                header="Nama Mahasiswa"
                            />
                            <Column field="npm" header="NPM" />
                            <Column field="moora" header="Nilai Akhir" />
                            <Column header="Golongan" field="golongan">
                                <template #body="slotProps">
                                    <div
                                        @click="
                                            toggleRowExpansion(slotProps.data)
                                        "
                                        class="cursor-pointer"
                                    >
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
                                        <div
                                            v-if="
                                                expandedRows.includes(
                                                    slotProps.data
                                                )
                                            "
                                            class="p-2 bg-gray-100 rounded mt-2"
                                        >
                                            <p>
                                                <strong>SKS Tempuh:</strong>
                                                {{ slotProps.data.sks_tempuh }}
                                            </p>
                                            <p>
                                                <strong>SKS Sisa:</strong>
                                                {{ slotProps.data.sks_sisa }}
                                            </p>
                                            <p>
                                                <strong>Studi Tempuh:</strong>
                                                {{
                                                    slotProps.data.studi_tempuh
                                                }}
                                            </p>
                                            <p>
                                                <strong>Studi Sisa:</strong>
                                                {{ slotProps.data.studi_sisa }}
                                            </p>
                                            
                                        </div>
                                    </div>
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
                            <!-- Pesan jika data kosong -->
                            <template #empty>
                                <div class="text-center">
                                    Data Mahasiswa tidak Ditemukan
                                </div>
                            </template>

                            <Column field="index" header="No" />
                            <Column
                                field="nama_mahasiswa"
                                header="Nama Mahasiswa"
                            />
                            <Column field="npm" header="NPM" />
                            <Column field="moora" header="Nilai Akhir" />
                            <Column header="Golongan" field="golongan">
                                <template #body="slotProps">
                                    <div
                                        @click="
                                            toggleRowExpansion(slotProps.data)
                                        "
                                        class="cursor-pointer"
                                    >
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
                                        <div
                                            v-if="
                                                expandedRows.includes(
                                                    slotProps.data
                                                )
                                            "
                                            class="p-2 bg-gray-100 rounded mt-2"
                                        >
                                            <p>
                                                <strong>SKS Tempuh:</strong>
                                                {{ slotProps.data.sks_tempuh }}
                                            </p>
                                            <p>
                                                <strong>SKS Sisa:</strong>
                                                {{ slotProps.data.sks_sisa }}
                                            </p>
                                            <p>
                                                <strong>Studi Tempuh:</strong>
                                                {{
                                                    slotProps.data.studi_tempuh
                                                }}
                                            </p>
                                            <p>
                                                <strong>Studi Sisa:</strong>
                                                {{ slotProps.data.studi_sisa }}
                                            </p>
                                            
                                        </div>
                                    </div>
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
