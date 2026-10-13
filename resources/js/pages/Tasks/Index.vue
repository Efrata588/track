<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import {
    AlertCircle,
    Calendar,
    CalendarDays,
    CheckCircle2,
    Circle,
    Clock,
    Pencil,
    Plus,
    Trash2,
} from '@lucide/vue';
import { computed, onMounted, onUnmounted, ref } from 'vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { dashboard } from '@/routes';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});

const props = defineProps({
    tasks: {
        type: Array,
        required: true,
    },
    username: {
        type: String,
        required: true,
    },
});

const form = useForm({
    title: '',
    description: '',
    due_at: '',
});

const isCreateModalOpen = ref(false);
const isEditModalOpen = ref(false);
const selectedTask = ref(null);

const now = ref(new Date());
let clockInterval = null;

onMounted(() => {
    clockInterval = setInterval(() => {
        now.value = new Date();
    }, 30000);
});

onUnmounted(() => {
    clearInterval(clockInterval);
});

function isSameDay(dateA, dateB) {
    return (
        dateA.getFullYear() === dateB.getFullYear() &&
        dateA.getMonth() === dateB.getMonth() &&
        dateA.getDate() === dateB.getDate()
    );
}

function isPastDue(task) {
    return new Date(task.due_at) < now.value;
}

const editForm = useForm({
    title: '',
    description: '',
    due_at: '',
});

const todaysTask = computed(() =>
    props.tasks.filter(
        (task) =>
            !task.completed_at &&
            isSameDay(new Date(task.due_at), now.value) &&
            !isPastDue(task),
    ),
);

const upcomingTask = computed(() =>
    props.tasks.filter(
        (task) =>
            !task.completed_at &&
            !isPastDue(task) &&
            !isSameDay(new Date(task.due_at), now.value),
    ),
);

const pastDueTasks = computed(() =>
    props.tasks.filter((task) => !task.completed_at && isPastDue(task)),
);

const completed = computed(() =>
    props.tasks.filter((task) => task.completed_at),
);

const taskCategories = computed(() => [
    {
        title: 'Today',
        tasks: todaysTask.value,
    },
    {
        title: 'Upcoming',
        tasks: upcomingTask.value,
    },
    {
        title: 'Past Due',
        tasks: pastDueTasks.value,
    },
    {
        title: 'Completed',
        tasks: completed.value,
    },
]);

function closeCreateModal() {
    isCreateModalOpen.value = false;
    form.reset();
    form.clearErrors();
}

function startEditing(task) {
    selectedTask.value = task;
    editForm.title = task.title;
    editForm.description = task.description ?? '';
    editForm.due_at = task.due_at ? task.due_at.slice(0, 16) : '';
    isEditModalOpen.value = true;
}

function closeEditModal() {
    isEditModalOpen.value = false;
    selectedTask.value = null;
    editForm.reset();
    editForm.clearErrors();
}

function saveTask() {
    if (!selectedTask.value) return;

    editForm.patch(`/tasks/${selectedTask.value.id}`, {
        preserveScroll: true,
        onSuccess: () => closeEditModal(),
    });
}

function submit() {
    form.post('/tasks', {
        onSuccess: () => closeCreateModal(),
    });
}

function toggleCompletion(task) {
    router.patch(
        `/tasks/${task.id}/completion`,
        {},
        {
            preserveScroll: true,
        },
    );
}

function destroy(task) {
    router.delete(`/tasks/${task.id}`, {
        preserveScroll: true,
    });
}

// Presentation helper for formatted date display
const formattedDate = computed(() =>
    new Intl.DateTimeFormat(undefined, {
        weekday: 'long',
        month: 'short',
        day: 'numeric',
    }).format(now.value),
);

function formatDueDate(dueStr) {
    if (!dueStr) return '';
    try {
        const d = new Date(dueStr);
        return new Intl.DateTimeFormat(undefined, {
            month: 'short',
            day: 'numeric',
            year: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
        }).format(d);
    } catch {
        return dueStr;
    }
}
</script>

<template>
    <Head title="Dashboard" />

    <main class="mx-auto max-w-7xl space-y-8 p-4 sm:p-6 lg:p-8">
        <!-- Dashboard Header -->
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div class="space-y-1">
                <h1
                    class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl"
                >
                    Welcome back, {{ username }}!
                </h1>
                <p
                    class="flex items-center gap-1.5 text-sm text-muted-foreground"
                >
                    <Calendar class="size-4 text-primary" />
                    <span>{{ formattedDate }}</span>
                    <span>&bull;</span>
                    <span>Here's an overview of your tasks.</span>
                </p>
            </div>

            <Button
                type="button"
                class="gap-1.5 self-start font-medium sm:self-auto"
                @click="isCreateModalOpen = true"
            >
                <Plus class="size-4" />
                Add Task
            </Button>
        </div>

        <!-- Metric Summary Cards -->
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <Card class="border-border/80 shadow-xs">
                <CardHeader class="pb-2">
                    <div class="flex items-center justify-between">
                        <CardDescription
                            class="text-xs font-medium tracking-wider uppercase"
                        >
                            Today
                        </CardDescription>
                        <CalendarDays class="size-4 text-primary" />
                    </div>
                    <CardTitle class="text-2xl font-bold">
                        {{ todaysTask.length }}
                    </CardTitle>
                </CardHeader>
                <CardContent class="text-xs text-muted-foreground">
                    Due by end of today
                </CardContent>
            </Card>

            <Card class="border-border/80 shadow-xs">
                <CardHeader class="pb-2">
                    <div class="flex items-center justify-between">
                        <CardDescription
                            class="text-xs font-medium tracking-wider uppercase"
                        >
                            Upcoming
                        </CardDescription>
                        <Clock class="size-4 text-blue-500" />
                    </div>
                    <CardTitle class="text-2xl font-bold">
                        {{ upcomingTask.length }}
                    </CardTitle>
                </CardHeader>
                <CardContent class="text-xs text-muted-foreground">
                    Scheduled for future days
                </CardContent>
            </Card>

            <Card class="border-border/80 shadow-xs">
                <CardHeader class="pb-2">
                    <div class="flex items-center justify-between">
                        <CardDescription
                            class="text-xs font-medium tracking-wider uppercase"
                        >
                            Past Due
                        </CardDescription>
                        <AlertCircle class="size-4 text-destructive" />
                    </div>
                    <CardTitle
                        class="text-2xl font-bold"
                        :class="{ 'text-destructive': pastDueTasks.length > 0 }"
                    >
                        {{ pastDueTasks.length }}
                    </CardTitle>
                </CardHeader>
                <CardContent class="text-xs text-muted-foreground">
                    Requires immediate attention
                </CardContent>
            </Card>

            <Card class="border-border/80 shadow-xs">
                <CardHeader class="pb-2">
                    <div class="flex items-center justify-between">
                        <CardDescription
                            class="text-xs font-medium tracking-wider uppercase"
                        >
                            Completed
                        </CardDescription>
                        <CheckCircle2 class="size-4 text-emerald-500" />
                    </div>
                    <CardTitle
                        class="text-2xl font-bold text-emerald-600 dark:text-emerald-400"
                    >
                        {{ completed.length }}
                    </CardTitle>
                </CardHeader>
                <CardContent class="text-xs text-muted-foreground">
                    Accomplished so far
                </CardContent>
            </Card>
        </div>

        <!-- Create Task Dialog -->
        <Dialog
            :open="isCreateModalOpen"
            @update:open="
                (val) => {
                    if (!val) closeCreateModal();
                    else isCreateModalOpen = val;
                }
            "
        >
            <DialogContent class="sm:max-w-lg">
                <DialogHeader>
                    <DialogTitle>Create a Task</DialogTitle>
                    <DialogDescription>
                        Add a new task to your dashboard with a title, optional
                        description, and due date.
                    </DialogDescription>
                </DialogHeader>

                <form class="space-y-4" @submit.prevent="submit">
                    <div class="grid gap-2">
                        <Label for="title">Task title</Label>
                        <Input
                            id="title"
                            v-model="form.title"
                            type="text"
                            required
                            maxlength="255"
                            placeholder="e.g. Learn Laravel models"
                        />
                        <InputError :message="form.errors.title" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="description">Description (optional)</Label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="3"
                            class="w-full rounded-md border border-input bg-transparent px-3 py-2 text-base shadow-xs transition-[color,box-shadow] outline-none placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 disabled:cursor-not-allowed disabled:opacity-50 aria-invalid:border-destructive aria-invalid:ring-destructive/20 md:text-sm dark:bg-input/30 dark:aria-invalid:ring-destructive/40"
                            placeholder="What needs to be done?"
                        ></textarea>
                        <InputError :message="form.errors.description" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="due_at">Due date and time</Label>
                        <Input
                            id="due_at"
                            v-model="form.due_at"
                            type="datetime-local"
                            required
                        />
                        <InputError :message="form.errors.due_at" />
                    </div>

                    <DialogFooter class="gap-2 sm:gap-0">
                        <Button
                            type="button"
                            variant="outline"
                            @click="closeCreateModal"
                        >
                            Cancel
                        </Button>
                        <Button type="submit" :disabled="form.processing">
                            <Spinner v-if="form.processing" />
                            {{ form.processing ? 'Saving...' : 'Create Task' }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Edit Task Dialog -->
        <Dialog
            :open="isEditModalOpen"
            @update:open="
                (val) => {
                    if (!val) closeEditModal();
                    else isEditModalOpen = val;
                }
            "
        >
            <DialogContent class="sm:max-w-lg">
                <DialogHeader>
                    <DialogTitle>Edit Task</DialogTitle>
                    <DialogDescription>
                        Update the task details, description, or due date.
                    </DialogDescription>
                </DialogHeader>

                <form class="space-y-4" @submit.prevent="saveTask">
                    <div class="grid gap-2">
                        <Label for="edit-title">Task title</Label>
                        <Input
                            id="edit-title"
                            v-model="editForm.title"
                            type="text"
                            required
                            maxlength="255"
                        />
                        <InputError :message="editForm.errors.title" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="edit-description"
                            >Description (optional)</Label
                        >
                        <textarea
                            id="edit-description"
                            v-model="editForm.description"
                            rows="3"
                            class="w-full rounded-md border border-input bg-transparent px-3 py-2 text-base shadow-xs transition-[color,box-shadow] outline-none placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 disabled:cursor-not-allowed disabled:opacity-50 aria-invalid:border-destructive aria-invalid:ring-destructive/20 md:text-sm dark:bg-input/30 dark:aria-invalid:ring-destructive/40"
                        ></textarea>
                        <InputError :message="editForm.errors.description" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="edit-due-at">Due date and time</Label>
                        <Input
                            id="edit-due-at"
                            v-model="editForm.due_at"
                            type="datetime-local"
                            required
                        />
                        <InputError :message="editForm.errors.due_at" />
                    </div>

                    <DialogFooter class="gap-2 sm:gap-0">
                        <Button
                            type="button"
                            variant="outline"
                            @click="closeEditModal"
                        >
                            Cancel
                        </Button>
                        <Button type="submit" :disabled="editForm.processing">
                            <Spinner v-if="editForm.processing" />
                            {{
                                editForm.processing
                                    ? 'Saving...'
                                    : 'Save Changes'
                            }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Task Categories Sections -->
        <section class="space-y-8">
            <div class="flex items-center justify-between">
                <h2
                    class="text-xl font-semibold tracking-tight text-foreground"
                >
                    My Tasks
                </h2>
            </div>

            <!-- Global Empty State -->
            <div
                v-if="tasks.length === 0"
                class="flex flex-col items-center justify-center rounded-xl border border-dashed border-border bg-card p-12 text-center"
            >
                <div
                    class="mb-3 flex size-12 items-center justify-center rounded-full bg-primary/10 text-primary"
                >
                    <CalendarDays class="size-6" />
                </div>
                <p class="text-base font-medium text-foreground">
                    You haven't created any tasks yet.
                </p>
                <p class="mt-1 text-sm text-muted-foreground">
                    Click "Add Task" to get started organizing your day.
                </p>
                <Button
                    type="button"
                    class="mt-4 gap-1.5"
                    @click="isCreateModalOpen = true"
                >
                    <Plus class="size-4" />
                    Add Task
                </Button>
            </div>

            <!-- Categorized Task Lists -->
            <div v-else class="space-y-8">
                <section
                    v-for="category in taskCategories"
                    :key="category.title"
                    class="space-y-3"
                >
                    <div class="flex items-center gap-2">
                        <h3
                            class="text-lg font-semibold tracking-tight text-foreground"
                        >
                            {{ category.title }}
                        </h3>
                        <Badge variant="secondary" class="text-xs font-normal">
                            {{ category.tasks.length }}
                        </Badge>
                    </div>

                    <!-- Category Empty State -->
                    <div
                        v-if="category.tasks.length === 0"
                        class="rounded-lg border border-dashed border-border/80 bg-muted/30 px-4 py-6 text-center text-sm text-muted-foreground"
                    >
                        No tasks in this category.
                    </div>

                    <!-- Category Tasks Grid -->
                    <div
                        v-else
                        class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3"
                    >
                        <Card
                            v-for="task in category.tasks"
                            :key="task.id"
                            class="group relative flex flex-col justify-between border-border/80 transition-all hover:border-border hover:shadow-sm"
                            :class="{
                                'bg-muted/20 opacity-75': task.completed_at,
                            }"
                        >
                            <CardHeader class="p-4 pb-2">
                                <div
                                    class="flex items-start justify-between gap-3"
                                >
                                    <div class="min-w-0 flex-1 space-y-1">
                                        <CardTitle
                                            class="text-base leading-snug font-semibold break-words"
                                            :class="{
                                                'text-muted-foreground line-through':
                                                    task.completed_at,
                                            }"
                                        >
                                            {{ task.title }}
                                        </CardTitle>

                                        <p
                                            v-if="task.description"
                                            class="line-clamp-2 text-xs text-muted-foreground"
                                        >
                                            {{ task.description }}
                                        </p>
                                    </div>

                                    <!-- Delete Button -->
                                    <Button
                                        type="button"
                                        variant="ghost"
                                        size="icon"
                                        class="size-7 shrink-0 text-muted-foreground opacity-60 transition-opacity group-hover:opacity-100 hover:text-destructive"
                                        :aria-label="
                                            'Delete task ' + task.title
                                        "
                                        @click="destroy(task)"
                                    >
                                        <Trash2 class="size-3.5" />
                                        <span class="sr-only">x</span>
                                    </Button>
                                </div>
                            </CardHeader>

                            <CardContent class="p-4 pt-2">
                                <div
                                    class="flex items-center justify-between gap-2 pt-2 text-xs"
                                >
                                    <!-- Due Date -->
                                    <span
                                        class="flex items-center gap-1 text-muted-foreground"
                                    >
                                        <Clock class="size-3" />
                                        Due: {{ formatDueDate(task.due_at) }}
                                    </span>

                                    <!-- Status Badge -->
                                    <Badge
                                        v-if="task.completed_at"
                                        variant="outline"
                                        class="border-emerald-500/30 bg-emerald-500/10 font-medium text-emerald-700 dark:text-emerald-400"
                                    >
                                        Completed
                                    </Badge>
                                    <Badge
                                        v-else-if="isPastDue(task)"
                                        variant="destructive"
                                        class="font-medium"
                                    >
                                        Overdue
                                    </Badge>
                                    <Badge
                                        v-else
                                        variant="outline"
                                        class="border-amber-500/30 bg-amber-500/10 font-medium text-amber-700 dark:text-amber-400"
                                    >
                                        Pending
                                    </Badge>
                                </div>

                                <!-- Action Buttons -->
                                <div
                                    class="mt-4 flex items-center justify-between gap-2 border-t border-border/50 pt-3"
                                >
                                    <Button
                                        type="button"
                                        size="sm"
                                        variant="outline"
                                        class="h-8 gap-1.5 text-xs font-medium"
                                        :class="{
                                            'text-muted-foreground':
                                                task.completed_at,
                                            'hover:border-emerald-500/40 hover:text-emerald-600':
                                                !task.completed_at,
                                        }"
                                        :aria-label="
                                            task.completed_at
                                                ? 'Undo ' + task.title
                                                : 'Complete ' + task.title
                                        "
                                        @click="toggleCompletion(task)"
                                    >
                                        <CheckCircle2
                                            v-if="task.completed_at"
                                            class="size-3.5 text-emerald-500"
                                        />
                                        <Circle
                                            v-else
                                            class="size-3.5 text-muted-foreground"
                                        />
                                        {{
                                            task.completed_at
                                                ? 'Undo'
                                                : 'Complete'
                                        }}
                                    </Button>

                                    <Button
                                        type="button"
                                        size="sm"
                                        variant="ghost"
                                        class="h-8 gap-1.5 text-xs font-medium text-muted-foreground hover:text-foreground"
                                        :aria-label="'Edit ' + task.title"
                                        @click="startEditing(task)"
                                    >
                                        <Pencil class="size-3.5" />
                                        Edit
                                    </Button>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </section>
            </div>
        </section>
    </main>
</template>
