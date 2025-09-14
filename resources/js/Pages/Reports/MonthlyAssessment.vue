<script setup>
import { ref, reactive } from 'vue';
import { router, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    assessments: {
        type: Array,
        default: () => []
    }
});

const form = reactive({
    assessment_id: '',
    preview: false
});

const selectedAssessment = ref(null);
const previewData = ref(null);
const loading = ref(false);

const assessments = ref(props.assessments);

const onAssessmentChange = () => {
    const assessment = assessments.value.find(a => a.id == form.assessment_id);
    if (assessment) {
        selectedAssessment.value = assessment;
    } else {
        selectedAssessment.value = null;
    }
    previewData.value = null;
    form.preview = false;
};

const previewReport = () => {
    if (!form.assessment_id) return;

    loading.value = true;
    // In a real implementation, you would fetch preview data from the API
    previewData.value = selectedAssessment.value;
    form.preview = true;
    loading.value = false;
};

const downloadPdf = () => {
    if (!form.assessment_id) return;

    window.open(route('reports.monthly-assessment.pdf', form.assessment_id), '_blank');
};

const resetForm = () => {
    form.assessment_id = '';
    form.preview = false;
    selectedAssessment.value = null;
    previewData.value = null;
};

const getMonthName = (month) => {
    const months = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    return months[month - 1] || '';
};

const getStatusText = (status) => {
    const texts = {
        draft: 'Draft',
        submitted: 'Diajukan',
        approved: 'Disetujui'
    };
    return texts[status] || status;
};
</script>

<template>

    <Head title="Laporan Penilaian Bulanan" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Laporan Penilaian Bulanan
                </h2>
                <SecondaryButton @click="router.visit(route('reports.index'))">
                    Kembali
                </SecondaryButton>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Pilih Penilaian Bulanan</h3>
                            <p class="text-gray-600">Pilih penilaian bulanan yang ingin dibuat laporannya</p>
                        </div>

                        <form @submit.prevent="previewReport">
                            <div class="mb-6">
                                <InputLabel for="assessment_id" value="Penilaian Bulanan" />
                                <select v-model="form.assessment_id" id="assessment_id"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required @change="onAssessmentChange">
                                    <option value="">-- Pilih Penilaian Bulanan --</option>
                                    <option v-for="assessment in assessments" :key="assessment.id"
                                        :value="assessment.id">
                                        {{ assessment.performanceAgreement.employee.name }} -
                                        {{ getMonthName(assessment.month) }} {{ assessment.year }}
                                        ({{ getStatusText(assessment.status) }})
                                    </option>
                                </select>
                                <InputError :message="form.errors?.assessment_id" class="mt-2" />
                            </div>

                            <!-- Selected Assessment Info -->
                            <div v-if="selectedAssessment" class="bg-green-50 p-4 rounded-lg mb-6">
                                <h4 class="font-medium text-green-900 mb-2">Informasi Penilaian</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <strong>Pegawai:</strong> {{
                                            selectedAssessment.performanceAgreement.employee.name }}
                                    </div>
                                    <div>
                                        <strong>NIP:</strong> {{ selectedAssessment.performanceAgreement.employee.nip }}
                                    </div>
                                    <div>
                                        <strong>Periode:</strong> {{ getMonthName(selectedAssessment.month) }} {{
                                        selectedAssessment.year }}
                                    </div>
                                    <div>
                                        <strong>Status:</strong>
                                        <span :class="[
                                            'inline-flex px-2 py-1 text-xs font-semibold rounded-full ml-2',
                                            selectedAssessment.status === 'approved' ? 'bg-green-100 text-green-800' :
                                                selectedAssessment.status === 'submitted' ? 'bg-yellow-100 text-yellow-800' :
                                                    'bg-gray-100 text-gray-800'
                                        ]">
                                            {{ getStatusText(selectedAssessment.status) }}
                                        </span>
                                    </div>
                                    <div>
                                        <strong>Nilai Total:</strong> {{ selectedAssessment.total_score.toFixed(2) }}
                                    </div>
                                    <div>
                                        <strong>Grade:</strong> {{ selectedAssessment.grade }}
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <strong>Pejabat Penilai:</strong> {{ selectedAssessment.assessor.name }}
                                </div>
                                <div v-if="selectedAssessment.supervisor" class="mt-1">
                                    <strong>Atasan Pejabat Penilai:</strong> {{ selectedAssessment.supervisor.name }}
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex justify-between">
                                <SecondaryButton type="button" @click="resetForm">
                                    Reset
                                </SecondaryButton>
                                <div class="flex space-x-3">
                                    <SecondaryButton type="button" @click="previewReport"
                                        :disabled="!form.assessment_id || loading">
                                        <span v-if="loading">Memuat...</span>
                                        <span v-else>Pratinjau</span>
                                    </SecondaryButton>
                                    <PrimaryButton type="button" @click="downloadPdf" :disabled="!form.assessment_id">
                                        Download PDF
                                    </PrimaryButton>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Preview Section -->
                <div v-if="form.preview && previewData" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Pratinjau Laporan</h3>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-900 mb-3">Penilaian Capaian Kinerja Bulanan</h4>

                            <!-- Assessment Info -->
                            <div class="mb-4">
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div><strong>Pegawai:</strong> {{ previewData.performanceAgreement.employee.name }}
                                    </div>
                                    <div><strong>Periode:</strong> {{ getMonthName(previewData.month) }} {{
                                        previewData.year }}
                                    </div>
                                    <div><strong>Nilai Total:</strong> {{ previewData.total_score.toFixed(2) }}</div>
                                    <div><strong>Grade:</strong> {{ previewData.grade }}</div>
                                </div>
                            </div>

                            <!-- Assessment Details Preview -->
                            <div v-if="previewData.assessmentDetails && previewData.assessmentDetails.length > 0">
                                <h5 class="font-medium text-gray-900 mb-2">Detail Penilaian:</h5>
                                <div class="space-y-2">
                                    <div v-for="(detail, index) in previewData.assessmentDetails.slice(0, 3)"
                                        :key="detail.id" class="text-sm bg-white p-2 rounded border">
                                        <div class="font-medium">{{ index + 1 }}. {{
                                            detail.performanceTarget.activity_goal }}
                                        </div>
                                        <div class="text-gray-600 ml-4">
                                            Target: {{ detail.performanceTarget.quantity_target }} {{
                                            detail.performanceTarget.unit }} |
                                            Realisasi: {{ detail.quantity_realization }} {{
                                            detail.performanceTarget.unit }} |
                                            Nilai: {{ detail.score.toFixed(2) }}
                                        </div>
                                    </div>
                                    <div v-if="previewData.assessmentDetails.length > 3" class="text-sm text-gray-500">
                                        ... dan {{ previewData.assessmentDetails.length - 3 }} detail lainnya
                                    </div>
                                </div>
                            </div>

                            <!-- Notes -->
                            <div v-if="previewData.notes" class="mt-3">
                                <strong>Catatan:</strong> {{ previewData.notes }}
                            </div>

                            <div class="mt-4 p-3 bg-green-50 rounded text-sm">
                                <strong>Catatan:</strong> Laporan PDF akan berisi detail lengkap penilaian bulanan
                                sesuai format
                                pemerintah.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>