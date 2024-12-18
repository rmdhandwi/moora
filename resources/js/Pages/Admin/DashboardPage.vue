<script setup>
import { onMounted, ref } from "vue";
import { Head } from "@inertiajs/vue3";
import { Toast, useToast, Card, Button } from "primevue";
import TemplateLayout from "@/Layouts/TemplateLayout.vue";

// Props from the controller
const props = defineProps({
    flash: Object,
    title: String,
    auth: Object,
    dosen: Number,
    angkatan: Number,
    kriteria: Number,
    user: Number,
    mahasiswa: Number,
    angkatanData: Array,
});

const toast = useToast();
const role = props.auth.user.role;

// Prepare data for the chart
const chartData = ref({
    labels: props.angkatanData.map((data) => data.tahun),
    datasets: [
        {
            label: "Jumlah Mahasiswa",
            data: props.angkatanData.map((data) => data.total),
            backgroundColor: "rgba(66, 165, 245, 0.4)", // Warna batang dengan opasitas
            borderColor: "rgb(66, 165, 245)", // Outline warna batang
            borderWidth: 1,
            borderRadius: 4, // Sudut lembut pada batang
            animation: {
                duration: 1500, // Animasi pada dataset ini
                easing: "easeOutBounce", // Gaya animasi khusus untuk dataset ini
            },
        },
    ],
});

const chartOptions = ref({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: "top", // Posisi legenda
            labels: {
                color: "#4B5563", // Warna teks legenda
            },
        },
        tooltip: {
            backgroundColor: "rgba(0,0,0,0.8)", // Latar tooltip
            titleColor: "#fff", // Warna judul tooltip
            bodyColor: "#fff", // Warna isi tooltip
        },
    },
    scales: {
        x: {
            ticks: {
                color: "#4B5563", // Warna teks sumbu x
            },
            grid: {
                color: "#E5E7EB", // Warna grid sumbu x
            },
        },
        y: {
            ticks: {
                color: "#4B5563", // Warna teks sumbu y
            },
            grid: {
                color: "#E5E7EB", // Warna grid sumbu y
            },
        },
    },
});

// Toast notification logic
const ShowToast = () => {
    if (props.flash.notif && props.flash.message) {
        toast.add({
            severity: props.flash.notif || "info",
            summary:
                props.flash.notif.charAt(0).toUpperCase() +
                props.flash.notif.slice(1),
            detail: props.flash.message,
            life: 4000,
            group: "tc",
        });
    }
};

onMounted(() => {
    if (props.flash.notif === "success" || props.flash.notif === "error") {
        setTimeout(() => ShowToast(), 500);
    }
});
</script>

<template>
    <Toast position="top-center" group="tc" />
    <Head :title="props.title" />
    <TemplateLayout :auth="props.auth" :title="props.title">
        <template #content>
            <!-- Cards -->
            <div
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6"
            >
                <!-- Total Dosen -->
                <Button
                    unstyled
                    v-if="role === 1"
                    :href="route('dosenPage')"
                    as="a"
                    class="flex items-center justify-between p-4 no-border w-full rounded-md hover:-translate-y-1 transition-all shadow-lg hover:shadow-xl bg-white"
                >
                    <div class="text-left">
                        <h2 class="text-lg font-semibold text-gray-600">
                            Total Dosen
                        </h2>
                        <p class="text-4xl font-bold text-blue-600">
                            {{ props.dosen }}
                        </p>
                    </div>
                    <i class="pi pi-users text-blue-500 text-5xl"></i>
                </Button>

                <!-- Total Kriteria -->
                <Button
                    unstyled
                    v-if="role === 1"
                    :href="route('kriteriaPage')"
                    as="a"
                    class="flex items-center justify-between p-4 no-border w-full rounded-md hover:-translate-y-1 transition-all shadow-lg hover:shadow-xl bg-white"
                >
                    <div class="text-left">
                        <h2 class="text-lg font-semibold text-gray-600">
                            Total Kriteria
                        </h2>
                        <p class="text-4xl font-bold text-green-600">
                            {{ props.kriteria }}
                        </p>
                    </div>
                    <i class="pi pi-tags text-green-500 text-5xl"></i>
                </Button>

                <!-- Total User -->
                <Button
                    unstyled
                    v-if="role === 1"
                    :href="route('usersPage')"
                    as="a"
                    class="flex items-center justify-between p-4 no-border w-full rounded-md hover:-translate-y-1 transition-all shadow-lg hover:shadow-xl bg-white"
                >
                    <div class="text-left">
                        <h2 class="text-lg font-semibold text-gray-600">
                            Total User
                        </h2>
                        <p class="text-4xl font-bold text-orange-600">
                            {{ props.user }}
                        </p>
                    </div>
                    <i class="pi pi-user text-orange-500 text-5xl"></i>
                </Button>

                <!-- Total Angkatan -->
                <Button
                    unstyled
                    v-if="role === 1"
                    :href="route('angkatanPage')"
                    as="a"
                    class="flex items-center justify-between p-4 no-border w-full rounded-md hover:-translate-y-1 transition-all shadow-lg hover:shadow-xl bg-white"
                >
                    <div class="text-left">
                        <h2 class="text-lg font-semibold text-gray-600">
                            Total Angkatan
                        </h2>
                        <p class="text-4xl font-bold text-pink-600">
                            {{ props.angkatan }}
                        </p>
                    </div>
                    <i class="pi pi-chart-line text-pink-500 text-5xl"></i>
                </Button>

                <!-- Total Mahasiswa -->
                <Button
                    as="a"
                    unstyled
                    v-if="role === 1"
                    :href="route('mahasiswaPage')"
                    class="flex items-center justify-between p-4 no-border w-full rounded-md hover:-translate-y-1 transition-all shadow-lg hover:shadow-xl bg-white"
                >
                    <div class="text-left">
                        <h2 class="text-lg font-semibold text-gray-600">
                            Total Mahasiswa
                        </h2>
                        <p class="text-4xl font-bold text-purple-600">
                            {{ props.mahasiswa }}
                        </p>
                    </div>
                    <i
                        class="pi pi-graduation-cap text-purple-500 text-5xl"
                    ></i>
                </Button>
            </div>

            <!-- Chart -->
            <Card class="p-6">
                <template #content>
                    <h2 class="text-xl font-semibold mb-4">
                        Jumlah Mahasiswa Berdasarkan Angkatan
                    </h2>

                    <div class="w-full chart-container">
                        <Chart
                            type="bar"
                            :data="chartData"
                            :options="chartOptions"
                        />
                    </div>
                </template>
            </Card>
        </template>
    </TemplateLayout>
</template>

<style scoped>
.card {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}
.chart-container {
    height: 100%;
    width: 100%;
}
</style>
