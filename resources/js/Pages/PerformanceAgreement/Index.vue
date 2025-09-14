<script setup>
import { ref, onMounted } from 'vue';
import { router, Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    agreements: {
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

const agreements = ref(props.agreements.data);
const loading = ref(false);

const deleteAgreement = (agreement) => {
    if (confirm(`Apakah Anda yakin ingin menghapus perjanjian kinerja ${agreement.employee.name}?`)) {
        router.delete(`/performance-agreements/${agreement.id}`, {
            onSuccess: () => {
                // Refresh the page or update the list
                router.reload();
            }
        });
    }
};

const submitForApproval = (agreement) => {
    if (confirm(`Apakah Anda yakin ingin mengajukan perjanjian kinerja ${agreement.employee.name} untuk persetujuan?`)) {
        router.post(`/performance-agreements/${agreement.id}/submit`, {}, {
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
</script>

<template>

    <Head title="Perjanjian Kinerja" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Perjanjian Kinerja
                </h2>
                <Link
                    v-if="$page.props.auth.user.role === 'pejabat_penilai' || $page.props.auth.user.role === 'super_admin'"
                    :href="route('performance-agreements.create')"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Tambah Perjanjian Kinerja
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
                                            Tahun
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Dibuat Oleh
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="agreement in agreements" :key="agreement.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ agreement.employee.name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                NIP: {{ agreement.employee.nip }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ agreement.year }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                :class="['inline-flex px-2 py-1 text-xs font-semibold rounded-full', getStatusBadge(agreement.status)]">
                                                {{ getStatusText(agreement.status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ agreement.created_by.name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <Link :href="route('performance-agreements.show', agreement.id)"
                                                    class="text-blue-600 hover:text-blue-900">
                                                Lihat
                                                </Link>

                                                <!-- Edit - only for draft agreements and pejabat_penilai or super_admin -->
                                                <Link
                                                    v-if="agreement.status === 'draft' && ($page.props.auth.user.role === 'pejabat_penilai' || $page.props.auth.user.role === 'super_admin')"
                                                    :href="route('performance-agreements.edit', agreement.id)"
                                                    class="text-indigo-600 hover:text-indigo-900">
                                                Edit
                                                </Link>

                                                <!-- Submit for approval - only for draft agreements and pejabat_penilai -->
                                                <button
                                                    v-if="agreement.status === 'draft' && $page.props.auth.user.role === 'pejabat_penilai'"
                                                    @click="submitForApproval(agreement)"
                                                    class="text-green-600 hover:text-green-900">
                                                    Ajukan
                                                </button>

                                                <!-- Delete - only for draft agreements and pejabat_penilai or super_admin -->
                                                <button
                                                    v-if="agreement.status === 'draft' && ($page.props.auth.user.role === 'pejabat_penilai' || $page.props.auth.user.role === 'super_admin')"
                                                    @click="deleteAgreement(agreement)"
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
                        <div v-if="props.agreements.last_page > 1" class="mt-4">
                            <div class="flex justify-between items-center">
                                <div class="text-sm text-gray-700">
                                    Menampilkan {{ (props.agreements.current_page - 1) * props.agreements.per_page + 1
                                    }} sampai
                                    {{ Math.min(props.agreements.current_page * props.agreements.per_page,
                                        props.agreements.total) }} dari {{ props.agreements.total }} hasil
                                </div>
                                <div class="flex space-x-1">
                                    <Link v-if="props.agreements.current_page > 1"
                                        :href="route('performance-agreements.index', { page: props.agreements.current_page - 1 })"
                                        class="px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                                    Sebelumnya
                                    </Link>
                                    <Link v-if="props.agreements.current_page < props.agreements.last_page"
                                        :href="route('performance-agreements.index', { page: props.agreements.current_page + 1 })"
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