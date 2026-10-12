<script setup>
import { Head, useForm } from "@inertiajs/vue3";

defineProps({
    tasks: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    title: "",
    description: "",
    due_at: "",
});

function submit() {
    form.post("/tasks", {
        onSuccess: () => form.reset(),
    });
}
</script>

<template>
    <Head title="My Tasks" />

    <main class="mx-auto max-w-4xl space-y-8 p-6">
        <h1 class="text-3xl font-bold">My Tasks</h1>

        <section class="rounded-lg border p-6">
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

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded-md bg-green-600 px-5 py-2 font-medium text-white disabled:opacity-50"
                >
                    {{ form.processing ? "Saving..." : "Create Task" }}
                </button>
            </form>
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
                    <h3 class="text-lg font-semibold">{{ task.title }}</h3>

                    <p v-if="task.description" class="mt-2">
                        {{ task.description }}
                    </p>

                    <p class="mt-2 text-sm text-gray-500">
                        Due: {{ task.due_at }}
                    </p>
                </article>
            </div>
        </section>
    </main>
</template>
