<script setup>
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({ reminders: Object });

function ack(id) {
    router.post(`/refills/${id}/ack`, {}, { preserveScroll: true });
}
</script>

<template>
    <AppLayout title="Refill reminders">
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-600">
                    <tr>
                        <th class="px-4 py-3">Customer</th>
                        <th class="px-4 py-3">Medicine</th>
                        <th class="px-4 py-3">Remind</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="r in reminders.data" :key="r.id" class="border-t border-slate-100">
                        <td class="px-4 py-3">{{ r.customer?.name }}</td>
                        <td class="px-4 py-3">{{ r.medicine?.name }}</td>
                        <td class="px-4 py-3">{{ r.remind_at }}</td>
                        <td class="px-4 py-3">
                            <button
                                v-if="!r.acknowledged_at"
                                type="button"
                                class="text-emerald-700 text-sm"
                                @click="ack(r.id)"
                            >
                                Acknowledge
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
