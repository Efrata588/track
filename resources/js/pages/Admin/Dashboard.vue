<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import {
    Calendar,
    CheckCircle2,
    Clock,
    Filter,
    Globe,
    Search,
    Shield,
    ShieldCheck,
    Smartphone,
    UserCheck,
    Users,
    XCircle,
} from '@lucide/vue';
import { computed, ref } from 'vue';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Admin Dashboard',
                href: '/admin/dashboard',
            },
        ],
    },
});

interface UserItem {
    id: number;
    name: string;
    email: string;
    role: string;
    status: string;
    last_client_used: string | null;
    last_login_at: string | null;
    created_at: string;
}

interface ClientUsage {
    web: number;
    mobile: number;
}

interface Props {
    totalUsers?: number;
    clientUsage?: ClientUsage;
    users?: UserItem[];
}

const props = withDefaults(defineProps<Props>(), {
    totalUsers: 0,
    clientUsage: () => ({ web: 0, mobile: 0 }),
    users: () => [],
});

const searchQuery = ref('');
const roleFilter = ref<'all' | 'admin' | 'user'>('all');
const statusFilter = ref<'all' | 'active' | 'suspended'>('all');
const clientFilter = ref<'all' | 'vue-web' | 'flutter-mobile' | 'never'>('all');

const filteredUsers = computed(() => {
    return props.users.filter((user) => {
        // Search query filter
        const query = searchQuery.value.toLowerCase().trim();
        const matchesSearch =
            !query ||
            user.name.toLowerCase().includes(query) ||
            user.email.toLowerCase().includes(query);

        // Role filter
        const matchesRole =
            roleFilter.value === 'all' || user.role === roleFilter.value;

        // Status filter
        const matchesStatus =
            statusFilter.value === 'all' || user.status === statusFilter.value;

        // Client filter
        let matchesClient = true;
        if (clientFilter.value === 'vue-web') {
            matchesClient = user.last_client_used === 'vue-web';
        } else if (clientFilter.value === 'flutter-mobile') {
            matchesClient = user.last_client_used === 'flutter-mobile';
        } else if (clientFilter.value === 'never') {
            matchesClient = !user.last_client_used;
        }

        return matchesSearch && matchesRole && matchesStatus && matchesClient;
    });
});

function formatDateTime(dateStr: string | null | undefined): string {
    if (!dateStr) return 'Never';
    try {
        const d = new Date(dateStr);
        if (isNaN(d.getTime())) return dateStr;
        return new Intl.DateTimeFormat(undefined, {
            month: 'short',
            day: 'numeric',
            year: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
        }).format(d);
    } catch {
        return dateStr;
    }
}

function clearFilters() {
    searchQuery.value = '';
    roleFilter.value = 'all';
    statusFilter.value = 'all';
    clientFilter.value = 'all';
}
</script>

<template>
    <Head title="Admin Dashboard" />

    <AuthenticatedLayout
        :breadcrumbs="[{ title: 'Admin Dashboard', href: '/admin/dashboard' }]"
    >
        <main class="mx-auto max-w-7xl space-y-8 p-4 sm:p-6 lg:p-8">
            <!-- Header Section -->
            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="space-y-1">
                    <div class="flex items-center gap-2.5">
                        <h1
                            class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl"
                        >
                            Admin Dashboard
                        </h1>
                        <Badge
                            variant="outline"
                            class="border-primary/30 bg-primary/10 text-primary text-xs font-semibold"
                        >
                            <ShieldCheck class="mr-1 size-3" />
                            Administrator
                        </Badge>
                    </div>
                    <p
                        class="flex items-center gap-1.5 text-sm text-muted-foreground"
                    >
                        <UserCheck class="size-4 text-primary shrink-0" />
                        <span>Platform user management, client distribution, and login activity.</span>
                    </p>
                </div>
            </div>

            <!-- Metric Cards Grid -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <!-- 1. Total Users -->
                <Card
                    class="border-border/80 shadow-xs transition-shadow hover:shadow-sm"
                >
                    <CardHeader class="pb-2">
                        <div class="flex items-center justify-between">
                            <CardDescription
                                class="text-xs font-medium tracking-wider uppercase text-muted-foreground"
                            >
                                Total Users
                            </CardDescription>
                            <div
                                class="flex size-9 items-center justify-center rounded-lg border border-primary/20 bg-primary/10 text-primary"
                            >
                                <Users class="size-4" />
                            </div>
                        </div>
                        <CardTitle class="text-3xl font-bold tracking-tight">
                            {{ totalUsers }}
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="text-xs text-muted-foreground">
                        All registered accounts on the platform
                    </CardContent>
                </Card>

                <!-- 2. Vue Web Users (highlighted distinctly in Vue emerald) -->
                <Card
                    class="relative overflow-hidden border-emerald-500/30 bg-gradient-to-br from-card to-emerald-500/[0.04] shadow-xs transition-shadow hover:shadow-sm dark:border-emerald-500/20"
                >
                    <div
                        class="absolute top-0 right-0 h-1 w-full bg-gradient-to-r from-emerald-500 to-teal-400"
                    />
                    <CardHeader class="pb-2">
                        <div class="flex items-center justify-between">
                            <CardDescription
                                class="text-xs font-semibold tracking-wider uppercase text-emerald-600 dark:text-emerald-400"
                            >
                                Vue Web Users
                            </CardDescription>
                            <div
                                class="flex size-9 items-center justify-center rounded-lg border border-emerald-500/20 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400"
                            >
                                <Globe class="size-4" />
                            </div>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <CardTitle
                                class="text-3xl font-bold tracking-tight text-emerald-700 dark:text-emerald-300"
                            >
                                {{ clientUsage?.web ?? 0 }}
                            </CardTitle>
                            <span
                                v-if="totalUsers > 0"
                                class="text-xs font-medium text-emerald-600/80 dark:text-emerald-400/80"
                            >
                                ({{ Math.round(((clientUsage?.web ?? 0) / totalUsers) * 100) }}%)
                            </span>
                        </div>
                    </CardHeader>
                    <CardContent class="flex items-center justify-between text-xs text-muted-foreground">
                        <span>Active on Vue 3 Web Client</span>
                        <Badge
                            variant="outline"
                            class="border-emerald-500/30 bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 text-[10px] font-medium"
                        >
                            vue-web
                        </Badge>
                    </CardContent>
                </Card>

                <!-- 3. Flutter Mobile Users (highlighted distinctly in Flutter sky blue) -->
                <Card
                    class="relative overflow-hidden border-sky-500/30 bg-gradient-to-br from-card to-sky-500/[0.04] shadow-xs transition-shadow hover:shadow-sm dark:border-sky-500/20"
                >
                    <div
                        class="absolute top-0 right-0 h-1 w-full bg-gradient-to-r from-sky-500 to-blue-500"
                    />
                    <CardHeader class="pb-2">
                        <div class="flex items-center justify-between">
                            <CardDescription
                                class="text-xs font-semibold tracking-wider uppercase text-sky-600 dark:text-sky-400"
                            >
                                Flutter Mobile Users
                            </CardDescription>
                            <div
                                class="flex size-9 items-center justify-center rounded-lg border border-sky-500/20 bg-sky-500/10 text-sky-600 dark:text-sky-400"
                            >
                                <Smartphone class="size-4" />
                            </div>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <CardTitle
                                class="text-3xl font-bold tracking-tight text-sky-700 dark:text-sky-300"
                            >
                                {{ clientUsage?.mobile ?? 0 }}
                            </CardTitle>
                            <span
                                v-if="totalUsers > 0"
                                class="text-xs font-medium text-sky-600/80 dark:text-sky-400/80"
                            >
                                ({{ Math.round(((clientUsage?.mobile ?? 0) / totalUsers) * 100) }}%)
                            </span>
                        </div>
                    </CardHeader>
                    <CardContent class="flex items-center justify-between text-xs text-muted-foreground">
                        <span>Active on Flutter Mobile App</span>
                        <Badge
                            variant="outline"
                            class="border-sky-500/30 bg-sky-500/10 text-sky-700 dark:text-sky-300 text-[10px] font-medium"
                        >
                            flutter-mobile
                        </Badge>
                    </CardContent>
                </Card>
            </div>

            <!-- User Status & Activity Table Card -->
            <Card class="border-border/80 shadow-xs">
                <CardHeader class="gap-4 pb-4 border-b border-border/60">
                    <div
                        class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div>
                            <CardTitle class="text-lg font-semibold">
                                User Status & Activity
                            </CardTitle>
                            <CardDescription class="text-xs text-muted-foreground">
                                Detailed directory of accounts, roles, statuses, and primary client usage.
                            </CardDescription>
                        </div>
                        <div class="text-xs text-muted-foreground font-medium">
                            Showing {{ filteredUsers.length }} of {{ users.length }} users
                        </div>
                    </div>

                    <!-- Search and Filter Bar -->
                    <div
                        class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div class="relative w-full sm:max-w-xs">
                            <Search
                                class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground"
                            />
                            <Input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Search by name or email..."
                                class="pl-9 h-9 text-sm"
                            />
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <!-- Role Filter Buttons -->
                            <div class="flex items-center rounded-lg border border-border/80 p-0.5 bg-muted/40 text-xs">
                                <button
                                    type="button"
                                    :class="[
                                        'px-2.5 py-1 rounded-md font-medium transition-colors',
                                        roleFilter === 'all'
                                            ? 'bg-background text-foreground shadow-xs'
                                            : 'text-muted-foreground hover:text-foreground',
                                    ]"
                                    @click="roleFilter = 'all'"
                                >
                                    All Roles
                                </button>
                                <button
                                    type="button"
                                    :class="[
                                        'px-2.5 py-1 rounded-md font-medium transition-colors',
                                        roleFilter === 'admin'
                                            ? 'bg-background text-foreground shadow-xs'
                                            : 'text-muted-foreground hover:text-foreground',
                                    ]"
                                    @click="roleFilter = 'admin'"
                                >
                                    Admins
                                </button>
                                <button
                                    type="button"
                                    :class="[
                                        'px-2.5 py-1 rounded-md font-medium transition-colors',
                                        roleFilter === 'user'
                                            ? 'bg-background text-foreground shadow-xs'
                                            : 'text-muted-foreground hover:text-foreground',
                                    ]"
                                    @click="roleFilter = 'user'"
                                >
                                    Users
                                </button>
                            </div>

                            <!-- Client Filter Buttons -->
                            <div class="flex items-center rounded-lg border border-border/80 p-0.5 bg-muted/40 text-xs">
                                <button
                                    type="button"
                                    :class="[
                                        'px-2.5 py-1 rounded-md font-medium transition-colors',
                                        clientFilter === 'all'
                                            ? 'bg-background text-foreground shadow-xs'
                                            : 'text-muted-foreground hover:text-foreground',
                                    ]"
                                    @click="clientFilter = 'all'"
                                >
                                    All Clients
                                </button>
                                <button
                                    type="button"
                                    :class="[
                                        'px-2.5 py-1 rounded-md font-medium transition-colors',
                                        clientFilter === 'vue-web'
                                            ? 'bg-background text-emerald-700 dark:text-emerald-300 shadow-xs'
                                            : 'text-muted-foreground hover:text-foreground',
                                    ]"
                                    @click="clientFilter = 'vue-web'"
                                >
                                    Vue Web
                                </button>
                                <button
                                    type="button"
                                    :class="[
                                        'px-2.5 py-1 rounded-md font-medium transition-colors',
                                        clientFilter === 'flutter-mobile'
                                            ? 'bg-background text-sky-700 dark:text-sky-300 shadow-xs'
                                            : 'text-muted-foreground hover:text-foreground',
                                    ]"
                                    @click="clientFilter = 'flutter-mobile'"
                                >
                                    Flutter
                                </button>
                            </div>

                            <Button
                                v-if="searchQuery || roleFilter !== 'all' || statusFilter !== 'all' || clientFilter !== 'all'"
                                variant="ghost"
                                size="sm"
                                class="h-8 px-2 text-xs text-muted-foreground hover:text-foreground"
                                @click="clearFilters"
                            >
                                Reset
                            </Button>
                        </div>
                    </div>
                </CardHeader>

                <!-- Users Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-border/60 bg-muted/30 text-xs uppercase tracking-wider text-muted-foreground">
                            <tr>
                                <th scope="col" class="py-3.5 pl-6 pr-4 font-semibold">
                                    User
                                </th>
                                <th scope="col" class="px-4 py-3.5 font-semibold">
                                    Role
                                </th>
                                <th scope="col" class="px-4 py-3.5 font-semibold">
                                    Status
                                </th>
                                <th scope="col" class="px-4 py-3.5 font-semibold">
                                    Primary Client
                                </th>
                                <th scope="col" class="py-3.5 pl-4 pr-6 font-semibold">
                                    Last Login
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/60">
                            <tr
                                v-for="user in filteredUsers"
                                :key="user.id"
                                class="transition-colors hover:bg-muted/40"
                            >
                                <!-- User Name and Email -->
                                <td class="py-4 pl-6 pr-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex size-9 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary font-semibold text-xs border border-primary/20"
                                        >
                                            {{ user.name ? user.name.charAt(0).toUpperCase() : 'U' }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-medium text-foreground truncate">
                                                {{ user.name }}
                                            </div>
                                            <div class="text-xs text-muted-foreground truncate">
                                                {{ user.email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Role badge (admin vs user) -->
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <Badge
                                        v-if="user.role === 'admin'"
                                        variant="default"
                                        class="font-semibold gap-1 bg-primary text-primary-foreground shadow-xs"
                                    >
                                        <Shield class="size-3 shrink-0" />
                                        <span>admin</span>
                                    </Badge>
                                    <Badge
                                        v-else
                                        variant="secondary"
                                        class="font-normal"
                                    >
                                        user
                                    </Badge>
                                </td>

                                <!-- Status badge (active vs suspended) -->
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <Badge
                                        v-if="user.status === 'active'"
                                        class="border border-emerald-500/20 bg-emerald-500/15 text-emerald-700 dark:text-emerald-400 font-medium gap-1.5"
                                    >
                                        <span class="size-1.5 rounded-full bg-emerald-500 shrink-0" />
                                        <span>active</span>
                                    </Badge>
                                    <Badge
                                        v-else-if="user.status === 'suspended'"
                                        variant="destructive"
                                        class="font-medium gap-1.5"
                                    >
                                        <span class="size-1.5 rounded-full bg-white shrink-0" />
                                        <span>suspended</span>
                                    </Badge>
                                    <Badge
                                        v-else
                                        variant="outline"
                                        class="capitalize"
                                    >
                                        {{ user.status }}
                                    </Badge>
                                </td>

                                <!-- Primary Client used (vue-web, flutter-mobile, or 'Never logged in') -->
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <Badge
                                        v-if="user.last_client_used === 'vue-web'"
                                        variant="outline"
                                        class="border-emerald-500/30 bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 font-medium gap-1.5"
                                    >
                                        <Globe class="size-3 text-emerald-600 dark:text-emerald-400 shrink-0" />
                                        <span>vue-web</span>
                                    </Badge>
                                    <Badge
                                        v-else-if="user.last_client_used === 'flutter-mobile'"
                                        variant="outline"
                                        class="border-sky-500/30 bg-sky-500/10 text-sky-700 dark:text-sky-300 font-medium gap-1.5"
                                    >
                                        <Smartphone class="size-3 text-sky-600 dark:text-sky-400 shrink-0" />
                                        <span>flutter-mobile</span>
                                    </Badge>
                                    <span
                                        v-else
                                        class="text-xs text-muted-foreground italic font-normal"
                                    >
                                        Never logged in
                                    </span>
                                </td>

                                <!-- Formatted Last Login timestamp -->
                                <td class="py-4 pl-4 pr-6 whitespace-nowrap">
                                    <div
                                        v-if="user.last_login_at"
                                        class="flex items-center gap-1.5 text-xs text-foreground font-medium"
                                    >
                                        <Clock class="size-3.5 text-muted-foreground shrink-0" />
                                        <span>{{ formatDateTime(user.last_login_at) }}</span>
                                    </div>
                                    <span
                                        v-else
                                        class="text-xs text-muted-foreground italic"
                                    >
                                        Never
                                    </span>
                                </td>
                            </tr>

                            <!-- Empty state if no users match filters -->
                            <tr v-if="filteredUsers.length === 0">
                                <td
                                    colspan="5"
                                    class="py-12 text-center text-muted-foreground"
                                >
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <Users class="size-8 text-muted-foreground/50" />
                                        <p class="text-sm font-medium">No users found</p>
                                        <p class="text-xs">
                                            {{
                                                searchQuery || roleFilter !== 'all' || statusFilter !== 'all' || clientFilter !== 'all'
                                                    ? 'Try adjusting your search query or filters.'
                                                    : 'No users have registered yet.'
                                            }}
                                        </p>
                                        <Button
                                            v-if="searchQuery || roleFilter !== 'all' || statusFilter !== 'all' || clientFilter !== 'all'"
                                            variant="outline"
                                            size="sm"
                                            class="mt-2 text-xs"
                                            @click="clearFilters"
                                        >
                                            Reset Filters
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </Card>
        </main>
    </AuthenticatedLayout>
</template>
