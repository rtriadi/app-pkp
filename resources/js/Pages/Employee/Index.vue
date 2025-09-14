<script setup>
import { ref, onMounted } from 'vue';
import { router, Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    employees: {
        type: Object,
        default: () => ({
            data: [],
            current_page: 1,
            last_page: 1,
            per_page: 15,
            total: 0
        })
    }
});

const employees = ref(props.employees.data);
const loading = ref(false);
const importFile = ref(null);
const importResults = ref(null);
const showImportModal = ref(false);

const deleteEmployee = (employee) => {
    if (confirm(`Apakah Anda yakin ingin menghapus pegawai ${employee.name}?`)) {
        router.delete(`/employees/${employee.id}`, {
            onSuccess: () => {
                router.reload();
            }
        });
    }
};

const handleFileImport = () => {
    if (!importFile.value) {
        alert('Pilih file untuk diimpor terlebih dahulu');
        return;
    }

    const formData = new FormData();
    formData.append('file', importFile.value);

    loading.value = true;

    router.post('/employees/import', formData, {
        onSuccess: (response) => {
            importResults.value = response.props.flash.results;
            showImportModal.value = true;
            router.reload();
        },
        onError: (errors) => {
            alert('Gagal mengimpor data: ' + (errors.message || 'Terjadi kesalahan'));
        },
        onFinish: () => {
            loading.value = false;
        }
    });
};

const downloadTemplate = () => {
    window.open(route('employees.download-template'), '_blank');
};

const closeImportModal = () => {
    showImportModal.value = false;
    importResults.value = null;
    importFile.value = null;
};

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
</script>

<template>

    <Head title="Manajemen Pegawai" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Manajemen Pegawai
                </h2>
                <div class="flex space-x-2">
                    <SecondaryButton @click="downloadTemplate">
                        Download Template
                    </SecondaryButton>
                    <label
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 cursor-pointer">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                            </path>
                        </svg>
                        Import Excel
                        <input type="file" ref="importFile" @change="(e) => importFile = e.target.files[0]"
                            accept=".xlsx,.xls,.csv" class="hidden">
                    </label>
                    <Link :href="route('employees.create')"
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                    Tambah Pegawai
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Import Results Modal -->
                <div v-if="showImportModal"
                    class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
                    @click="closeImportModal">
                    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" @click.stop>
                        <div class="mt-3">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Hasil Import</h3>

                            <div class="mb-4">
                                <div class="flex justify-between text-sm">
                                    <span>Berhasil:</span>
                                    <span class="font-semibold text-green-600">{{ importResults?.success_count || 0
                                        }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span>Gagal:</span>
                                    <span class="font-semibold text-red-600">{{ importResults?.error_count || 0
                                        }}</span>
                                </div>
                            </div>

                            <div v-if="importResults?.errors?.length > 0" class="mb-4">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Detail Error:</h4>
                                <div class="max-h-32 overflow-y-auto bg-gray-50 p-2 rounded text-xs">
                                    <div v-for="(error, index) in importResults.errors" :key="index"
                                        class="text-red-600 mb-1">
                                        {{ error }}
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <SecondaryButton @click="closeImportModal">
                                    Tutup
                                </SecondaryButton>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            NIP
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nama
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Jabatan
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Unit Kerja
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="employee in employees" :key="employee.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ employee.nip }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ employee.name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ employee.rank_grade }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ employee.position }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ employee.work_unit?.name || '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                :class="['inline-flex px-2 py-1 text-xs font-semibold rounded-full', getStatusBadge(employee.employee_status)]">
                                                {{ getStatusText(employee.employee_status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <Link :href="route('employees.show', employee.id)"
                                                    class="text-blue-600 hover:text-blue-900">
                                                Lihat
                                                </Link>
                                                <Link :href="route('employees.edit', employee.id)"
                                                    class="text-indigo-600 hover:text-indigo-900">
                                                Edit
                                                </Link>
                                                <button @click="deleteEmployee(employee)"
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
                        <div v-if="props.employees.last_page > 1" class="mt-4">
                            <div class="flex justify-between items-center">
                                <div class="text-sm text-gray-700">
                                    Menampilkan {{ (props.employees.current_page - 1) * props.employees.per_page + 1 }}
                                    sampai {{ Math.min(props.employees.current_page * props.employees.per_page,
                                    props.employees.total) }}
                                    dari {{ props.employees.total }} hasil
                                </div>
                                <div class="flex space-x-1">
                                    <Link v-if="props.employees.current_page > 1"
                                        :href="route('employees.index', { page: props.employees.current_page - 1 })"
                                        class="px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                                    Sebelumnya
                                    </Link>
                                    <Link v-if="props.employees.current_page < props.employees.last_page"
                                        :href="route('employees.index', { page: props.employees.current_page + 1 })"
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