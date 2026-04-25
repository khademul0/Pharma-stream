<template>
    <div class="min-h-screen flex items-center justify-center bg-slate-100 px-4 py-12">
        <div class="w-full max-w-lg bg-white rounded-2xl shadow-lg border border-slate-200 p-8">
            <h1 class="text-xl font-semibold text-secondary mb-1">Create pharmacy</h1>
            <p class="text-sm text-slate-500 mb-6">Start your PharmaStream workspace (14-day trial).</p>
            <form class="space-y-4" @submit.prevent="submit">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Pharmacy name</label>
                    <input v-model="form.pharmacy_name" type="text" class="mt-1 w-full rounded-lg border px-3 py-2 text-sm" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">URL slug</label>
                    <input v-model="form.slug" type="text" class="mt-1 w-full rounded-lg border px-3 py-2 text-sm" required />
                    <p v-if="form.errors.slug" class="text-sm text-red-600 mt-1">{{ form.errors.slug }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Your name</label>
                    <input v-model="form.name" type="text" class="mt-1 w-full rounded-lg border px-3 py-2 text-sm" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Email</label>
                    <input v-model="form.email" type="email" class="mt-1 w-full rounded-lg border px-3 py-2 text-sm" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Password</label>
                    <input v-model="form.password" type="password" class="mt-1 w-full rounded-lg border px-3 py-2 text-sm" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Confirm password</label>
                    <input v-model="form.password_confirmation" type="password" class="mt-1 w-full rounded-lg border px-3 py-2 text-sm" required />
                </div>
                <button
                    type="submit"
                    class="w-full py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-sm"
                    :disabled="form.processing"
                >
                    Create pharmacy
                </button>
            </form>
            <p class="mt-6 text-center text-sm text-slate-600">
                Already have access?
                <a href="/login" class="text-emerald-700 font-medium">Sign in</a>
            </p>
        </div>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    pharmacy_name: '',
    slug: '',
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

function submit() {
    form.post('/register');
}
</script>
