<script setup>
import { ref, reactive, onMounted } from 'vue';
import { router, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    agreements: {
        type: Array,
        default: () => []
    }
});

const form = reactive({
    agreement_id: '',
    preview: false
});

const selectedAgreement = ref(null);
const previewData = ref(null);
const loading = ref(false);

const agreements = ref(props.agreements);

const onAgreementChange = () => {
    const agreement = agreements.value.find(a => a.id == form.agreement_id);
    if (agreement) {
        selectedAgreement.value = agreement;
    } else {
        selectedAgreement.value = null;
    }
    previewData.value = null;
    form.preview = false;
};

const previewReport = () => {
    if (!form.agreement_id) return;

    loading.value = true;
    // In a real implementation, you would fetch preview data from the API
    // For now, we'll just set the preview data to the selected agreement
    previewData.value = selectedAgreement.value;
    form.preview = true;
    loading.value = false;
};

const downloadPdf = () => {
    if (!form.agreement_id) return;

    window.open(route('reports.performance-agreement.pdf', form.agreement_id), '_blank');
};

const resetForm = () => {
    form.agreement_id = '';
    form.preview = false;
    selectedAgreement.value = null;
    previewData.value = null;
};
</script>

<template>

    <Head title="Laporan Perjanjian Kinerja" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Laporan Perjanjian Kinerja
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
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Pilih Perjanjian Kinerja</h3>
                            <p class="text-gray-600">Pilih perjanjian kinerja yang ingin dibuat laporannya</p>
                        </div>

                        <form @submit.prevent="previewReport">
                            <div class="mb-6">
                                <InputLabel for="agreement_id" value="Perjanjian Kinerja" />
                                <select v-model="form.agreement_id" id="agreement_id"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required @change="onAgreementChange">
                                    <option value="">-- Pilih Perjanjian Kinerja --</option>
                                    <option v-for="agreement in agreements" :key="agreement.id" :value="agreement.id">
                                        {{ agreement.employee.name }} - {{ agreement.year }} ({{ agreement.status }})
                                    </option>
                                </select>
                                <InputError :message="form.errors?.agreement_id" class="mt-2" />
                            </div>

                            <!-- Selected Agreement Info -->
                            <div v-if="selectedAgreement" class="bg-blue-50 p-4 rounded-lg mb-6">
                                <h4 class="font-medium text-blue-900 mb-2">Informasi Perjanjian</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <strong>Pegawai:</strong> {{ selectedAgreement.employee.name }}
                                    </div>
                                    <div>
                                        <strong>NIP:</strong> {{ selectedAgreement.employee.nip }}
                                    </div>
                                    <div>
                                        <strong>Tahun:</strong> {{ selectedAgreement.year }}
                                    </div>
                                    <div>
                                        <strong>Status:</strong>
                                        <span :class="[
                                            'inline-flex px-2 py-1 text-xs font-semibold rounded-full ml-2',
                                            selectedAgreement.status === 'approved' ? 'bg-green-100 text-green-800' :
                                                selectedAgreement.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                                    'bg-gray-100 text-gray-800'
                                        ]">
                                            {{ selectedAgreement.status === 'approved' ? 'Disetujui' :
                                                selectedAgreement.status === 'pending' ? 'Menunggu' : 'Draft' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <strong>Jumlah Sasaran:</strong> {{ selectedAgreement.performance_targets?.length ||
                                    0 }}
                                    sasaran
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex justify-between">
                                <SecondaryButton type="button" @click="resetForm">
                                    Reset
                                </SecondaryButton>
                                <div class="flex space-x-3">
                                    <SecondaryButton type="button" @click="previewReport"
                                        :disabled="!form.agreement_id || loading">
                                        <span v-if="loading">Memuat...</span>
                                        <span v-else>Pratinjau</span>
                                    </SecondaryButton>
                                    <PrimaryButton type="button" @click="downloadPdf" :disabled="!form.agreement_id">
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
                            <h4 class="font-medium text-gray-900 mb-3">Perjanjian Kinerja Individu</h4>

                            <!-- Employee Info -->
                            <div class="mb-4">
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div><strong>Nama:</strong> {{ previewData.employee.name }}</div>
                                    <div><strong>NIP:</strong> {{ previewData.employee.nip }}</div>
                                    <div><strong>Jabatan:</strong> {{ previewData.employee.position }}</div>
                                    <div><strong>Tahun:</strong> {{ previewData.year }}</div>
                                </div>
                            </div>

                            <!-- Targets Preview -->
                            <div v-if="previewData.performance_targets && previewData.performance_targets.length > 0">
                                <h5 class="font-medium text-gray-900 mb-2">Sasaran Kinerja:</h5>
                                <div class="space-y-2">
                                    <div v-for="(target, index) in previewData.performance_targets.slice(0, 3)"
                                        :key="target.id" class="text-sm bg-white p-2 rounded border">
                                        <div class="font-medium">{{ index + 1 }}. {{ target.activity_goal }}</div>
                                        <div class="text-gray-600 ml-4">{{ target.performance_indicator }}</div>
                                    </div>
                                    <div v-if="previewData.performance_targets.length > 3"
                                        class="text-sm text-gray-500">
                                        ... dan {{ previewData.performance_targets.length - 3 }} sasaran lainnya
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 p-3 bg-blue-50 rounded text-sm">
                                <strong>Catatan:</strong> Laporan PDF akan berisi detail lengkap perjanjian kinerja
                                sesuai
                                format pemerintah.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>