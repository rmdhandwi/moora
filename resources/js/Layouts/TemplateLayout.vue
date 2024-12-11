<script setup>
import Sidebar from "@/Components/Sidebar.vue";
import Navbar from "@/Components/Navbar.vue";

// Terima data auth dari DashboardPage sebagai props
const props = defineProps({
  auth: Object,
});

</script>

<template>
    <div class="flex">
        <Sidebar class="w-64" :auth="auth" />
        <div class="flex flex-col w-full">
            <Navbar />
            <div class="p-4">
                <slot name="content" />
            </div>
        </div>
    </div>
</template>

<style scoped></style>
