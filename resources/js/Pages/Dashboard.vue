<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { AlertTriangle, TrendingUp, Bell } from 'lucide-vue-next';

defineProps({
    expiringBatches: Array,
    lowStockMedicines: Array,
    upcomingRefills: Array,
    todaySales: Number,
});
</script>

<template>
    <AppLayout title="Dashboard">
        <div class="grid md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
                <div class="text-sm text-slate-500">Today sales</div>
                <div class="text-3xl font-semibold text-emerald-600 mt-1 flex items-center gap-2">
                    <TrendingUp class="w-6 h-6" />
                    ${{ todaySales.toFixed(2) }}
                </div>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-6">
            <section class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
                <h2 class="font-semibold text-secondary flex items-center gap-2 mb-4">
                    <AlertTriangle class="w-5 h-5 text-amber-500" />
                    Expiry (90d)
                </h2>
                <ul class="space-y-2 text-sm">
                    <li v-for="b in expiringBatches" :key="b.id" class="flex justify-between gap-2 border-b border-slate-100 pb-2">
                        <span class="truncate">{{ b.medicine }}</span>
                        <span class="text-slate-500 whitespace-nowrap">{{ b.expiry_date }}</span>
                    </li>
                    <li v-if="!expiringBatches.length" class="text-slate-500">No batches in window.</li>
                </ul>
            </section>

            <section class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
                <h2 class="font-semibold text-secondary mb-4">Low stock</h2>
                <ul class="space-y-2 text-sm">
                    <li v-for="m in lowStockMedicines" :key="m.id" class="flex justify-between gap-2 border-b border-slate-100 pb-2">
                        <span>{{ m.name }}</span>
                        <span class="text-amber-600">{{ m.stock }} / {{ m.threshold }}</span>
                    </li>
                    <li v-if="!lowStockMedicines.length" class="text-slate-500">All clear.</li>
                </ul>
            </section>

            <section class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
                <h2 class="font-semibold text-secondary flex items-center gap-2 mb-4">
                    <Bell class="w-5 h-5 text-emerald-600" />
                    Refills (7d)
                </h2>
                <ul class="space-y-2 text-sm">
                    <li v-for="r in upcomingRefills" :key="r.id" class="border-b border-slate-100 pb-2">
                        <div class="font-medium">{{ r.customer }}</div>
                        <div class="text-slate-500">{{ r.medicine }} · {{ r.remind_at }}</div>
                    </li>
                    <li v-if="!upcomingRefills.length" class="text-slate-500">No upcoming refills.</li>
                </ul>
            </section>
        </div>
    </AppLayout>
</template>
