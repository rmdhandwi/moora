<script setup>
import { Toast, useToast, Card } from "primevue";
import { onMounted } from "vue";
import { Head, usePage } from "@inertiajs/vue3";
import TemplateLayout from "@/Layouts/TemplateLayout.vue";

// Mengambil data dari Laravel menggunakan Inertia.js
const page = usePage();
const auth = page.props.auth; 

// cek data dari controller
const pageChack = defineProps({
    flash: Object,
});

// ceknotif
onMounted(() => {
    ChackNotif();
});

const toast = useToast();

// notif
const ShowToast = () => {
    if (pageChack.flash.notif && pageChack.flash.message) {
        toast.add({
            severity: pageChack.flash.notif || "info", // Gunakan 'info' sebagai default jika tidak ada notif
            summary:
                pageChack.flash.notif.charAt(0).toUpperCase() +
                pageChack.flash.notif.slice(1), // Kapitalisasi pertama
            detail: pageChack.flash.message,
            life: 4000,
            group: "tc",
        });
    }
};

const ChackNotif = () => {
    if (pageChack.flash.show) {
        setTimeout(() => ShowToast(), 500);
    }
};
</script>

<template>
    <Toast position="top-center" group="tc" />
    <Head title="Dashboard" />
    <TemplateLayout :auth="auth">
        <template #content>
            <h1>Content</h1>
        </template>
    </TemplateLayout>
</template>

<style scoped>

</style>
