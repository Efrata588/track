<script setup>
import { Head, useForm, router } from "@inertiajs/vue3";
import { ref } from "vue";

defineProps({
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

const editForm = useForm({
    title: "",
    description: "",
    due_at: "",
});

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
    router.patch(`/tasks/${task.id}/completion`, {}, { preserveScroll: true });
}

function destroy(task) {
    router.delete(`/tasks/${task.id}`, {}, { preserveScroll: true });
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
                @click="isCreateModalOpen = true"
                class="rounded-lg bg-blue-600 px-4 py-2 font-medium text-white hover:bg-blue-700"
            >
                + Add Task
            </button>
        </div>

        <section v-if="isCreateModalOpen" class="rounded-lg border p-6">
            <div
                class="w-full max-w-lg rounded-xl bg-white p-6 text-gray-900 shadow-xl"
                role="dialog"
                aria-modal="true"
                aria-labelledby="create-task-title"
            >
                <h2 class="mb-4 text-xl font-semibold">Create a Task</h2>

                <form @submit.prevent="submit" class="space-y-4">
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
                            @click="isCreateModalOpen = false"
                            class="rounded-md border border-gray-300 px-4 py-2 font-medium text-gray-700 hover:bg-gray-100"
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

        <section>
            <h2 class="mb-4 text-xl font-semibold">My Tasks</h2>

            <div v-if="tasks.length === 0" class="rounded-lg border p-6">
                You haven't created any tasks yet.
            </div>

            <div v-else class="space-y-4">
                <article
                    v-for="task in tasks"
                    :key="task.id"
                    class="rounded-lg border p-4"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0 flex-1">
                            <h3
                                class="text-lg font-semibold"
                                :class="{
                                    'line-through text-gray-500':
                                        task.completed_at,
                                }"
                            >
                                {{ task.title }}
                            </h3>

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
                                        : new Date(task.due_at) < new Date()
                                          ? 'text-red-600'
                                          : 'text-amber-600'
                                "
                            >
                                {{
                                    task.completed_at
                                        ? "Completed"
                                        : new Date(task.due_at) < new Date()
                                          ? "Overdue"
                                          : "Pending"
                                }}
                            </p>
                        </div>

                        <button
                            type="button"
                            @click="destroy(task)"
                            class="shrink-0 rounded-md border px-3 py-2 text-sm font-medium"
                        >
                            x
                        </button>
                    </div>

                    <div class="flex items-start justify-between mt-5 mb-3">
                        <button
                            type="button"
                            @click="toggleCompletion(task)"
                            class="shrink-0 rounded-md border px-3 py-2 text-sm font-medium"
                        >
                            {{ task.completed_at ? "Undo" : "Complete" }}
                        </button>

                        <button
                            type="button"
                            @click="startEditing(task)"
                            class="shrink-0 rounded-md border px-3 py-2 text-sm font-medium"
                        >
                            Edit
                        </button>
                    </div>
                </article>
            </div>
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
                                @click="closeEditModal"
                                class="rounded-md border border-gray-300 bg-white px-4 py-2 font-medium text-gray-700 hover:bg-gray-100"
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
