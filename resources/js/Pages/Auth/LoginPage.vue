<script setup>
import {
    Card,
    InputText,
    Password,
    FloatLabel,
    Button,
    Message,
    Toast,
    useToast,
} from "primevue";
import { useForm, Head, router } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";

// Menerima data flash dari server untuk notifikasi
const pageChack = defineProps({
    flash: Object,
});

// Fungsi untuk menampilkan notifikasi jika ada pesan flash
const toast = useToast();
const ShowToast = () => {
    if (pageChack.flash.notif && pageChack.flash.message) {
        toast.add({
            severity: pageChack.flash.notif || "info", // Gunakan 'info' sebagai default jika tidak ada notif
            summary:
                pageChack.flash.notif.charAt(0).toUpperCase() +
                pageChack.flash.notif.slice(1), // Kapitalisasi huruf pertama
            detail: pageChack.flash.message,
            life: 4000,
            group: "tc",
        });
    }
};

// Cek notifikasi saat komponen di-mount
onMounted(() => {
    if (pageChack.flash.show) {
        setTimeout(() => ShowToast(), 500);
    }
});

// Menyimpan status loading saat merefresh halaman
const refrashLoading = ref(false);

// Fungsi untuk merefresh halaman
const refresh = () => {
    refrashLoading.value = true;
    router.visit(route("login"), {
        preserveScroll: true,
        onSuccess: () => {
            refrashLoading.value = false;
            formLogin.reset();
        },
    });
};

// Data formulir login
const formLogin = useForm({
    nama_pengguna: "",
    kata_sandi: "",
});

// Fungsi submit untuk login
const LoginSubmit = () => {
    formLogin.post(route("LoginSubmit"), {
        onSuccess: () => {
            // Cek jika login berhasil, arahkan ke dashboard
            if (pageChack.flash.notif === "success") {
                router.visit(route("dashboard"));
            } else {
                ShowToast();
            }
        },
        onError: () => {
            // Jika terjadi kesalahan, tampilkan notifikasi error
            ShowToast();
        },
    });
};
</script>

<template>
    <Toast position="top-center" group="tc" />
    <Head title="Selamat Datang" />
    <div class="flex flex-col justify-center min-h-screen items-center">
        <div class="flex mb-4">
            <div class="logo">
                <img src="image/logo/favicon.ico" alt="logo.png" />
            </div>
            <div class="judul ms-3 font-bold text-2xl">
                <h1>Judul Aplikasi</h1>
            </div>
        </div>
        <Card class="w-full md:w-1/3 shadow-lg p-6">
            <template #content>
                <form @submit.prevent="LoginSubmit">
                    <div class="flex flex-col gap-2 mb-6">
                        <FloatLabel variant="on">
                            <InputText
                                id="nama_pengguna"
                                v-model="formLogin.nama_pengguna"
                                class="w-full"
                                :invalid="!!formLogin.errors.nama_pengguna"
                                placeholder="Nama Pengguna"
                            />
                            <label for="nama_pengguna">Nama Pengguna</label>
                        </FloatLabel>
                        <Message
                            v-if="formLogin.errors.nama_pengguna"
                            severity="error"
                            size="small"
                            variant="simple"
                            >{{ formLogin.errors.nama_pengguna }}</Message
                        >
                    </div>
                    <div class="flex flex-col gap-2 mb-6">
                        <FloatLabel variant="on">
                            <Password
                                fluid
                                toggleMask
                                id="kata_sandi"
                                v-model="formLogin.kata_sandi"
                                :invalid="!!formLogin.errors.kata_sandi"
                                placeholder="Kata Sandi"
                            />
                            <label for="kata_sandi">Kata Sandi</label>
                        </FloatLabel>
                        <Message
                            v-if="formLogin.errors.kata_sandi"
                            severity="error"
                            size="small"
                            variant="simple"
                            >{{ formLogin.errors.kata_sandi }}</Message
                        >
                    </div>
                    <div>
                        <Button
                            severity="info"
                            class="w-full"
                            size="small"
                            label="Masuk"
                            type="submit"
                        />
                    </div>
                </form>
            </template>
        </Card>
    </div>
</template>
