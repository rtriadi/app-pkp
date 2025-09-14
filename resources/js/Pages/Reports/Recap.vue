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
    employees: {
        type: Array,
        default: () => []
    }
});

const form = reactive({
    employee_id: '',
    year: new Date().getFullYear(),
    preview: false
});

const selectedEmployee = ref(null);
const previewData = ref(null);
const loading = ref(false);

const employees = ref(props.employees);

const onEmployeeChange = () => {
    const employee = employees.value.find(e => e.id == form.employee_id);
    if (employee) {
        selectedEmployee.value = employee;
    } else {
        selectedEmployee.value = null;
    }
    previewData.value = null;
    form.preview = false;
};

const previewReport = () => {
    if (!form.employee_id || !form.year) return;

    loading.value = true;
    // In a real implementation, you would fetch preview data from the API
    // For now, we'll just set basic preview data
    previewData.value = {
        employee: selectedEmployee.value,
        year: form.year,
        totalAssessments: 0,
        averageScore: 0,
        overallGrade: 'N/A'
    };
    form.preview = true;
    loading.value = false;
};

const downloadPdf = () => {
    if (!form.employee_id || !form.year) return;

    window.open(route('reports.recap.pdf', [form.employee_id, form.year]), '_blank');
};

const resetForm = () => {
    form.employee_id = '';
    form.year = new Date().getFullYear();
    form.preview = false;
    selectedEmployee.value = null;
    previewData.value = null;
};
</script>

<template>

    <Head title="Laporan Rekapitulasi" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Laporan Rekapitulasi
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
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Pilih Pegawai dan Tahun</h3>
                            <p class="text-gray-600">Pilih pegawai dan tahun untuk membuat laporan rekapitulasi
                                penilaian
                                kinerja</p>
                        </div>

                        <form @submit.prevent="previewReport">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div>
                                    <InputLabel for="employee_id" value="Pegawai" />
                                    <select v-model="form.employee_id" id="employee_id"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        required @change="onEmployeeChange">
                                        <option value="">-- Pilih Pegawai --</option>
                                        <option v-for="employee in employees" :key="employee.id" :value="employee.id">
                                            {{ employee.name }} ({{ employee.nip }})
                                        </option>
                                    </select>
                                    <InputError :message="form.errors?.employee_id" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="year" value="Tahun" />
                                    <select v-model="form.year" id="year"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        required>
                                        <option
                                            v-for="year in [new Date().getFullYear() - 1, new Date().getFullYear(), new Date().getFullYear() + 1]"
                                            :key="year" :value="year">
                                            {{ year }}
                                        </option>
                                    </select>
                                    <InputError :message="form.errors?.year" class="mt-2" />
                                </div>
                            </div>

                            <!-- Selected Employee Info -->
                            <div v-if="selectedEmployee" class="bg-purple-50 p-4 rounded-lg mb-6">
                                <h4 class="font-medium text-purple-900 mb-2">Informasi Pegawai</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <strong>Nama:</strong> {{ selectedEmployee.name }}
                                    </div>
                                    <div>
                                        <strong>NIP:</strong> {{ selectedEmployee.nip }}
                                    </div>
                                    <div>
                                        <strong>Jabatan:</strong> {{ selectedEmployee.position }}
                                    </div>
                                    <div>
                                        <strong>Unit Kerja:</strong> {{ selectedEmployee.workUnit?.name || 'N/A' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex justify-between">
                                <SecondaryButton type="button" @click="resetForm">
                                    Reset
                                </SecondaryButton>
                                <div class="flex space-x-3">
                                    <SecondaryButton type="button" @click="previewReport"
                                        :disabled="!form.employee_id || !form.year || loading">
                                        <span v-if="loading">Memuat...</span>
                                        <span v-else>Pratinjau</span>
                                    </SecondaryButton>
                                    <PrimaryButton type="button" @click="downloadPdf"
                                        :disabled="!form.employee_id || !form.year">
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
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Pratinjau Laporan Rekapitulasi</h3>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-900 mb-3">Rekapitulasi Penilaian Kinerja Tahunan</h4>

                            <!-- Employee Info -->
                            <div class="mb-4">
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div><strong>Pegawai:</strong> {{ previewData.employee.name }}</div>
                                    <div><strong>NIP:</strong> {{ previewData.employee.nip }}</div>
                                    <div><strong>Jabatan:</strong> {{ previewData.employee.position }}</div>
                                    <div><strong>Tahun:</strong> {{ previewData.year }}</div>
                                </div>
                            </div>

                            <!-- Summary Stats -->
                            <div class="bg-white p-4 rounded-lg mb-4">
                                <h5 class="font-medium text-gray-900 mb-3">Ringkasan Penilaian</h5>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-blue-600">{{ previewData.totalAssessments }}
                                        </div>
                                        <div class="text-gray-600">Total Penilaian</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-green-600">{{
                                            previewData.averageScore.toFixed(2) }}
                                        </div>
                                        <div class="text-gray-600">Rata-rata Nilai</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-purple-600">{{ previewData.overallGrade }}
                                        </div>
                                        <div class="text-gray-600">Grade Keseluruhan</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Monthly Breakdown Preview -->
                            <div class="bg-white p-4 rounded-lg">
                                <h5 class="font-medium text-gray-900 mb-3">Penilaian per Bulan</h5>
                                <div class="grid grid-cols-6 gap-2 text-xs">
                                    <div v-for="month in ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des']"
                                        :key="month" class="text-center p-2 bg-gray-100 rounded">
                                        <div class="font-medium">{{ month }}</div>
                                        <div class="text-gray-600">-</div>
                                    </div>
                                </div>
                                <div class="mt-3 text-sm text-gray-600">
                                    Detail lengkap penilaian bulanan akan ditampilkan dalam laporan PDF.
                                </div>
                            </div>

                            <div class="mt-4 p-3 bg-purple-50 rounded text-sm">
                                <strong>Catatan:</strong> Laporan PDF akan berisi rekapitulasi lengkap penilaian kinerja
                                tahunan
                                sesuai format pemerintah.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>