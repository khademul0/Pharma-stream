<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({ suppliers: Object });

const form = useForm({
    name: '',
    contact_person: '',
    phone: '',
});

function add() {
    form.post('/suppliers', { preserveScroll: true, onSuccess: () => form.reset() });
}
</script>

<template>
    <AppLayout title="Suppliers">
        <form class="grid md:grid-cols-4 gap-3 mb-8 bg-white p-4 rounded-xl border border-slate-200" @submit.prevent="add">
            <input v-model="form.name" placeholder="Name" class="border rounded-lg px-3 py-2 text-sm" required />
            <input v-model="form.contact_person" placeholder="Contact" class="border rounded-lg px-3 py-2 text-sm" />
            <input v-model="form.phone" placeholder="Phone" class="border rounded-lg px-3 py-2 text-sm" />
            <button type="submit" class="rounded-lg bg-emerald-600 text-white text-sm font-medium" :disabled="form.processing">Add</button>
        </form>
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-600">
                    <tr>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Contact</th>
                        <th class="px-4 py-3">Phone</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="s in suppliers.data" :key="s.id" class="border-t border-slate-100">
                        <td class="px-4 py-3 font-medium">{{ s.name }}</td>
                        <td class="px-4 py-3">{{ s.contact_person }}</td>
                        <td class="px-4 py-3">{{ s.phone }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
