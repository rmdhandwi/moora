<script setup>
import { onMounted, ref } from "vue";
import { Head } from "@inertiajs/vue3";
import { Toast, useToast, Card } from "primevue";
import TemplateLayout from "@/Layouts/TemplateLayout.vue";

// cek data dari controller
const props = defineProps({
    flash: Object,
    title: String,
    auth: Object,
});

// ceknotif
onMounted(() => {
    ChackNotif();
});

const toast = useToast();

// notif
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

const ChackNotif = () => {
    if (props.flash.notif === "success" || props.flash.notif === "error") {
        setTimeout(() => ShowToast(), 500);
    }
};
</script>

<template>
    <Toast position="top-center" group="tc" />
    <Head :title="props.title" />
    <TemplateLayout :auth="props.auth" :title="props.title">
        <template #content>
            <div
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"
            ></div>
        </template>
    </TemplateLayout>
</template>

<style scoped></style>
