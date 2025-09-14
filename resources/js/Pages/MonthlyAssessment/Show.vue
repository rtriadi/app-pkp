<script setup>
import { ref } from 'vue';
import { router, Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    assessment: {
        type: Object,
        required: true
    }
});

const getStatusBadge = (status) => {
    const badges = {
        draft: 'bg-gray-100 text-gray-800',
        submitted: 'bg-yellow-100 text-yellow-800',
        approved: 'bg-green-100 text-green-800'
    };
    return badges[status] || 'bg-gray-100 text-gray-800';
};

const getStatusText = (status) => {
    const texts = {
        draft: 'Draft',
        submitted: 'Diajukan',
        approved: 'Disetujui'
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

const calculateScore = (detail) => {
    if (detail.ak_target && detail.quantity_realization && detail.quality_realization) {
        const target_avg = (parseFloat(detail.ak_target) + parseFloat(detail.quantity_realization)) / 2;
        const realization_avg = (parseFloat(detail.quality_realization) + parseFloat(detail.quantity_realization)) / 2;
        return ((target_avg + realization_avg) / 2).toFixed(2);
    }
    return '0.00';
};

const getGrade = (score) => {
    const numScore = parseFloat(score);
    if (numScore >= 91) return 'Sangat Baik';
    if (numScore >= 76) return 'Baik';
    if (numScore >= 61) return 'Cukup';
    if (numScore >= 51) return 'Kurang';
    return 'Sangat Kurang';
};

const downloadPdf = () => {
    window.open(route('reports.monthly-assessment.pdf', props.assessment.id), '_blank');
};

const submitAssessment = () => {
    router.post(`/monthly-assessments/${props.assessment.id}/submit`, {}, {
        onSuccess: () => {
            router.reload();
        }
    });
};

const approveAssessment = () => {
    if (confirm('Apakah Anda yakin ingin menyetujui penilaian bulanan ini?')) {
        router.post(`/monthly-assessments/${props.assessment.id}/approve`, {}, {
            onSuccess: () => {
                router.reload();
            }
        });
    }
};
</script>

<template>

    <Head :title="`Penilaian Bulanan - ${assessment.performanceAgreement.employee.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Detail Penilaian Bulanan
                </h2>
                <div class="flex space-x-2">
                    <SecondaryButton @click="downloadPdf">
                        Download PDF
                    </SecondaryButton>
                    <SecondaryButton @click="router.visit(route('monthly-assessments.index'))">
                        Kembali
                    </SecondaryButton>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Assessment Info -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Informasi Penilaian</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <strong>Pegawai:</strong> {{ assessment.performanceAgreement.employee.name }}
                                    </div>
                                    <div>
                                        <strong>NIP:</strong> {{ assessment.performanceAgreement.employee.nip }}
                                    </div>
                                    <div>
                                        <strong>Periode:</strong> {{ getMonthName(assessment.month) }} {{
                                        assessment.year }}
                                    </div>
                                    <div>
                                        <strong>Status:</strong>
                                        <span
                                            :class="['inline-flex px-2 py-1 text-xs font-semibold rounded-full ml-2', getStatusBadge(assessment.status)]">
                                            {{ getStatusText(assessment.status) }}
                                        </span>
                                    </div>
                                    <div>
                                        <strong>Pejabat Penilai:</strong> {{ assessment.assessor.name }}
                                    </div>
                                    <div v-if="assessment.supervisor">
                                        <strong>Atasan Pejabat Penilai:</strong> {{ assessment.supervisor.name }}
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-col space-y-2">
                                <Link v-if="assessment.status === 'draft'"
                                    :href="route('monthly-assessments.edit', assessment.id)"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                Edit
                                </Link>
                                <PrimaryButton v-if="assessment.status === 'draft'" @click="submitAssessment">
                                    Ajukan Persetujuan
                                </PrimaryButton>
                                <PrimaryButton v-if="assessment.status === 'submitted'" @click="approveAssessment"
                                    class="bg-green-600 hover:bg-green-700">
                                    Setujui
                                </PrimaryButton>
                            </div>
                        </div>

                        <!-- Summary -->
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="flex justify-between items-center">
                                <div>
                                    <strong>Total Nilai:</strong> {{ assessment.total_score.toFixed(2) }}
                                </div>
                                <div>
                                    <strong>Grade:</strong> {{ assessment.grade }}
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div v-if="assessment.notes" class="mt-4">
                            <strong>Catatan:</strong>
                            <div class="mt-2 p-3 bg-gray-50 rounded-md">
                                {{ assessment.notes }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Assessment Details -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Detail Penilaian per Sasaran</h3>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
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
                                            Target
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Realisasi
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nilai
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Grade
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="detail in assessment.assessmentDetails" :key="detail.id">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ detail.performanceTarget.activity_goal }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900">
                                                {{ detail.performanceTarget.performance_indicator }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900">
                                                <div>AK: {{ detail.ak_target }}</div>
                                                <div>Kuantitas: {{ detail.performanceTarget.quantity_target }} {{
                                                    detail.performanceTarget.unit }}</div>
                                                <div>Mutu: {{ detail.performanceTarget.quality_target }}%</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900">
                                                <div>AK: {{ detail.ak_target }}</div>
                                                <div>Kuantitas: {{ detail.quantity_realization }} {{
                                                    detail.performanceTarget.unit }}</div>
                                                <div>Mutu: {{ detail.quality_realization }}%</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ calculateScore(detail) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="text-sm text-gray-900">
                                                {{ getGrade(calculateScore(detail)) }}
                                            </div>
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