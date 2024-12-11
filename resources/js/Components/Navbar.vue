<script setup>
import { Button } from 'primevue';
import { router } from '@inertiajs/vue3';

// Fungsi logout
const Logout = () => {
    router.post(route("logout"), {
        onSuccess: () => {
            // Redirect setelah logout sukses
            router.visit(route("login"));
        },
        onError: () => {
            ShowToast(); // Menampilkan notifikasi jika terjadi kesalahan
        },
    });
};
</script>

<template>
    <div class="header flex justify-between items-center p-4 shadow-md">
        <h1 class="text-xl font-bold">My Dashboard</h1>
        <Button label="Logout" icon="pi pi-sign-out" class="p-button-danger" @click="Logout" />
    </div>
</template>

<style scoped>
.header {
    height: 60px;
    background-color: #007ad9;
    color: #ffffff;
}
</style>
