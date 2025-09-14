<script setup>
import { ref, onMounted } from 'vue';
import { router, Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    assessments: {
        type: Object,
        default: () => ({
            data: [],
            current_page: 1,
            last_page: 1,
            per_page: 15,
            total: 0
        })
    },
    filters: {
        type: Object,
        default: () => ({})
    }
});

const assessments = ref(props.assessments.data);
const loading = ref(false);

const submitAssessment = (assessment) => {
    router.post(`/monthly-assessments/${assessment.id}/submit`, {}, {
        onSuccess: () => {
            router.reload();
        }
    });
};

const deleteAssessment = (assessment) => {
    if (confirm(`Apakah Anda yakin ingin menghapus penilaian bulanan ${assessment.performanceAgreement.employee.name} untuk bulan ${assessment.month}/${assessment.year}?`)) {
        router.delete(`/monthly-assessments/${assessment.id}`, {
            onSuccess: () => {
                router.reload();
            }
        });
    }
};

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

const formatScore = (score) => {
    return score ? score.toFixed(2) : '-';
};
</script>

<template>

    <Head title="Penilaian Bulanan" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Penilaian Bulanan
                </h2>
                <Link :href="route('monthly-assessments.create')"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Tambah Penilaian Bulanan
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Pegawai
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Periode
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
                                    <tr v-for="assessment in assessments" :key="assessment.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ assessment.performanceAgreement.employee.name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                NIP: {{ assessment.performanceAgreement.employee.nip }}
                                            </div>
                                        </td>
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
                                            {{ formatScore(assessment.total_score) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ assessment.grade || '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <Link :href="route('monthly-assessments.show', assessment.id)"
                                                    class="text-blue-600 hover:text-blue-900">
                                                Lihat
                                                </Link>
                                                <Link v-if="assessment.status === 'draft'"
                                                    :href="route('monthly-assessments.edit', assessment.id)"
                                                    class="text-indigo-600 hover:text-indigo-900">
                                                Edit
                                                </Link>
                                                <button v-if="assessment.status === 'draft'"
                                                    @click="submitAssessment(assessment)"
                                                    class="text-green-600 hover:text-green-900">
                                                    Ajukan
                                                </button>
                                                <button v-if="assessment.status === 'draft'"
                                                    @click="deleteAssessment(assessment)"
                                                    class="text-red-600 hover:text-red-900">
                                                    Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div v-if="props.assessments.last_page > 1" class="mt-4">
                            <div class="flex justify-between items-center">
                                <div class="text-sm text-gray-700">
                                    Menampilkan {{ (props.assessments.current_page - 1) * props.assessments.per_page + 1
                                    }}
                                    sampai {{ Math.min(props.assessments.current_page * props.assessments.per_page,
                                    props.assessments.total) }} dari {{ props.assessments.total }} hasil
                                </div>
                                <div class="flex space-x-1">
                                    <Link v-if="props.assessments.current_page > 1"
                                        :href="route('monthly-assessments.index', { page: props.assessments.current_page - 1 })"
                                        class="px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                                    Sebelumnya
                                    </Link>
                                    <Link v-if="props.assessments.current_page < props.assessments.last_page"
                                        :href="route('monthly-assessments.index', { page: props.assessments.current_page + 1 })"
                                        class="px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                                    Selanjutnya
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>