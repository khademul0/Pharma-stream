<template>
    <div class="min-h-screen flex items-center justify-center bg-slate-100 px-4">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-lg border border-slate-200 p-8">
            <div class="flex items-center gap-2 mb-6">
                <div class="w-10 h-10 rounded-xl bg-emerald-600 flex items-center justify-center text-white font-bold">P</div>
                <div>
                    <h1 class="text-lg font-semibold text-secondary">PharmaStream</h1>
                    <p class="text-sm text-slate-500">Sign in to your pharmacy</p>
                </div>
            </div>
            <form class="space-y-4" @submit.prevent="submit">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Pharmacy slug</label>
                    <input
                        v-model="form.tenant_slug"
                        type="text"
                        class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        required
                        autocomplete="organization"
                    />
                    <p v-if="form.errors.tenant_slug" class="text-sm text-red-600 mt-1">{{ form.errors.tenant_slug }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Email</label>
                    <input
                        v-model="form.email"
                        type="email"
                        class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        required
                    />
                    <p v-if="form.errors.email" class="text-sm text-red-600 mt-1">{{ form.errors.email }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Password</label>
                    <input
                        v-model="form.password"
                        type="password"
                        class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        required
                    />
                </div>
                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input v-model="form.remember" type="checkbox" />
                    Remember me
                </label>
                <button
                    type="submit"
                    class="w-full py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-sm disabled:opacity-50"
                    :disabled="form.processing"
                >
                    Sign in
                </button>
            </form>
            <p class="mt-6 text-center text-sm text-slate-600">
                No account?
                <a href="/register" class="text-emerald-700 font-medium">Register pharmacy</a>
            </p>
        </div>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    tenant_slug: '',
    email: '',
    password: '',
    remember: false,
});

function submit() {
    form.post('/login');
}
</script>
