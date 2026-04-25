<script setup>
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    medicines: Object,
});

function destroy(id) {
    if (confirm('Delete this medicine?')) {
        router.delete(`/medicines/${id}`);
    }
}
</script>

<template>
    <AppLayout title="Medicines">
        <div class="flex justify-between items-center mb-6">
            <p class="text-slate-600 text-sm">Manage catalog and low-stock thresholds.</p>
            <Link href="/medicines/create" class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-medium">Add medicine</Link>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-600">
                    <tr>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Generic</th>
                        <th class="px-4 py-3">Unit</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="m in medicines.data" :key="m.id" class="border-t border-slate-100">
                        <td class="px-4 py-3 font-medium">{{ m.name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ m.generic_name }}</td>
                        <td class="px-4 py-3">{{ m.unit_type }}</td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <Link :href="`/medicines/${m.id}/edit`" class="text-emerald-700 text-sm">Edit</Link>
                            <button type="button" class="text-red-600 text-sm" @click="destroy(m.id)">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-if="medicines.links?.length > 3" class="px-4 py-3 flex gap-2 border-t border-slate-100">
                <template v-for="l in medicines.links" :key="String(l.label)">
                    <Link
                        v-if="l.url"
                        :href="l.url"
                        class="px-2 py-1 rounded text-sm"
                        :class="{ 'bg-emerald-600 text-white': l.active }"
                    >
                        <span v-html="l.label" />
                    </Link>
                    <span
                        v-else
                        class="px-2 py-1 rounded text-sm text-slate-500"
                        v-html="l.label"
                    />
                </template>
            </div>
        </div>
    </AppLayout>
</template>
