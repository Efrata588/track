<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { useSidebar } from '@/components/ui/sidebar/utils';
import type { BreadcrumbItem } from '@/types';

defineProps<{
    breadcrumbs?: BreadcrumbItem[];
}>();

const hasSidebar = useSidebar(null) !== null;
</script>

<template>
    <div v-if="hasSidebar" class="contents">
        <slot />
    </div>
    <AppLayout v-else :breadcrumbs="breadcrumbs">
        <slot />
    </AppLayout>
</template>
