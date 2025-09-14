<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    employee: {
        type: Object,
        required: true
    }
});

const getStatusBadge = (status) => {
    const badges = {
        active: 'bg-green-100 text-green-800',
        inactive: 'bg-gray-100 text-gray-800',
        retired: 'bg-yellow-100 text-yellow-800'
    };
    return badges[status] || 'bg-gray-100 text-gray-800';
};

const getStatusText = (status) => {
    const texts = {
        active: 'Aktif',
        inactive: 'Tidak Aktif',
        retired: 'Pensiun'
    };
    return texts[status] || status;
};

const getMonthName = (month) => {
    const months = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    return months[month - 1] || '';
};
</script>

<template>

    <Head :title="`Detail Pegawai - ${employee.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Detail Pegawai
                </h2>
                <div class="flex space-x-2">
                    <Link :href="route('employees.edit', employee.id)"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                    Edit
                    </Link>
                    <SecondaryButton @click="$inertia.visit(route('employees.index'))">
                        Kembali
                    </SecondaryButton>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Employee Info -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Pegawai</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div>
                                <strong>NIP:</strong>
                                <div class="mt-1">{{ employee.nip }}</div>
                            </div>
                            <div>
                                <strong>Nama Lengkap:</strong>
                                <div class="mt-1">{{ employee.name }}</div>
                            </div>
                            <div>
                                <strong>Pangkat/Golongan:</strong>
                                <div class="mt-1">{{ employee.rank_grade || '-' }}</div>
                            </div>
                            <div>
                                <strong>Jabatan:</strong>
                                <div class="mt-1">{{ employee.position || '-' }}</div>
                            </div>
                            <div>
                                <strong>Unit Kerja:</strong>
                                <div class="mt-1">{{ employee.work_unit?.name || '-' }}</div>
                            </div>
                            <div>
                                <strong>Status:</strong>
                                <div class="mt-1">
                                    <span
                                        :class="['inline-flex px-2 py-1 text-xs font-semibold rounded-full', getStatusBadge(employee.employee_status)]">
                                        {{ getStatusText(employee.employee_status) }}
                                    </span>
                                </div>
                            </div>
                            <div v-if="employee.hire_date">
                                <strong>Tanggal Masuk:</strong>
                                <div class="mt-1">{{ new Date(employee.hire_date).toLocaleDateString('id-ID') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Performance Agreements -->
                <div v-if="employee.performance_agreements && employee.performance_agreements.length > 0"
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Perjanjian Kinerja</h3>

                        <div class="space-y-4">
                            <div v-for="agreement in employee.performance_agreements" :key="agreement.id"
                                class="border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h4 class="font-medium">Tahun {{ agreement.year }}</h4>
                                        <p class="text-sm text-gray-600">Dibuat: {{ new
                                            Date(agreement.created_at).toLocaleDateString('id-ID') }}</p>
                                    </div>
                                    <span :class="['inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                                        agreement.status === 'approved' ? 'bg-green-100 text-green-800' :
                                            agreement.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                                agreement.status === 'rejected' ? 'bg-red-100 text-red-800' :
                                                    'bg-gray-100 text-gray-800']">
                                        {{ agreement.status === 'approved' ? 'Disetujui' :
                                            agreement.status === 'pending' ? 'Menunggu Persetujuan' :
                                                agreement.status === 'rejected' ? 'Ditolak' :
                                                    'Draft' }}
                                    </span>
                                </div>

                                <div class="flex space-x-4">
                                    <Link :href="route('performance-agreements.show', agreement.id)"
                                        class="text-blue-600 hover:text-blue-900 text-sm">
                                    Lihat Detail
                                    </Link>
                                    <Link :href="route('reports.performance-agreement.pdf', agreement.id)"
                                        target="_blank" class="text-green-600 hover:text-green-900 text-sm">
                                    Download PDF
                                    </Link>
                                </div>

                                <!-- Monthly Assessments -->
                                <div v-if="agreement.monthly_assessments && agreement.monthly_assessments.length > 0"
                                    class="mt-4">
                                    <h5 class="text-sm font-medium text-gray-700 mb-2">Penilaian Bulanan:</h5>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                                        <div v-for="assessment in agreement.monthly_assessments" :key="assessment.id"
                                            class="text-xs bg-gray-50 p-2 rounded">
                                            <div class="font-medium">{{ getMonthName(assessment.month) }} {{
                                                assessment.year }}
                                            </div>
                                            <div>Nilai: {{ assessment.total_score }}</div>
                                            <div>Grade: {{ assessment.grade }}</div>
                                            <Link :href="route('monthly-assessments.show', assessment.id)"
                                                class="text-blue-600 hover:text-blue-900">
                                            Lihat
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- No Performance Agreements -->
                <div v-else class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <div class="text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada Perjanjian Kinerja</h3>
                            <p class="mt-1 text-sm text-gray-500">Pegawai ini belum memiliki perjanjian kinerja yang
                                tercatat.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>