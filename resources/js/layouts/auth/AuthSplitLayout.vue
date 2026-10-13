<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { CheckCircle2, Clock, Sparkles } from '@lucide/vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { home } from '@/routes';

const page = usePage();
const name = page.props.name;

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="relative min-h-svh w-full lg:grid lg:grid-cols-2">
        <!-- Left brand panel (visible on lg+) -->
        <div
            class="relative hidden h-full flex-col justify-between overflow-hidden border-r border-border/60 bg-gradient-to-br from-slate-950 via-blue-950 to-slate-900 p-12 text-white lg:flex"
        >
            <!-- Ambient glows -->
            <div
                class="pointer-events-none absolute -top-24 -left-24 size-96 rounded-full bg-blue-500/20 blur-3xl"
            />
            <div
                class="pointer-events-none absolute -right-24 -bottom-24 size-96 rounded-full bg-indigo-500/20 blur-3xl"
            />

            <!-- Brand header -->
            <div class="relative z-10 flex items-center gap-3">
                <Link
                    :href="home()"
                    class="flex items-center gap-2.5 transition-opacity hover:opacity-90"
                >
                    <div
                        class="flex size-9 items-center justify-center rounded-lg bg-blue-600 text-white shadow-md shadow-blue-500/30"
                    >
                        <AppLogoIcon class="size-5 fill-current" />
                    </div>
                    <span class="text-xl font-bold tracking-tight text-white">{{
                        name
                    }}</span>
                </Link>
            </div>

            <!-- Value proposition & benefits -->
            <div class="relative z-10 my-auto max-w-md space-y-8 py-12">
                <div class="space-y-3">
                    <h2
                        class="text-3xl font-bold tracking-tight text-white sm:text-4xl"
                    >
                        Organize with clarity.<br />Deliver on time.
                    </h2>
                    <p class="text-base leading-relaxed text-blue-200/80">
                        A focused task management dashboard built for momentum,
                        clarity, and seamless everyday productivity.
                    </p>
                </div>

                <div class="space-y-4">
                    <div class="flex items-start gap-3.5">
                        <div
                            class="flex size-7 shrink-0 items-center justify-center rounded-md bg-blue-500/20 text-blue-400"
                        >
                            <CheckCircle2 class="size-4" />
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-white">
                                Instant organization
                            </p>
                            <p class="text-xs text-blue-200/70">
                                Filter tasks by Today, Upcoming, and Past Due in
                                real time.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3.5">
                        <div
                            class="flex size-7 shrink-0 items-center justify-center rounded-md bg-blue-500/20 text-blue-400"
                        >
                            <Clock class="size-4" />
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-white">
                                Stay ahead of deadlines
                            </p>
                            <p class="text-xs text-blue-200/70">
                                Live status updates keep pending and overdue
                                tasks clearly visible.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3.5">
                        <div
                            class="flex size-7 shrink-0 items-center justify-center rounded-md bg-blue-500/20 text-blue-400"
                        >
                            <Sparkles class="size-4" />
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-white">
                                Modern, fluid workflow
                            </p>
                            <p class="text-xs text-blue-200/70">
                                Lightweight, accessible design system tailored
                                for speed.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Brand panel footer -->
            <div class="relative z-10 text-xs text-blue-200/50">
                &copy; {{ new Date().getFullYear() }} {{ name }}. All rights
                reserved.
            </div>
        </div>

        <!-- Right / mobile form column -->
        <div
            class="flex min-h-svh flex-col items-center justify-center bg-background p-6 md:p-10"
        >
            <div class="w-full max-w-sm sm:max-w-md">
                <!-- Mobile brand logo (visible < lg) -->
                <div class="mb-8 flex flex-col items-center gap-2 lg:hidden">
                    <Link :href="home()" class="flex items-center gap-2.5">
                        <div
                            class="flex size-10 items-center justify-center rounded-lg bg-primary text-primary-foreground shadow-sm"
                        >
                            <AppLogoIcon class="size-6 fill-current" />
                        </div>
                        <span class="text-xl font-bold tracking-tight">{{
                            name
                        }}</span>
                    </Link>
                </div>

                <!-- Page heading & description -->
                <div class="mb-8 space-y-2 text-center lg:text-left">
                    <h1
                        v-if="title"
                        class="text-2xl font-semibold tracking-tight text-foreground"
                    >
                        {{ title }}
                    </h1>
                    <p v-if="description" class="text-sm text-muted-foreground">
                        {{ description }}
                    </p>
                </div>

                <!-- Form content slot -->
                <slot />
            </div>
        </div>
    </div>
</template>
