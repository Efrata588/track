<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    ArrowRight,
    Calendar,
    CheckCircle2,
    Clock,
    Sparkles,
} from '@lucide/vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { dashboard, login, register } from '@/routes';

const page = usePage();
const name = page.props.name;
</script>

<template>
    <Head title="Welcome" />

    <div
        class="flex min-h-screen flex-col bg-background text-foreground selection:bg-primary selection:text-primary-foreground"
    >
        <!-- Navigation bar -->
        <header
            class="sticky top-0 z-40 border-b border-border/70 bg-background/80 backdrop-blur-md"
        >
            <div
                class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8"
            >
                <div class="flex items-center gap-2.5">
                    <div
                        class="flex size-9 items-center justify-center rounded-lg bg-primary text-primary-foreground shadow-xs"
                    >
                        <AppLogoIcon class="size-5 fill-current" />
                    </div>
                    <span class="text-lg font-bold tracking-tight">{{
                        name
                    }}</span>
                </div>

                <nav class="flex items-center gap-3">
                    <Link v-if="$page.props.auth.user" :href="dashboard()">
                        <Button class="gap-1.5 font-medium shadow-xs">
                            Dashboard
                            <ArrowRight class="size-4" />
                        </Button>
                    </Link>
                    <template v-else>
                        <Link :href="login()">
                            <Button variant="ghost" class="font-medium">
                                Log in
                            </Button>
                        </Link>
                        <Link :href="register()">
                            <Button class="font-medium shadow-xs">
                                Register
                            </Button>
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <!-- Hero section -->
        <main class="flex-1">
            <section class="relative overflow-hidden py-20 sm:py-28 lg:py-32">
                <div
                    class="pointer-events-none absolute -top-40 left-1/2 -z-10 -translate-x-1/2 transform-gpu blur-3xl sm:-top-80"
                    aria-hidden="true"
                >
                    <div
                        class="aspect-1155/678 w-[68.4375rem] bg-gradient-to-tr from-primary/30 to-blue-400/20 opacity-40 dark:opacity-20"
                    />
                </div>

                <div class="mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
                    <div
                        class="mx-auto mb-6 inline-flex items-center gap-2 rounded-full border border-primary/20 bg-primary/10 px-3.5 py-1 text-xs font-medium text-primary"
                    >
                        <Sparkles class="size-3.5" />
                        <span>Simple, focused task management</span>
                    </div>

                    <h1
                        class="mx-auto max-w-4xl text-4xl font-extrabold tracking-tight sm:text-5xl lg:text-6xl"
                    >
                        Stay organized.<br />
                        <span class="text-primary"
                            >Accomplish more every day.</span
                        >
                    </h1>

                    <p
                        class="mx-auto mt-6 max-w-2xl text-base leading-relaxed text-muted-foreground sm:text-lg"
                    >
                        An elegant task manager designed to keep your priorities
                        crystal clear. Group tasks by Today, Upcoming, and Past
                        Due with instant status updates.
                    </p>

                    <div
                        class="mt-10 flex flex-wrap items-center justify-center gap-4"
                    >
                        <Link v-if="$page.props.auth.user" :href="dashboard()">
                            <Button
                                size="lg"
                                class="gap-2 font-medium shadow-md"
                            >
                                Go to Dashboard
                                <ArrowRight class="size-4" />
                            </Button>
                        </Link>
                        <template v-else>
                            <Link :href="register()">
                                <Button
                                    size="lg"
                                    class="gap-2 font-medium shadow-md"
                                >
                                    Get Started
                                    <ArrowRight class="size-4" />
                                </Button>
                            </Link>
                            <Link :href="login()">
                                <Button
                                    size="lg"
                                    variant="outline"
                                    class="font-medium"
                                >
                                    Sign In
                                </Button>
                            </Link>
                        </template>
                    </div>

                    <!-- Feature cards grid -->
                    <div
                        class="mt-20 grid grid-cols-1 gap-6 text-left sm:grid-cols-3"
                    >
                        <Card class="border-border/80 shadow-xs">
                            <CardContent class="space-y-2 p-6">
                                <div
                                    class="mb-3 flex size-10 items-center justify-center rounded-lg bg-primary/10 text-primary"
                                >
                                    <Calendar class="size-5" />
                                </div>
                                <h3 class="text-lg font-semibold">
                                    Today's Focus
                                </h3>
                                <p class="text-sm text-muted-foreground">
                                    Direct visibility on tasks scheduled for
                                    today so you know what needs your immediate
                                    attention.
                                </p>
                            </CardContent>
                        </Card>

                        <Card class="border-border/80 shadow-xs">
                            <CardContent class="space-y-2 p-6">
                                <div
                                    class="mb-3 flex size-10 items-center justify-center rounded-lg bg-blue-500/10 text-blue-500"
                                >
                                    <Clock class="size-5" />
                                </div>
                                <h3 class="text-lg font-semibold">
                                    Deadline Tracking
                                </h3>
                                <p class="text-sm text-muted-foreground">
                                    Automated past-due detection keeps you
                                    informed before overdue tasks slip through
                                    the cracks.
                                </p>
                            </CardContent>
                        </Card>

                        <Card class="border-border/80 shadow-xs">
                            <CardContent class="space-y-2 p-6">
                                <div
                                    class="mb-3 flex size-10 items-center justify-center rounded-lg bg-emerald-500/10 text-emerald-500"
                                >
                                    <CheckCircle2 class="size-5" />
                                </div>
                                <h3 class="text-lg font-semibold">
                                    Quick Actions
                                </h3>
                                <p class="text-sm text-muted-foreground">
                                    One-click completion toggles, instant task
                                    creation, and smooth inline edits with full
                                    keyboard accessibility.
                                </p>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer
            class="border-t border-border/60 py-6 text-center text-xs text-muted-foreground"
        >
            &copy; {{ new Date().getFullYear() }} {{ name }}. All rights
            reserved.
        </footer>
    </div>
</template>
