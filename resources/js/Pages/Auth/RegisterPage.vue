<script setup>
import {
    Card,
    InputText,
    RadioButton,
    Password,
    FloatLabel,
    Button,
    Message,
    Toast,
    useToast,
} from "primevue";
import { useForm, Head, router } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";

// cek data dari controller
const pageChack = defineProps({
    flash: Object,
});

// penampung data
const formRegister = useForm({
    nama_pengguna: null,
    kata_sandi: null,
    role: null,
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

const refrashLoading = ref(false);
const refresh = () => {
    ChackNotif();
    refrashLoading.value = true;
    router.visit(route("login"));
    formRegister.reset();
    setTimeout(() => (refrashLoading.value = false), 500);
};

// Fungsi untuk submit registrasi
const RegisterSubmit = () => {
    formRegister.post(route("registerSubmit"), {
        onSuccess: () => refresh(),
        onError: () => ShowToast(),
    });
};
</script>

<template>
    <Toast position="top-center" group="tc" />
    <Head title="Daftar" />
    <div class="flex flex-col justify-center min-h-screen items-center">
        <div class="flex">
            <div class="logo">
                <img src="" alt="logo.png" />
            </div>
            <div class="judu ms-3 font-bold">
                <h1>Register</h1>
            </div>
        </div>
        <Card class="w-2/5 shadow-xl">
            <template #content>
                <form @submit.prevent="RegisterSubmit">
                    <!-- Input Nama Pengguna -->
                    <div class="flex flex-col gap-1 mb-4">
                        <FloatLabel variant="on">
                            <InputText
                                id="nama_pengguna"
                                v-model="formRegister.nama_pengguna"
                                class="w-full"
                                :invalid="formRegister.errors.nama_pengguna"
                            />
                            <label for="nama_pengguna">Nama Pengguna</label>
                        </FloatLabel>
                        <Message
                            v-if="formRegister.errors.nama_pengguna"
                            severity="error"
                            size="small"
                            variant="simple"
                            >{{ formRegister.errors.nama_pengguna }}</Message
                        >
                    </div>

                    <!-- Input Kata Sandi -->
                    <div class="flex flex-col gap-1 mb-4">
                        <FloatLabel variant="on">
                            <Password
                                fluid
                                id="kata_sandi"
                                toggleMask
                                v-model="formRegister.kata_sandi"
                                :invalid="formRegister.errors.kata_sandi"
                            />
                            <label for="kata_sandi">Kata Sandi</label>
                        </FloatLabel>
                        <Message
                            v-if="formRegister.errors.kata_sandi"
                            severity="error"
                            size="small"
                            variant="simple"
                            >{{ formRegister.errors.kata_sandi }}</Message
                        >
                    </div>

                    <!-- Input Role -->
                    <div class="flex flex-wrap gap-4 mb-2">
                        <div class="flex items-center gap-2">
                            <RadioButton
                                v-model="formRegister.role"
                                inputId="admin"
                                value="0"
                                :invalid="formRegister.errors.role"
                                size="small"
                            />
                            <label class="text-sm" for="admin">Admin</label>
                        </div>
                        <div class="flex items-center gap-2">
                            <RadioButton
                                v-model="formRegister.role"
                                inputId="staff"
                                value="1"
                                :invalid="formRegister.errors.role"
                                size="small"
                            />
                            <label class="text-sm" for="staff">Staff</label>
                        </div>
                        <div class="flex items-center gap-2">
                            <RadioButton
                                v-model="formRegister.role"
                                inputId="dosen"
                                value="2"
                                :invalid="formRegister.errors.role"
                                size="small"
                            />
                            <label class="text-sm" for="dosen">Dosen</label>
                        </div>
                        <div class="flex items-center gap-2">
                            <RadioButton
                                v-model="formRegister.role"
                                inputId="mahasiswa"
                                value="3"
                                :invalid="formRegister.errors.role"
                                size="small"
                            />
                            <label class="text-sm" for="mahasiswa"
                                >Mahasiswa</label
                            >
                        </div>
                    </div>

                    <!-- Pesan error untuk Role -->
                    <Message
                        v-if="formRegister.errors.role"
                        severity="error"
                        size="small"
                        variant="simple"
                        >{{ formRegister.errors.role }}</Message
                    >

                    <!-- Tombol Daftar -->
                    <div>
                        <Button
                            severity="info"
                            class="w-full mt-2"
                            size="small"
                            label="Daftar"
                            type="submit"
                        />
                    </div>
                </form>
            </template>
        </Card>
    </div>
</template>

<style scoped>
/* Anda bisa menambahkan style tambahan di sini jika perlu */
</style>
