<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    assessmentId: {
        type: [String, Number],
        required: true
    },
    documents: {
        type: Array,
        default: () => []
    },
    canEdit: {
        type: Boolean,
        default: true
    }
});

const emit = defineEmits(['deleted']);

const deleting = ref(null);

const downloadDocument = (document) => {
    window.open(`/api/monthly-assessments/${props.assessmentId}/documents/${document.id}/download`, '_blank');
};

const deleteDocument = (document) => {
    if (confirm(`Apakah Anda yakin ingin menghapus dokumen "${document.original_name}"?`)) {
        deleting.value = document.id;

        router.delete(`/api/monthly-assessments/${props.assessmentId}/documents/${document.id}`, {
            onSuccess: () => {
                emit('deleted', document.id);
            },
            onError: (errors) => {
                alert('Gagal menghapus dokumen: ' + (errors.message || 'Terjadi kesalahan'));
            },
            onFinish: () => {
                deleting.value = null;
            }
        });
    }
};

const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const getFileIcon = (document) => {
    if (document.is_image) {
        return '🖼️';
    } else if (document.mime_type === 'application/pdf') {
        return '📄';
    } else {
        return '📎';
    }
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<template>
    <div v-if="documents.length > 0" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Dokumen Pendukung</h3>

            <div class="space-y-3">
                <div v-for="document in documents" :key="document.id"
                    class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                    <div class="flex items-center space-x-4">
                        <span class="text-2xl">{{ getFileIcon(document) }}</span>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">{{ document.original_name }}</h4>
                            <p class="text-xs text-gray-500">{{ formatFileSize(document.size) }}</p>
                            <p v-if="document.description" class="text-xs text-gray-600 mt-1">
                                {{ document.description }}
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                Diupload oleh {{ document.uploader?.name || 'Unknown' }} pada {{
                                    formatDate(document.created_at) }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2">
                        <SecondaryButton @click="downloadDocument(document)" size="sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            Download
                        </SecondaryButton>

                        <DangerButton v-if="canEdit" @click="deleteDocument(document)"
                            :disabled="deleting === document.id" size="sm">
                            <template v-if="deleting === document.id">
                                <span>Menghapus...</span>
                            </template>
                            <template v-else>
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                                <span>Hapus</span>
                            </template>
                        </DangerButton>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div v-else class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-center">
            <div class="text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada dokumen</h3>
                <p class="mt-1 text-sm text-gray-500">Upload dokumen pendukung untuk penilaian ini.</p>
            </div>
        </div>
    </div>
</template>