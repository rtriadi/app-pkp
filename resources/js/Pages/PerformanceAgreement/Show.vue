<script setup>
import { ref } from 'vue';
import { router, Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    agreement: {
        type: Object,
        required: true
    }
});

const getStatusBadge = (status) => {
    const badges = {
        draft: 'bg-gray-100 text-gray-800',
        pending: 'bg-yellow-100 text-yellow-800',
        approved: 'bg-green-100 text-green-800',
        rejected: 'bg-red-100 text-red-800'
    };
    return badges[status] || 'bg-gray-100 text-gray-800';
};

const getStatusText = (status) => {
    const texts = {
        draft: 'Draft',
        pending: 'Menunggu Persetujuan',
        approved: 'Disetujui',
        rejected: 'Ditolak'
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

const isMonthScheduled = (target, month) => {
    const monthProps = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
    return target[monthProps[month - 1]] || false;
};

const downloadPdf = () => {
    window.open(route('reports.performance-agreement.pdf', props.agreement.id), '_blank');
};

const submitForApproval = () => {
    if (confirm('Apakah Anda yakin ingin mengajukan perjanjian kinerja ini untuk persetujuan?')) {
        router.post(`/performance-agreements/${props.agreement.id}/submit`, {}, {
            onSuccess: (response) => {
                alert('Perjanjian kinerja berhasil diajukan untuk persetujuan!');
                router.reload();
            },
            onError: (errors) => {
                alert('Gagal mengajukan perjanjian: ' + (errors.message || 'Terjadi kesalahan'));
            }
        });
    }
};

const approveAgreement = () => {
    if (confirm('Apakah Anda yakin ingin menyetujui perjanjian kinerja ini?')) {
        router.post(`/performance-agreements/${props.agreement.id}/approve`, {}, {
            onSuccess: (response) => {
                alert('Perjanjian kinerja berhasil disetujui!');
                router.reload();
            },
            onError: (errors) => {
                alert('Gagal menyetujui perjanjian: ' + (errors.message || 'Terjadi kesalahan'));
            }
        });
    }
};

const rejectAgreement = () => {
    const reason = prompt('Masukkan alasan penolakan:');
    if (reason && reason.trim()) {
        router.post(`/performance-agreements/${props.agreement.id}/reject`, { reason: reason.trim() }, {
            onSuccess: (response) => {
                alert('Perjanjian kinerja berhasil ditolak!');
                router.reload();
            },
            onError: (errors) => {
                alert('Gagal menolak perjanjian: ' + (errors.message || 'Terjadi kesalahan'));
            }
        });
    }
};
</script>

<template>

    <Head :title="`Perjanjian Kinerja - ${agreement.employee.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Detail Perjanjian Kinerja
                </h2>
                <div class="flex space-x-2">
                    <SecondaryButton @click="downloadPdf">
                        Download PDF
                    </SecondaryButton>
                    <SecondaryButton @click="router.visit(route('performance-agreements.index'))">
                        Kembali
                    </SecondaryButton>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Agreement Info -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Informasi Perjanjian</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <strong>Pegawai:</strong> {{ agreement.employee.name }}
                                    </div>
                                    <div>
                                        <strong>NIP:</strong> {{ agreement.employee.nip }}
                                    </div>
                                    <div>
                                        <strong>Jabatan:</strong> {{ agreement.employee.position }}
                                    </div>
                                    <div>
                                        <strong>Tahun:</strong> {{ agreement.year }}
                                    </div>
                                    <div>
                                        <strong>Status:</strong>
                                        <span
                                            :class="['inline-flex px-2 py-1 text-xs font-semibold rounded-full ml-2', getStatusBadge(agreement.status)]">
                                            {{ getStatusText(agreement.status) }}
                                        </span>
                                    </div>
                                    <div>
                                        <strong>Dibuat Oleh:</strong> {{ agreement.created_by.name }}
                                    </div>
                                    <div v-if="agreement.approved_by">
                                        <strong>Disetujui Oleh:</strong> {{ agreement.approved_by.name }}
                                    </div>
                                    <div v-if="agreement.approved_at">
                                        <strong>Tanggal Persetujuan:</strong> {{ new
                                            Date(agreement.approved_at).toLocaleDateString('id-ID') }}
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-col space-y-2">
                                <!-- Edit button - only for draft agreements and pejabat_penilai or super_admin -->
                                <Link
                                    v-if="agreement.status === 'draft' && ($page.props.auth.user.role === 'pejabat_penilai' || $page.props.auth.user.role === 'super_admin')"
                                    :href="route('performance-agreements.edit', agreement.id)"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                Edit
                                </Link>

                                <!-- Submit for approval - only for draft agreements and pejabat_penilai -->
                                <PrimaryButton
                                    v-if="agreement.status === 'draft' && $page.props.auth.user.role === 'pejabat_penilai'"
                                    @click="submitForApproval">
                                    Ajukan Persetujuan
                                </PrimaryButton>

                                <!-- Approve button - only for pending agreements and atasan_pejabat -->
                                <PrimaryButton
                                    v-if="agreement.status === 'pending' && $page.props.auth.user.role === 'atasan_pejabat'"
                                    @click="approveAgreement" class="bg-green-600 hover:bg-green-700">
                                    Setujui
                                </PrimaryButton>

                                <!-- Reject button - only for pending agreements and atasan_pejabat -->
                                <DangerButton
                                    v-if="agreement.status === 'pending' && $page.props.auth.user.role === 'atasan_pejabat'"
                                    @click="rejectAgreement">
                                    Tolak
                                </DangerButton>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Performance Targets -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Sasaran Kinerja</h3>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            No
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Sasaran Kegiatan
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Indikator Kinerja
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Target Mutu
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Target Kuantitas
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Satuan
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Waktu Penyelesaian
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="(target, index) in agreement.performance_targets" :key="target.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ index + 1 }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ target.activity_goal }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ target.performance_indicator }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ target.quality_target }}%
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ target.quantity_target }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ target.unit }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <div class="flex flex-wrap gap-1">
                                                <span v-for="month in 12" :key="month" :class="[
                                                    'inline-block w-6 h-6 text-xs text-center rounded',
                                                    isMonthScheduled(target, month)
                                                        ? 'bg-blue-600 text-white'
                                                        : 'bg-gray-200 text-gray-500'
                                                ]">
                                                    {{ month }}
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Monthly Assessments -->
                <div v-if="agreement.monthly_assessments && agreement.monthly_assessments.length > 0"
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Penilaian Bulanan</h3>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Bulan
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nilai
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Grade
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="assessment in agreement.monthly_assessments" :key="assessment.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ getMonthName(assessment.month) }} {{ assessment.year }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                :class="['inline-flex px-2 py-1 text-xs font-semibold rounded-full', getStatusBadge(assessment.status)]">
                                                {{ getStatusText(assessment.status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ assessment.total_score }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ assessment.grade }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <Link :href="route('monthly-assessments.show', assessment.id)"
                                                class="text-blue-600 hover:text-blue-900">
                                            Lihat Detail
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>