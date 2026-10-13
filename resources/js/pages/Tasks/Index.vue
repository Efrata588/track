<script setup>
import { Head, useForm, router } from "@inertiajs/vue3";
import { ref, computed, onMounted, onUnmounted } from "vue";

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
    title: "",
    description: "",
    due_at: "",
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
    title: "",
    description: "",
    due_at: "",
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
        title: "Today",
        tasks: todaysTask.value,
    },
    {
        title: "Upcoming",
        tasks: upcomingTask.value,
    },
    {
        title: "Past Due",
        tasks: pastDueTasks.value,
    },
    {
        title: "Completed",
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
    editForm.description = task.description ?? "";
    editForm.due_at = task.due_at ? task.due_at.slice(0, 16) : "";
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
    form.post("/tasks", {
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
</script>

<template>
    <Head title="My Tasks" />

    <main class="mx-auto max-w-4xl space-y-8 p-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-4xl font-bold text-blue-500">
                    Welcome back, {{ username }}!
                </h1>
                <p class="mt-2 text-gray-500">
                    Here's an overview of your tasks.
                </p>
            </div>

            <button
                type="button"
                class="rounded-lg bg-blue-600 px-4 py-2 font-medium text-white hover:bg-blue-700"
                @click="isCreateModalOpen = true"
            >
                + Add Task
            </button>
        </div>

        <section
            v-if="isCreateModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/70 p-4"
            @click.self="closeCreateModal"
        >
            <div
                class="w-full max-w-lg rounded-xl bg-white p-6 text-gray-900 shadow-xl"
                role="dialog"
                aria-modal="true"
                aria-labelledby="create-task-title"
            >
                <h2 id="create-task-title" class="mb-4 text-xl font-semibold">
                    Create a Task
                </h2>

                <form class="space-y-4" @submit.prevent="submit">
                    <div>
                        <label for="title" class="mb-1 block font-medium">
                            Task title
                        </label>

                        <input
                            id="title"
                            v-model="form.title"
                            type="text"
                            required
                            maxlength="255"
                            class="w-full rounded-md border px-3 py-2"
                            placeholder="e.g. Learn Laravel models"
                        />

                        <p
                            v-if="form.errors.title"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.title }}
                        </p>
                    </div>

                    <div>
                        <label for="description" class="mb-1 block font-medium">
                            Description (optional)
                        </label>

                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="3"
                            class="w-full rounded-md border px-3 py-2"
                            placeholder="What needs to be done?"
                        ></textarea>

                        <p
                            v-if="form.errors.description"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.description }}
                        </p>
                    </div>

                    <div>
                        <label for="due_at" class="mb-1 block font-medium">
                            Due date and time
                        </label>

                        <input
                            id="due_at"
                            v-model="form.due_at"
                            type="datetime-local"
                            required
                            class="w-full rounded-md border px-3 py-2"
                        />

                        <p
                            v-if="form.errors.due_at"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.due_at }}
                        </p>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button
                            type="button"
                            class="rounded-md border border-gray-300 px-4 py-2 font-medium text-gray-700 hover:bg-gray-100"
                            @click="closeCreateModal"
                        >
                            Cancel
                        </button>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-md bg-blue-600 px-4 py-2 font-medium text-white hover:bg-blue-700 disabled:opacity-50"
                        >
                            {{ form.processing ? "Saving..." : "Create Task" }}
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <section class="space-y-8">
            <h2 class="text-xl font-semibold">My Tasks</h2>

            <div v-if="tasks.length === 0" class="rounded-lg border p-6">
                You haven't created any tasks yet.
            </div>

            <section
                v-for="category in taskCategories"
                :key="category.title"
                class="space-y-4"
            >
                <h3 class="text-lg font-semibold">
                    {{ category.title }}
                    <span class="text-sm font-normal text-gray-500">
                        ({{ category.tasks.length }})
                    </span>
                </h3>

                <p
                    v-if="category.tasks.length === 0"
                    class="rounded-lg border border-dashed p-4 text-sm text-gray-500"
                >
                    No tasks in this category.
                </p>

                <article
                    v-for="task in category.tasks"
                    :key="task.id"
                    class="rounded-lg border p-4"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0 flex-1">
                            <h4
                                class="text-lg font-semibold"
                                :class="{
                                    'line-through text-gray-500':
                                        task.completed_at,
                                }"
                            >
                                {{ task.title }}
                            </h4>

                            <p v-if="task.description" class="mt-2">
                                {{ task.description }}
                            </p>

                            <p class="mt-2 text-sm text-gray-500">
                                Due: {{ task.due_at }}
                            </p>

                            <p
                                class="mt-2 text-sm font-medium"
                                :class="
                                    task.completed_at
                                        ? 'text-green-600'
                                        : isPastDue(task)
                                          ? 'text-red-600'
                                          : 'text-amber-600'
                                "
                            >
                                {{
                                    task.completed_at
                                        ? "Completed"
                                        : isPastDue(task)
                                          ? "Overdue"
                                          : "Pending"
                                }}
                            </p>
                        </div>

                        <button
                            type="button"
                            class="shrink-0 rounded-md border px-3 py-2 text-sm font-medium"
                            @click="destroy(task)"
                        >
                            x
                        </button>
                    </div>

                    <div class="mt-5 mb-3 flex items-start justify-between">
                        <button
                            type="button"
                            class="shrink-0 rounded-md border px-3 py-2 text-sm font-medium"
                            @click="toggleCompletion(task)"
                        >
                            {{ task.completed_at ? "Undo" : "Complete" }}
                        </button>

                        <button
                            type="button"
                            class="shrink-0 rounded-md border px-3 py-2 text-sm font-medium"
                            @click="startEditing(task)"
                        >
                            Edit
                        </button>
                    </div>
                </article>
            </section>
        </section>

        <section>
            <div
                v-if="isEditModalOpen"
                class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/70 p-4"
                @click.self="closeEditModal"
            >
                <div
                    class="w-full max-w-lg rounded-xl border border-gray-200 bg-white p-6 text-gray-900 shadow-2xl"
                    role="dialog"
                    aria-modal="true"
                    aria-labelledby="edit-task-title"
                >
                    <h2
                        id="edit-task-title"
                        class="mb-6 text-xl font-bold text-gray-900"
                    >
                        Edit Task
                    </h2>

                    <form @submit.prevent="saveTask">
                        <div class="space-y-4">
                            <div>
                                <label
                                    for="edit-title"
                                    class="mb-1 block font-medium text-gray-800"
                                >
                                    Task title
                                </label>

                                <input
                                    id="edit-title"
                                    v-model="editForm.title"
                                    type="text"
                                    required
                                    maxlength="255"
                                    class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                                />

                                <p
                                    v-if="editForm.errors.title"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ editForm.errors.title }}
                                </p>
                            </div>

                            <div>
                                <label
                                    for="edit-description"
                                    class="mb-1 block font-medium text-gray-800"
                                >
                                    Description (optional)
                                </label>

                                <textarea
                                    id="edit-description"
                                    v-model="editForm.description"
                                    rows="3"
                                    class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                                ></textarea>

                                <p
                                    v-if="editForm.errors.description"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ editForm.errors.description }}
                                </p>
                            </div>

                            <div>
                                <label
                                    for="edit-due-at"
                                    class="mb-1 block font-medium text-gray-800"
                                >
                                    Due date and time
                                </label>

                                <input
                                    id="edit-due-at"
                                    v-model="editForm.due_at"
                                    type="datetime-local"
                                    required
                                    class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                                />

                                <p
                                    v-if="editForm.errors.due_at"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ editForm.errors.due_at }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <button
                                type="button"
                                class="rounded-md border border-gray-300 bg-white px-4 py-2 font-medium text-gray-700 hover:bg-gray-100"
                                @click="closeEditModal"
                            >
                                Cancel
                            </button>

                            <button
                                type="submit"
                                :disabled="editForm.processing"
                                class="rounded-md bg-blue-600 px-4 py-2 font-semibold text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                {{
                                    editForm.processing
                                        ? "Saving..."
                                        : "Save Changes"
                                }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>
</template>
