<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import { router, Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    assessment: {
        type: Object,
        required: true
    }
});

const form = useForm({
    details: [],
    notes: props.assessment.notes || ''
});

const availableTargets = ref([]);

const monthNames = [
    { value: 1, label: 'Januari' },
    { value: 2, label: 'Februari' },
    { value: 3, label: 'Maret' },
    { value: 4, label: 'April' },
    { value: 5, label: 'Mei' },
    { value: 6, label: 'Juni' },
    { value: 7, label: 'Juli' },
    { value: 8, label: 'Agustus' },
    { value: 9, label: 'September' },
    { value: 10, label: 'Oktober' },
    { value: 11, label: 'November' },
    { value: 12, label: 'Desember' }
];

// Load existing data
onMounted(() => {
    if (props.assessment.assessmentDetails) {
        form.details = props.assessment.assessmentDetails.map(detail => ({
            target_id: detail.target_id,
            ak_target: detail.ak_target,
            quantity_realization: detail.quantity_realization,
            quality_realization: detail.quality_realization
        }));

        // Load available targets
        if (props.assessment.performanceAgreement?.performance_targets) {
            availableTargets.value = props.assessment.performanceAgreement.performance_targets.filter(target => {
                // Check if target is scheduled for the assessment month
                const monthProps = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
                return target[monthProps[props.assessment.month - 1]] || false;
            });
        }
    }
});

const submit = () => {
    form.put(route('monthly-assessments.update', props.assessment.id), {
        onSuccess: () => {
            router.visit(route('monthly-assessments.show', props.assessment.id));
        },
        onError: (errors) => {
            console.error('Validation errors:', errors);
        }
    });
};

const calculateScore = (detail) => {
    if (detail.ak_target && detail.quantity_realization && detail.quality_realization) {
        const target_avg = (parseFloat(detail.ak_target) + parseFloat(detail.quantity_realization)) / 2;
        const realization_avg = (parseFloat(detail.quality_realization) + parseFloat(detail.quantity_realization)) / 2;
        return ((target_avg + realization_avg) / 2).toFixed(2);
    }
    return '';
};

const getGrade = (score) => {
    if (!score) return '';
    const numScore = parseFloat(score);
    if (numScore >= 91) return 'Sangat Baik';
    if (numScore >= 76) return 'Baik';
    if (numScore >= 61) return 'Cukup';
    if (numScore >= 51) return 'Kurang';
    return 'Sangat Kurang';
};

const totalScore = computed(() => {
    if (form.details.length === 0) return 0;
    const scores = form.details.map(detail => parseFloat(calculateScore(detail)) || 0);
    return (scores.reduce((sum, score) => sum + score, 0) / scores.length).toFixed(2);
});

const overallGrade = computed(() => {
    return getGrade(totalScore.value);
});

const getMonthName = (month) => {
    const monthOption = monthNames.find(m => m.value === month);
    return monthOption ? monthOption.label : '';
};
</script>

<template>

    <Head title="Edit Penilaian Bulanan" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Edit Penilaian Bulanan - {{ assessment.performanceAgreement.employee.name }}
                </h2>
                <SecondaryButton @click="router.visit(route('monthly-assessments.show', assessment.id))">
                    Kembali
                </SecondaryButton>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6">
                        <!-- Assessment Info (Read-only) -->
                        <div class="bg-gray-50 p-4 rounded-lg mb-6">
                            <h4 class="font-medium text-gray-900 mb-2">Informasi Penilaian</h4>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                                <div>
                                    <strong>Pegawai:</strong> {{ assessment.performanceAgreement.employee.name }}
                                </div>
                                <div>
                                    <strong>NIP:</strong> {{ assessment.performanceAgreement.employee.nip }}
                                </div>
                                <div>
                                    <strong>Periode:</strong> {{ getMonthName(assessment.month) }} {{ assessment.year }}
                                </div>
                                <div>
                                    <strong>Status:</strong> {{ assessment.status }}
                                </div>
                            </div>
                        </div>

                        <!-- Assessment Details -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Detail Penilaian</h3>

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
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="(detail, index) in form.details" :key="index">
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ availableTargets[index]?.activity_goal }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ availableTargets[index]?.performance_indicator }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900">
                                                    <div>Kuantitas: {{ availableTargets[index]?.quantity_target }} {{
                                                        availableTargets[index]?.unit }}</div>
                                                    <div>Mutu: {{ availableTargets[index]?.quality_target }}%</div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="space-y-2">
                                                    <div>
                                                        <InputLabel :for="`ak_target_${index}`" value="AK Target"
                                                            class="text-xs" />
                                                        <TextInput :id="`ak_target_${index}`" v-model="detail.ak_target"
                                                            type="number" step="0.01" class="text-sm" required />
                                                    </div>
                                                    <div>
                                                        <InputLabel :for="`quantity_realization_${index}`"
                                                            value="Kuantitas Realisasi" class="text-xs" />
                                                        <TextInput :id="`quantity_realization_${index}`"
                                                            v-model="detail.quantity_realization" type="number"
                                                            step="0.01" class="text-sm" required />
                                                    </div>
                                                    <div>
                                                        <InputLabel :for="`quality_realization_${index}`"
                                                            value="Mutu Realisasi (%)" class="text-xs" />
                                                        <TextInput :id="`quality_realization_${index}`"
                                                            v-model="detail.quality_realization" type="number"
                                                            step="0.01" min="0" max="100" class="text-sm" required />
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ calculateScore(detail) }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ getGrade(calculateScore(detail)) }}
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Summary -->
                            <div class="mt-6 bg-blue-50 p-4 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <strong>Total Nilai Rata-rata:</strong> {{ totalScore }}
                                    </div>
                                    <div>
                                        <strong>Grade Keseluruhan:</strong> {{ overallGrade }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-6">
                            <InputLabel for="notes" value="Catatan" />
                            <textarea v-model="form.notes" id="notes"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                rows="4" placeholder="Tambahkan catatan penilaian..."></textarea>
                            <InputError :message="form.errors.notes" class="mt-2" />
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-end space-x-4">
                            <SecondaryButton type="button"
                                @click="router.visit(route('monthly-assessments.show', assessment.id))">
                                Batal
                            </SecondaryButton>
                            <PrimaryButton :disabled="form.processing" type="submit">
                                <span v-if="form.processing">Menyimpan...</span>
                                <span v-else>Simpan Perubahan</span>
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>