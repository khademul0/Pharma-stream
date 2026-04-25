<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { Pill, LayoutDashboard, ShoppingCart, Package, Truck, Users, BarChart3, Upload, Undo2, Bell, LogOut } from 'lucide-vue-next';

defineProps({
    title: { type: String, default: '' },
});

const page = usePage();
const nav = [
    { href: '/dashboard', label: 'Dashboard', icon: LayoutDashboard },
    { href: '/pos', label: 'POS', icon: ShoppingCart },
    { href: '/sales', label: 'Sales History', icon: Package },
    { href: '/medicines', label: 'Medicines', icon: Pill },
    { href: '/batches', label: 'Batches', icon: Package },
    { href: '/suppliers', label: 'Suppliers', icon: Truck },
    { href: '/customers', label: 'Customers', icon: Users },
    { href: '/reports/z', label: 'Z-Report', icon: BarChart3 },
    { href: '/inventory/import', label: 'Import', icon: Upload },
    { href: '/returns', label: 'Returns', icon: Undo2 },
    { href: '/refills', label: 'Refills', icon: Bell },
];
</script>

<template>
    <div class="min-h-screen bg-slate-50 flex">
        <aside class="w-56 bg-secondary text-slate-100 flex flex-col shrink-0">
            <div class="p-4 border-b border-slate-700 flex items-center gap-2">
                <Pill class="w-6 h-6 text-emerald-400" />
                <div>
                    <div class="font-semibold text-sm">PharmaStream</div>
                    <div v-if="page.props.tenant" class="text-xs text-slate-400 truncate">
                        {{ page.props.tenant.name }}
                    </div>
                </div>
            </div>
            <nav class="flex-1 p-2 space-y-1">
                <Link
                    v-for="item in nav"
                    :key="item.href"
                    :href="item.href"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm hover:bg-slate-700 transition"
                    :class="{ 'bg-emerald-600 text-white': page.url.startsWith(item.href) }"
                >
                    <component :is="item.icon" class="w-4 h-4" />
                    {{ item.label }}
                </Link>
                <div class="pt-4 border-t border-slate-700 mt-4 space-y-1">
                    <Link href="/reports/profit-loss" class="block px-3 py-2 text-sm rounded-lg hover:bg-slate-700">Profit / Loss</Link>
                    <Link href="/reports/tax" class="block px-3 py-2 text-sm rounded-lg hover:bg-slate-700">Tax report</Link>
                </div>
            </nav>
            <div class="p-3 border-t border-slate-700">
                <Link
                    href="/logout"
                    method="post"
                    as="button"
                    class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-sm"
                >
                    <LogOut class="w-4 h-4" />
                    Log out
                </Link>
            </div>
        </aside>
        <main class="flex-1 overflow-auto">
            <header class="bg-white border-b border-slate-200 px-8 py-4">
                <h1 class="text-xl font-semibold text-secondary">
                    {{ title }}
                </h1>
            </header>
            <div class="p-8">
                <slot />
            </div>
        </main>
    </div>
</template>
