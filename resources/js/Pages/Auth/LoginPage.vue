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
const props = defineProps({
    flash: Object,
});

// Cek notifikasi saat komponen di-mount
onMounted(() => {
    setTimeout(() => {
        ShowToast();
    }, 500);
});

// Fungsi untuk menampilkan notifikasi jika ada pesan flash
const toast = useToast();
const ShowToast = () => {
    if (props.flash.notif && props.flash.message) {
        toast.add({
            severity: props.flash.notif || "info", // Gunakan 'info' sebagai default jika tidak ada notif
            summary:
                props.flash.notif.charAt(0).toUpperCase() +
                props.flash.notif.slice(1), // Kapitalisasi huruf pertama
            detail: props.flash.message,
            life: 4000,
            group: "tc",
        });
    }
};

// Data formulir login
const formLogin = useForm({
    username: "",
    password: "",
});

// Fungsi untuk menangani pengiriman form login
const LoginSubmit = () => {
    formLogin.post(route("LoginSubmit"), {
        onSuccess: () => {
            if (props.flash.notif === "success") {
                router.visit(route("dashboard"));
            } else {
                formLogin.reset();
                ShowToast();
            }
        },
    });
};
</script>

<template>
    <Toast position="top-center" group="tc" />
    <Head title="Selamat Datang" />
    <div
        class="relative flex flex-col justify-center min-h-screen items-center bg-gray-100"
    >
        <div class="absolute inset-0">
            <img
                src="image/bg.jpg"
                alt="Background"
                class="w-full h-full object-cover"
            />
            <div
                class="absolute inset-0 bg-gradient-to-b from-transparent to-gray-800 opacity-70"
            ></div>
        </div>
        <div class="flex mb-4 z-10 items-center">
            <div class="logo">
                <img
                    src="image/logo/android-chrome-512x512.png"
                    alt="logo.png"
                    class="h-[100px] w-[100px]"
                />
            </div>
            <div class="judul ms-3 font-bold text-2xl text-white">
                <h1>SPK | MASMA</h1>
            </div>
        </div>
        <Card
            unstyled
            class="w-full md:w-2/4 sm:w-2/4 lg:w-[25%] bg-white bg-opacity-50 rounded-lg backdrop-blur-md p-6 z-10"
        >
            <template #content>
                <form @submit.prevent="LoginSubmit">
                    <div class="flex flex-col mb-6">
                        <FloatLabel variant="in">
                            <InputText
                                id="username"
                                v-model="formLogin.username"
                                class="w-full"
                                :invalid="!!formLogin.errors.username"
                            />
                            <label for="username">Username</label>
                        </FloatLabel>
                        <Message
                            v-if="formLogin.errors.username"
                            severity="error"
                            size="small"
                            variant="simple"
                            >{{ formLogin.errors.username }}</Message
                        >
                    </div>
                    <div class="flex flex-col mb-6">
                        <FloatLabel variant="in">
                            <Password
                                fluid
                                toggleMask
                                id="password"
                                :feedback="false"
                                v-model="formLogin.password"
                                :invalid="!!formLogin.errors.password"
                            />
                            <label for="password">Password</label>
                        </FloatLabel>
                        <Message
                            v-if="formLogin.errors.password"
                            severity="error"
                            size="small"
                            variant="simple"
                            >{{ formLogin.errors.password }}</Message
                        >
                    </div>
                    <div>
                        <Button
                            fluid
                            severity="info"
                            class="w-full"
                            label="Masuk"
                            type="submit"
                        />
                    </div>
                </form>
            </template>
        </Card>
    </div>
</template>

<style scoped>
/* Tambahkan gaya tambahan di sini */
body {
    font-family: "Arial", sans-serif;
}

h1 {
    color: #ffffff; /* Warna teks judul */
}

input:focus,
.p-inputtext:focus {
    border-color: #3b82f6; /* Warna border saat fokus */
    box-shadow: 0 0 0 1px rgba(59, 130, 246, 0.5); /* Bayangan saat fokus */
}

button {
    transition: background-color 0.3s ease; /* Transisi untuk tombol */
}
</style>
