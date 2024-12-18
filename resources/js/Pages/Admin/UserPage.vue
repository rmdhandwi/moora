<script setup>
import { onMounted, ref } from "vue";
import { Head, router, useForm } from "@inertiajs/vue3";
import {
    Toast,
    useToast,
    Card,
    Button,
    Dialog,
    DataTable,
    Column,
    FloatLabel,
    Message,
    IconField,
    InputIcon,
    InputText,
    Password,
    ConfirmPopup,
    Tag,
    useConfirm,
} from "primevue";
import TemplateLayout from "@/Layouts/TemplateLayout.vue";
import { FilterMatchMode } from "@primevue/core/api";

// cek data dari controller
const props = defineProps({
    flash: Object,
    title: String,
    auth: Object,
    users: Object,
});

// ceknotif
onMounted(() => {
    ShowToast();
    dataUsers.value = props.users.map((users, index) => ({
        index: index + 1,
        ...users,
    }));
});

const dt = ref([]);
const dataUsers = ref([]);

// Filter untuk DataTable
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

// Fungsi untuk mengekspor DataTable ke CSV
const exportCSV = () => {
    dt.value.exportCSV();
};

// notif
const toast = useToast();
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

const showDialog = ref(false);
const dataDialog = ref(null);

const formUser = useForm({
    user_id: "",
    username: "",
    password: "",
    role: "",
});

const addDialog = () => {
    formUser.reset();
    formUser.clearErrors();
    dataDialog.value = "add";
    showDialog.value = true;
};

const refresh = () => {
    return new Promise((resolve) => {
        router.visit(route("usersPage"), {
            onSuccess: () => {
                resolve();
            },
        });
    });
};

const createUser = () => {
    formUser.post(route("create.user"), {
        onSuccess: async () => {
            await refresh();
            ShowToast();
            showDialog.value = false;
        },
        onError: () => {
            toast.add({
                severity: "error",
                summary: "Kesalahan",
                detail: "Terjadi kesalah",
                life: 3000,
                group: "tc",
            });
            showDialog.value = true;
        },
    });
};

const confirm = useConfirm();
const confirmEdit = (data) => {
    confirm.require({
        message: `Anda ingin mengedit user : ${data.username}`,
        icon: "pi pi-exclamation-triangle",
        rejectProps: {
            icon: "pi pi-times",
            label: "Cancel",
            severity: "secondary",
            outlined: true,
        },
        acceptProps: {
            icon: "pi pi-check",
            label: "Edit",
        },
        accept: () => {
            formUser.reset();
            formUser.clearErrors();
            dataDialog.value = "edit";
            formUser.user_id = data.user_id;
            formUser.username = data.username;
            formUser.role = data.role;
            showDialog.value = true;
        },
        reject: () => {
            toast.add({
                severity: "info",
                summary: "Dibatalkan",
                detail: "Pembaharuan data dibatalkan.",
                life: 3000,
                group: "tc",
            });
        },
    });
};

const updateUser = () => {
    formUser.put(route("update.user", formUser.user_id), {
        onSuccess: async () => {
            await refresh();
            ShowToast();
            showDialog.value = false;
        },
        onError: () => {
            toast.add({
                severity: "error",
                summary: "Kesalahan",
                detail: "Terjadi kesalah",
                life: 3000,
                group: "tc",
            });
            showDialog.value = true;
        },
    });
};

const confirmDelete = (data) => {
    confirm.require({
        message: `Anda ingin menghapus user : ${data.username}`,
        icon: "pi pi-exclamation-triangle",
        rejectProps: {
            icon: "pi pi-times",
            label: "Cancel",
            severity: "secondary",
            outlined: true,
        },
        acceptProps: {
            severity: "danger",
            icon: "pi pi-trash",
            label: "Hapus",
        },
        accept: () => {
            router.delete(route("destroy.user", data.user_id), {
                onSuccess: async () => {
                    await refresh();
                    ShowToast();
                    showDialog.value = false;
                },
                onError: () => {
                    ShowToast();
                    showDialog.value = true;
                },
            });
        },
        reject: () => {
            toast.add({
                severity: "info",
                summary: "Dibatalkan",
                detail: "Penghapusan data dibatalkan.",
                life: 3000,
                group: "tc",
            });
        },
    });
};
</script>

<template>
    <Head :title="props.title" />
    <Toast position="top-center" group="tc" />
    <ConfirmPopup></ConfirmPopup>
    <TemplateLayout :auth="props.auth" :title="props.title">
        <template #content>
            <div class="flex items-center justify-between mb-4">
                <span class="text-2xl font-bold">Data {{ props.title }}</span>
                <Button
                    @click="addDialog"
                    unstyled
                    class="px-4 py-2 bg-blue-500 hover:-translate-x-1 text-white rounded-md transition-all hover:bg-blue-600"
                >
                    <template #default>
                        <div class="flex gap-2 items-center">
                            <i class="pi pi-plus-circle"></i>
                            <span>Tambah data</span>
                        </div>
                    </template>
                </Button>
            </div>

            <!-- FormDialog -->

            <Dialog
                v-model:visible="showDialog"
                modal
                :header="
                    dataDialog === 'add' ? 'Tambah Data User' : 'Edit Data User'
                "
                :style="{ width: '30vw' }"
                :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            >
                <form
                    @submit.prevent="
                        dataDialog === 'add' ? createUser() : updateUser()
                    "
                >
                    <div class="space-y-6">
                        <div class="mt-2">
                            <FloatLabel variant="on">
                                <InputText
                                    id="username"
                                    fluid
                                    v-model="formUser.username"
                                    :invalid="!!formUser.errors.username"
                                />
                                <label for="username">Username</label>
                            </FloatLabel>
                            <Message
                                v-if="!!formUser.errors.username"
                                severity="error"
                                size="small"
                                variant="simple"
                                >{{ formUser.errors.username }}</Message
                            >
                        </div>

                        <div class="mt-2">
                            <FloatLabel variant="on">
                                <Password
                                    id="password"
                                    :feedback="false"
                                    toggle-mask
                                    fluid
                                    v-model="formUser.password"
                                    :invalid="!!formUser.errors.password"
                                />
                                <label for="password">Password</label>
                            </FloatLabel>
                            <Message
                                v-if="formUser.errors.password"
                                severity="error"
                                size="small"
                                variant="simple"
                                >{{ formUser.errors.password }}</Message
                            >
                        </div>

                        <div class="mt-2">
                            <label>Role</label>
                            <div
                                class="items-center gap-4 grid grid-cols-2 mt-1"
                            >
                                <label>
                                    <input
                                        type="radio"
                                        value="1"
                                        v-model="formUser.role"
                                    />
                                    Admin
                                </label>
                                <label>
                                    <input
                                        type="radio"
                                        value="2"
                                        v-model="formUser.role"
                                    />
                                    Staff Prodi
                                </label>
                                <label>
                                    <input
                                        type="radio"
                                        value="3"
                                        v-model="formUser.role"
                                    />
                                    Dosen
                                </label>
                                <label>
                                    <input
                                        type="radio"
                                        value="4"
                                        v-model="formUser.role"
                                    />
                                    Mahasiswa
                                </label>
                            </div>
                            <Message
                                v-if="formUser.errors.role"
                                severity="error"
                                size="small"
                                variant="simple"
                                >{{ formUser.errors.role }}</Message
                            >
                        </div>

                        <div class="flex justify-end items-center gap-2">
                            <Button
                                unstyled
                                @click="showDialog = false"
                                class="flex text-red-500 justify-between items-center gap-2 px-4 py-1 border border-red-500 rounded-md transition-all hover:bg-red-500 hover:text-white"
                            >
                                <span>Batal</span>
                                <i class="pi pi-times"></i>
                            </Button>
                            <Button
                                type="submit"
                                unstyled
                                class="flex text-blue-500 justify-between items-center gap-2 px-4 py-1 border border-blue-500 rounded-md transition-all hover:bg-blue-500 hover:text-white"
                            >
                                <span>Simpan</span>
                                <i class="pi pi-check-circle"></i>
                            </Button>
                        </div>
                    </div>
                </form>
            </Dialog>
            <!-- endForm -->

            <!-- Tabel User -->
            <Card>
                <template #content>
                    <DataTable
                        ref="dt"
                        :value="dataUsers"
                        stripedRows
                        paginator
                        :rows="10"
                        scrollable
                        responsiveLayout="scroll"
                        :filters="filters"
                        resizableColumns
                        :rowsPerPageOptions="[5, 10, 20, 50, 100]"
                        columnResizeMode="fit"
                        size="large"
                    >
                        <!-- Pesan jika data kosong -->
                        <template #empty>
                            <div class="text-center">
                                Data User tidak Ditemukan
                            </div>
                        </template>

                        <!-- Header tabel -->
                        <template #header>
                            <div class="flex justify-between items-center">
                                <Button
                                    unstyled
                                    @click="exportCSV"
                                    class="px-4 py-2 bg-blue-500 hover:-translate-x-1 text-white rounded-md transition-all hover:bg-blue-600"
                                >
                                    <template #default>
                                        <div
                                            class="flex gap-2 items-center text-sm"
                                        >
                                            <i class="pi pi-external-link"></i>
                                            <span>Export</span>
                                        </div>
                                    </template>
                                </Button>
                                <IconField>
                                    <InputIcon>
                                        <i class="pi pi-search me-4" />
                                    </InputIcon>
                                    <InputText
                                        v-model="filters.global.value"
                                        placeholder="Cari Data User"
                                    />
                                </IconField>
                            </div>
                        </template>

                        <Column field="index" header="No" />
                        <Column field="user_id" header="Kode User" />
                        <Column field="username" header="Username" />
                        <Column header="Role">
                            <template #body="{ data }">
                                <Tag
                                    v-if="data.role === 1"
                                    :value="
                                        data.role === 1
                                            ? 'Admin'
                                            : 'Role Tidak ditemukan!'
                                    "
                                    icon="pi pi-user"
                                    severity="danger"
                                />
                                <Tag
                                    v-else-if="data.role === 2"
                                    :value="
                                        data.role === 2
                                            ? 'Staff'
                                            : 'Role Tidak ditemukan!'
                                    "
                                    icon="pi pi-user"
                                    severity="secondary"
                                />
                                <Tag
                                    v-else-if="data.role === 3"
                                    :value="
                                        data.role === 3
                                            ? 'Dosen'
                                            : 'Role Tidak ditemukan!'
                                    "
                                    icon="pi pi-user"
                                    severity="Info"
                                />
                                <Tag
                                    v-else-if="data.role === 4"
                                    :value="
                                        data.role === 4
                                            ? 'Mahasiswa'
                                            : 'Role Tidak ditemukan!'
                                    "
                                    icon="pi pi-user"
                                    severity="Info"
                                />
                                <span class="text-sm" v-else
                                    ><i>Role Tidak Terdaftar</i></span
                                >
                            </template>
                        </Column>

                        <Column header="Opsi" frozen alignFrozen="right">
                            <template #body="{ data }">
                                <div class="flex gap-2 items-center">
                                    <Button
                                        size="small"
                                        @click="confirmEdit(data)"
                                        icon="pi pi-pen-to-square"
                                        severity="info"
                                        outlined
                                    />
                                    <Button
                                        size="small"
                                        @click="confirmDelete(data)"
                                        icon="pi pi-trash"
                                        severity="danger"
                                        outlined
                                    />
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </template>
            </Card>
            <!-- endTabel -->
        </template>
    </TemplateLayout>
</template>

<style scoped></style>
