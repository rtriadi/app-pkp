<script setup>
import { ref } from 'vue';
import { router, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const reportTypes = [
    {
        id: 'performance-agreement',
        title: 'Laporan Perjanjian Kinerja',
        description: 'Laporan perjanjian kinerja individu pegawai dengan detail sasaran kerja',
        icon: '📋',
        route: 'reports.performance-agreement'
    },
    {
        id: 'monthly-assessment',
        title: 'Laporan Penilaian Bulanan',
        description: 'Laporan penilaian capaian kinerja bulanan pegawai',
        icon: '📊',
        route: 'reports.monthly-assessment'
    },
    {
        id: 'recap',
        title: 'Laporan Rekapitulasi',
        description: 'Laporan rekapitulasi penilaian kinerja tahunan pegawai',
        icon: '📈',
        route: 'reports.recap'
    }
];

const navigateToReport = (reportType) => {
    router.visit(route(reportType.route));
};
</script>

<template>

    <Head title="Laporan & Dokumen" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Laporan & Dokumen
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Pilih Jenis Laporan</h3>
                            <p class="text-gray-600">Pilih jenis laporan yang ingin Anda buat atau unduh</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div v-for="reportType in reportTypes" :key="reportType.id"
                                class="bg-gray-50 border border-gray-200 rounded-lg p-6 hover:bg-gray-100 transition-colors cursor-pointer"
                                @click="navigateToReport(reportType)">
                                <div class="flex items-center mb-4">
                                    <div class="text-3xl mr-3">{{ reportType.icon }}</div>
                                    <div>
                                        <h4 class="text-lg font-medium text-gray-900">{{ reportType.title }}</h4>
                                    </div>
                                </div>
                                <p class="text-gray-600 text-sm">{{ reportType.description }}</p>
                                <div class="mt-4">
                                    <SecondaryButton class="w-full justify-center">
                                        Pilih Laporan
                                    </SecondaryButton>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <div class="mt-8 border-t pt-6">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Ringkasan Sistem</h4>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="bg-blue-50 p-4 rounded-lg">
                                    <div class="text-2xl font-bold text-blue-600">0</div>
                                    <div class="text-sm text-gray-600">Total Pegawai</div>
                                </div>
                                <div class="bg-green-50 p-4 rounded-lg">
                                    <div class="text-2xl font-bold text-green-600">0</div>
                                    <div class="text-sm text-gray-600">Perjanjian Kinerja</div>
                                </div>
                                <div class="bg-yellow-50 p-4 rounded-lg">
                                    <div class="text-2xl font-bold text-yellow-600">0</div>
                                    <div class="text-sm text-gray-600">Penilaian Bulanan</div>
                                </div>
                                <div class="bg-purple-50 p-4 rounded-lg">
                                    <div class="text-2xl font-bold text-purple-600">0</div>
                                    <div class="text-sm text-gray-600">Laporan PDF</div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Reports -->
                        <div class="mt-8 border-t pt-6">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Laporan Terbaru</h4>
                            <div class="bg-gray-50 rounded-lg p-6 text-center">
                                <div class="text-gray-500">
                                    <div class="text-4xl mb-2">📄</div>
                                    <p>Belum ada laporan yang dibuat</p>
                                    <p class="text-sm">Pilih jenis laporan di atas untuk memulai</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>