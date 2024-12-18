<script setup>
import TemplateLayout from "@/Layouts/TemplateLayout.vue";
import { DataTable, Column, Card } from "primevue";
import { defineProps, computed } from "vue";

const props = defineProps({
    auth: Object,
    title: String,
    normalizationData: Array, // Data normalisasi dari backend
    optimizationData: Array, // Data optimasi bobot dan MOORA dari backend
});

// Ambil kriteria unik dari data normalisasi
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
                    <Button
                        unstyled
                        @click="exportCSV"
                        class="px-4 py-2 bg-blue-500 hover:-translate-x-1 text-white rounded-md transition-all hover:bg-blue-600"
                    >
                        <template #default>
                            <div class="flex gap-2 items-center">
                                <i class="pi pi-external-link"></i>
                                <span>Export</span>
                            </div>
                        </template>
                    </Button>
                </div>
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
                            :rowsPerPageOptions="[5, 10, 25, 50]"
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

                            <Column field="moora" header="Nilai Akhir" />
                            <Column field="rank" header="Rank" />
                        </DataTable>
                    </template>
                </Card>
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

<!-- data jika diperlukan -->
<!-- <h3 class="text-xl font-semibold mb-2">Data Normalisasi</h3>
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
                            <Column field="mahasiswa_id" header="ID Mahasiswa" />
                            <Column field="nama_mahasiswa" header="Nama Mahasiswa" />
                            <Column field="npm" header="NPM" />
                            <template
                                v-for="(kriteria, index) in uniqueCriteria"
                                :key="index"
                            >
                                <Column
                                    :header="kriteria"
                                    :field="rowData => rowData.nilai_normalisasi[kriteria]"
                                />
                            </template>
                        </DataTable>
                    </template>
                </Card>

                <h3 class="text-xl font-semibold mb-2 mt-4">Data Optimasi Bobot dan MOORA</h3>
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
                            <Column field="mahasiswa_id" header="ID Mahasiswa" />
                            <Column field="nama_mahasiswa" header="Nama Mahasiswa" />
                            <Column field="npm" header="NPM" frozen />

                            <template
                                v-for="(kriteria, index) in uniqueCriteria"
                                :key="index"
                            >
                                <Column
                                    :header="kriteria"
                                    :field="rowData => rowData.optimized_values[kriteria]"
                                />
                            </template>

                            <Column field="moora" header="Nilai MOORA" />
                        </DataTable>
                    </template>
                </Card> -->
