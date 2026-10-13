<script setup lang="ts">
defineProps<{
    message?: string;
}>();
</script>

<template>
    <div v-show="message">
        <p class="text-xs font-medium text-destructive dark:text-red-400">
            {{ message }}
        </p>
    </div>
</template>
