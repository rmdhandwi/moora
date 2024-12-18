<script setup>
import { onMounted, computed, ref } from "vue";
import {
    Button,
    Tag,
    Toast,
    useToast,
    ConfirmPopup,
    useConfirm,
} from "primevue";
import { router } from "@inertiajs/vue3";

// Terima data auth dari DashboardPage sebagai props
const props = defineProps({
    auth: Object,
    title: String,
});

// State untuk mengontrol visibilitas sidebar
const isSidebarOpen = ref(true);

// Fungsi untuk toggle sidebar
const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};

const title = "SISTEM PENDUKUNG KEPUTUSAN MASA STUDI MAHASISWA";
const shortTitle = "SPK-MASMA";

const windowWidth = ref(window.innerWidth);

// Update window width on resize
const updateWindowWidth = () => {
    windowWidth.value = window.innerWidth;
};

onMounted(() => {
    window.addEventListener("resize", updateWindowWidth);
});

// Computed property to determine which title to display
const displayTitle = computed(() => {
    return windowWidth.value < 768 ? shortTitle : title;
});

const confirm = useConfirm();
const toast = useToast();

const logout = (event) => {
    confirm.require({
        target: event.currentTarget,
        group: "templating",
        message: "Apakah anda ingin logout Sekarang?",
        icon: "pi pi-exclamation-circle",
        rejectProps: {
            icon: "pi pi-times",
            label: "Tidak",
            severity: "danger",
            outlined: true,
        },
        acceptProps: {
            icon: "pi pi-check",
            label: "Ya",
        },
        accept: () => {
            router.post(route("logout"));
        },
        reject: () => {
            toast.add({
                severity: "info",
                summary: "Dibatalkan",
                detail: "Batal Untuk keluar",
                life: 3000,
            });
        },
    });
};
</script>

<template>
    <Toast />
    <ConfirmPopup group="templating">
        <template #message="slotProps">
            <div
                class="flex flex-col items-center w-full gap-4 border-b border-surface-200 dark:border-surface-700 p-4 mb-4 pb-0"
            >
                <i
                    :class="slotProps.message.icon"
                    class="text-6xl text-primary-500"
                ></i>
                <p>{{ slotProps.message.message }}</p>
            </div>
        </template>
    </ConfirmPopup>
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside
            :class="isSidebarOpen ? 'w-64' : 'w-0'"
            class="bg-white shadow-md transition-width duration-300 overflow-hidden"
        >
            <div class="p-4">
                <h1
                    class="text-[1.5rem] text-center text-blue-400 font-bold floating-animation"
                >
                    SPK-MASMA
                </h1>
            </div>
            <hr />
            <nav class="mt-4">
                <div class="flex flex-col gap-2 m-4">
                    <Button
                        unstyled
                        as="a"
                        :href="route('dashboard')"
                        :class="[
                            'rounded-md p-3 gap-2 transition-all flex items-center',
                            props.title === 'Dashboard'
                                ? 'bg-blue-500 text-white font-semibold hover:-translate-y-1'
                                : 'hover:bg-blue-100 hover:-translate-y-1',
                        ]"
                    >
                        <template #default>
                            <i class="pi pi-home"></i>
                            <span>Dashboard</span>
                        </template>
                    </Button>
                    <Button
                        unstyled
                        as="a"
                        :href="route('usersPage')"
                        :class="[
                            'rounded-md p-3 gap-2 transition-all flex items-center',
                            props.title === 'Users'
                                ? 'bg-blue-500 text-white font-semibold hover:-translate-y-1'
                                : 'hover:bg-blue-100 hover:-translate-y-1',
                        ]"
                    >
                        <template #default>
                            <i class="pi pi-users"></i>
                            <span>Users</span>
                        </template>
                    </Button>
                    <Button
                        unstyled
                        as="a"
                        :href="route('dosenPage')"
                        :class="[
                            'rounded-md p-3 gap-2 transition-all flex items-center',
                            props.title === 'Dosen'
                                ? 'bg-blue-500 text-white font-semibold hover:-translate-y-1'
                                : 'hover:bg-blue-100 hover:-translate-y-1',
                        ]"
                    >
                        <template #default>
                            <i class="pi pi-user"></i>
                            <span>Dosen</span>
                        </template>
                    </Button>
                    <Button
                        unstyled
                        as="a"
                        :href="route('angkatanPage')"
                        :class="[
                            'rounded-md p-3 gap-2 transition-all flex items-center',
                            props.title === 'Angkatan'
                                ? 'bg-blue-500 text-white font-semibold hover:-translate-y-1'
                                : 'hover:bg-blue-100 hover:-translate-y-1',
                        ]"
                    >
                        <template #default>
                            <i class="pi pi-chart-line"></i>
                            <span>Angkatan</span>
                        </template>
                    </Button>
                    <Button
                        unstyled
                        as="a"
                        :href="route('mahasiswaPage')"
                        :class="[
                            'rounded-md p-3 gap-2 transition-all flex items-center',
                            props.title === 'Mahasiswa'
                                ? 'bg-blue-500 text-white font-semibold hover:-translate-y-1'
                                : 'hover:bg-blue-100 hover:-translate-y-1',
                        ]"
                    >
                        <template #default>
                            <i class="pi pi-users"></i>
                            <span>Mahasiswa</span>
                        </template>
                    </Button>
                    <Button
                        unstyled
                        as="a"
                        :href="route('kriteriaPage')"
                        :class="[
                            'rounded-md p-3 gap-2 transition-all flex items-center',
                            props.title === 'Kriteria'
                                ? 'bg-blue-500 text-white font-semibold hover:-translate-y-1'
                                : 'hover:bg-blue-100 hover:-translate-y-1',
                        ]"
                    >
                        <template #default>
                            <i class="pi pi-tags"></i>
                            <span>Kriteria</span>
                        </template>
                    </Button>
                    <Button
                        unstyled
                        as="a"
                        :href="route('perhitunganPage')"
                        :class="[
                            'rounded-md p-3 gap-2 transition-all flex items-center',
                            props.title === 'Perhitungan' || props.title === 'Hasil Perhitungan'
                                ? 'bg-blue-500 text-white font-semibold hover:-translate-y-1'
                                : 'hover:bg-blue-100 hover:-translate-y-1',
                        ]"
                    >
                        <template #default>
                            <i class="pi pi-calculator"></i>
                            <span>Perhitungan</span>
                        </template>
                    </Button>
                    <hr />
                    <Button
                        unstyled
                        @click="logout($event)"
                        :class="[
                            'rounded-md p-3 gap-2 transition-all flex items-center hover:bg-red-100 hover:-translate-y-1 hover:font-semibold hover:text-red-500',
                        ]"
                    >
                        <template #default>
                            <i class="pi pi-sign-out"></i>
                            <span>Logout</span>
                        </template>
                    </Button>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6 overflow-y-auto" style="padding-top: 80px">
            <header
                :class="[
                    'fixed top-0 mx-6 right-0 flex justify-between items-center mb-4 bg-white shadow rounded-md p-4 z-10',
                    isSidebarOpen ? 'left-64' : 'left-0',
                ]"
                style="height: 67px"
            >
                <div class="flex gap-2 items-center">
                    <Button
                        unstyled
                        @click="toggleSidebar"
                        class="text-grey-900 p-2 rounded hover:-translate-y-1 transition-all"
                    >
                        <i
                            :class="!isSidebarOpen ? 'pi pi-eye' : 'pi pi-bars'"
                        ></i>
                    </Button>
                    <span class="font-semibold judul">
                        {{ displayTitle }}
                    </span>
                </div>

                <Tag
                    icon="pi pi-user"
                    severity="secondary"
                    :value="props.auth.user.username"
                ></Tag>
            </header>

            <section>
                <slot name="content" />
            </section>
        </main>
    </div>
</template>

<style scoped>
/* Floating animation */
@keyframes floating {
    0% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px); /* Adjust the height of the float */
    }
    100% {
        transform: translateY(0);
    }
}

.floating-animation {
    animation: floating 2s ease-in-out infinite; /* Adjust duration and easing as needed */
}
</style>
