<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({ categories: Array });

const form = useForm({
    name: '',
    generic_name: '',
    category_id: props.categories[0]?.id ?? '',
    barcode: '',
    unit_type: 'tablet',
    conversion_rate: 1,
    units_per_strip: 10,
    strips_per_box: 1,
    low_stock_threshold: null,
    notes: '',
});

function submit() {
    form.post('/medicines');
}
</script>

<template>
    <AppLayout title="New medicine">
        <form class="max-w-xl space-y-4 bg-white p-6 rounded-xl border border-slate-200" @submit.prevent="submit">
            <div>
                <label class="text-sm font-medium">Name</label>
                <input v-model="form.name" class="mt-1 w-full border rounded-lg px-3 py-2 text-sm" required />
            </div>
            <div>
                <label class="text-sm font-medium">Generic name</label>
                <input v-model="form.generic_name" class="mt-1 w-full border rounded-lg px-3 py-2 text-sm" />
            </div>
            <div>
                <label class="text-sm font-medium">Category</label>
                <select v-model="form.category_id" class="mt-1 w-full border rounded-lg px-3 py-2 text-sm" required>
                    <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                </select>
            </div>
            <div>
                <label class="text-sm font-medium">Barcode</label>
                <input v-model="form.barcode" class="mt-1 w-full border rounded-lg px-3 py-2 text-sm" />
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium">Unit type</label>
                    <select v-model="form.unit_type" class="mt-1 w-full border rounded-lg px-3 py-2 text-sm">
                        <option value="tablet">Tablet</option>
                        <option value="strip">Strip</option>
                        <option value="box">Box</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium">Conversion rate</label>
                    <input v-model.number="form.conversion_rate" type="number" step="0.0001" min="0.0001" class="mt-1 w-full border rounded-lg px-3 py-2 text-sm" />
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium">Units / strip</label>
                    <input v-model.number="form.units_per_strip" type="number" min="1" class="mt-1 w-full border rounded-lg px-3 py-2 text-sm" />
                </div>
                <div>
                    <label class="text-sm font-medium">Strips / box</label>
                    <input v-model.number="form.strips_per_box" type="number" min="1" class="mt-1 w-full border rounded-lg px-3 py-2 text-sm" />
                </div>
            </div>
            <div>
                <label class="text-sm font-medium">Low stock threshold</label>
                <input v-model.number="form.low_stock_threshold" type="number" min="0" class="mt-1 w-full border rounded-lg px-3 py-2 text-sm" />
            </div>
            <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-medium" :disabled="form.processing">Save</button>
        </form>
    </AppLayout>
</template>
