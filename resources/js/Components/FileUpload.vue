<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    assessmentId: {
        type: [String, Number],
        required: true
    },
    maxFiles: {
        type: Number,
        default: 5
    },
    acceptedTypes: {
        type: String,
        default: '.pdf,.doc,.docx,.jpg,.jpeg,.png'
    },
    maxFileSize: {
        type: Number,
        default: 5120 // 5MB in KB
    }
});

const emit = defineEmits(['uploaded', 'error']);

const files = ref([]);
const descriptions = ref([]);
const uploading = ref(false);
const errors = ref({});
const dragOver = ref(false);

const acceptedMimeTypes = computed(() => {
    const types = props.acceptedTypes.split(',');
    return types.map(type => {
        if (type.startsWith('.')) {
            const ext = type.substring(1);
            switch (ext) {
                case 'pdf': return 'application/pdf';
                case 'doc': return 'application/msword';
                case 'docx': return 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
                case 'jpg':
                case 'jpeg': return 'image/jpeg';
                case 'png': return 'image/png';
                default: return '';
            }
        }
        return type;
    }).filter(type => type);
});

const handleFileSelect = (event) => {
    const selectedFiles = Array.from(event.target.files);
    addFiles(selectedFiles);
};

const handleDrop = (event) => {
    event.preventDefault();
    dragOver.value = false;

    const droppedFiles = Array.from(event.dataTransfer.files);
    addFiles(droppedFiles);
};

const handleDragOver = (event) => {
    event.preventDefault();
    dragOver.value = true;
};

const handleDragLeave = (event) => {
    event.preventDefault();
    dragOver.value = false;
};

const addFiles = (newFiles) => {
    const validFiles = [];
    const fileErrors = [];

    newFiles.forEach((file, index) => {
        // Check file type
        if (!acceptedMimeTypes.value.includes(file.type)) {
            fileErrors.push(`${file.name}: Tipe file tidak didukung`);
            return;
        }

        // Check file size
        if (file.size > props.maxFileSize * 1024) {
            fileErrors.push(`${file.name}: Ukuran file terlalu besar (maksimal ${props.maxFileSize}KB)`);
            return;
        }

        // Check if file already exists
        const exists = files.value.some(existingFile => existingFile.name === file.name && existingFile.size === file.size);
        if (exists) {
            fileErrors.push(`${file.name}: File sudah dipilih`);
            return;
        }

        validFiles.push(file);
    });

    if (fileErrors.length > 0) {
        errors.value.files = fileErrors;
    } else {
        delete errors.value.files;
    }

    // Add valid files
    files.value.push(...validFiles);

    // Initialize descriptions for new files
    validFiles.forEach(() => {
        descriptions.value.push('');
    });

    // Limit total files
    if (files.value.length > props.maxFiles) {
        files.value = files.value.slice(0, props.maxFiles);
        descriptions.value = descriptions.value.slice(0, props.maxFiles);
    }
};

const removeFile = (index) => {
    files.value.splice(index, 1);
    descriptions.value.splice(index, 1);
    if (errors.value.files && errors.value.files.length > 0) {
        errors.value.files = errors.value.files.filter(error => !error.includes(files.value[index]?.name || ''));
    }
};

const uploadFiles = () => {
    if (files.value.length === 0) {
        errors.value.files = ['Pilih minimal satu file untuk diupload'];
        return;
    }

    const formData = new FormData();

    files.value.forEach((file, index) => {
        formData.append('files[]', file);
        if (descriptions.value[index]) {
            formData.append('descriptions[]', descriptions.value[index]);
        }
    });

    uploading.value = true;
    errors.value = {};

    router.post(`/api/monthly-assessments/${props.assessmentId}/documents`, formData, {
        onSuccess: (response) => {
            emit('uploaded', response.props.flash);
            // Reset form
            files.value = [];
            descriptions.value = [];
        },
        onError: (err) => {
            errors.value = err;
            emit('error', err);
        },
        onFinish: () => {
            uploading.value = false;
        }
    });
};

const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const getFileIcon = (file) => {
    if (file.type.startsWith('image/')) {
        return '🖼️';
    } else if (file.type === 'application/pdf') {
        return '📄';
    } else {
        return '📎';
    }
};
</script>

<template>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Upload Dokumen Pendukung</h3>

            <!-- Drop Zone -->
            <div @drop="handleDrop" @dragover="handleDragOver" @dragleave="handleDragLeave" :class="[
                'border-2 border-dashed rounded-lg p-8 text-center transition-colors',
                dragOver ? 'border-blue-400 bg-blue-50' : 'border-gray-300',
                files.length >= maxFiles ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer hover:border-gray-400'
            ]">
                <input type="file" ref="fileInput" @change="handleFileSelect" :accept="acceptedTypes"
                    :multiple="maxFiles > 1" :disabled="files.length >= maxFiles" class="hidden">

                <div v-if="files.length === 0">
                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path
                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                    <div class="mt-4">
                        <p class="text-sm text-gray-600">
                            Drag & drop files here, or
                            <button @click="$refs.fileInput.click()"
                                class="text-blue-600 hover:text-blue-500 font-medium"
                                :disabled="files.length >= maxFiles">
                                browse
                            </button>
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            Supported: {{ acceptedTypes }} (Max {{ maxFileSize }}KB per file, {{ maxFiles }} files max)
                        </p>
                    </div>
                </div>

                <div v-else>
                    <button @click="$refs.fileInput.click()" :disabled="files.length >= maxFiles"
                        class="text-blue-600 hover:text-blue-500 font-medium">
                        Add more files
                    </button>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ files.length }}/{{ maxFiles }} files selected
                    </p>
                </div>
            </div>

            <!-- File List -->
            <div v-if="files.length > 0" class="mt-4 space-y-3">
                <div v-for="(file, index) in files" :key="index"
                    class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <span class="text-lg">{{ getFileIcon(file) }}</span>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ file.name }}</p>
                            <p class="text-xs text-gray-500">{{ formatFileSize(file.size) }}</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2">
                        <input v-model="descriptions[index]" type="text" placeholder="Deskripsi (opsional)"
                            class="text-xs border border-gray-300 rounded px-2 py-1 w-48">
                        <button @click="removeFile(index)" class="text-red-600 hover:text-red-800 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Errors -->
            <InputError :message="errors.files" class="mt-2" />

            <!-- Upload Button -->
            <div v-if="files.length > 0" class="mt-4 flex justify-end">
                <PrimaryButton @click="uploadFiles" :disabled="uploading">
                    <span v-if="uploading">Mengupload...</span>
                    <span v-else>Upload {{ files.length }} File{{ files.length > 1 ? 's' : '' }}</span>
                </PrimaryButton>
            </div>
        </div>
    </div>
</template>