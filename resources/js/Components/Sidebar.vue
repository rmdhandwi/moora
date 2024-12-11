<!-- resources/js/components/Sidebar.vue -->
<script setup>
import { PanelMenu, Button } from "primevue";
import { ref, computed } from "vue";

// Terima data auth sebagai props dari TemplateLayout
const props = defineProps({
    auth: Object,
});

// Memastikan data auth ada sebelum mengaksesnya
const currentUserRole = computed(() => props.auth.user.role);

const items = ref([
    {
        label: "Dashboard",
        icon: "pi pi-fw pi-home",
        to: "/dashboard",
        roles: [0, 1, 2, 3],
    },
    {
        label: "Users",
        icon: "pi pi-fw pi-users",
        to: "/user",
        roles: [0],
    },
    {
        label: "Settings",
        icon: "pi pi-fw pi-cog",
        to: "/mahasiswa",
        roles: [0, 1, 2],
    },
]);

const filterUser = computed(() => {
    // Pastikan currentUserRole memiliki nilai sebelum melakukan filter
    return items.value.filter(
        (item) =>
            currentUserRole.value !== null &&
            item.roles.includes(currentUserRole.value)
    );
});
</script>

<template>
    <div class="sidebar p-4">
        <!-- Use anchor tags to link directly to Laravel routes -->
        <PanelMenu :model="filterUser" class="w-full">
            <template #item="{ item }">
                <Button
                    :href="item.to"
                    unstyled
                    as="a"
                    class="flex items-center px-4 py-5 cursor-pointer group hover:bg-slate-400 hover:text-white transition"
                >
                    <span
                        :class="[
                            item.icon,
                            'text-primary group-hover:text-inherit',
                        ]"
                    ></span>
                    <span :class="['ml-2', { 'font-semibold': item.items }]">{{
                        item.label
                    }}</span>
                </Button>
            </template>
        </PanelMenu>
    </div>
</template>

<style scoped>
.sidebar {
    width: 250px;
    height: 100vh;
    background-color: #f4f4f4;
    border-right: 1px solid #ddd;
}
</style>
