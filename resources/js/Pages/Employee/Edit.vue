<script setup>
import { ref } from 'vue';
import { router, Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    employee: {
        type: Object,
        required: true
    },
    workUnits: {
        type: Array,
        default: () => []
    }
});

const form = ref({
    nip: props.employee.nip,
    name: props.employee.name,
    rank_grade: props.employee.rank_grade || '',
    position: props.employee.position || '',
    work_unit_id: props.employee.work_unit_id || '',
    employee_status: props.employee.employee_status,
    hire_date: props.employee.hire_date || ''
});

const errors = ref({});
const processing = ref(false);

const submit = () => {
    processing.value = true;

    router.put(route('employees.update', props.employee.id), form.value, {
        onSuccess: () => {
            router.visit(route('employees.show', props.employee.id));
        },
        onError: (err) => {
            errors.value = err;
        },
        onFinish: () => {
            processing.value = false;
        }
    });
};

const deleteEmployee = () => {
    if (confirm('Apakah Anda yakin ingin menghapus pegawai ini? Tindakan ini tidak dapat dibatalkan.')) {
        router.delete(route('employees.destroy', props.employee.id), {
            onSuccess: () => {
                router.visit(route('employees.index'));
            }
        });
    }
};
</script>

<template>

    <Head :title="`Edit Pegawai - ${employee.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Edit Pegawai
                </h2>
                <div class="flex space-x-2">
                    <SecondaryButton @click="$inertia.visit(route('employees.show', employee.id))">
                        Lihat Detail
                    </SecondaryButton>
                    <SecondaryButton @click="$inertia.visit(route('employees.index'))">
                        Kembali
                    </SecondaryButton>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- NIP -->
                                <div>
                                    <InputLabel for="nip" value="NIP" />
                                    <TextInput id="nip" v-model="form.nip" type="text" class="mt-1 block w-full"
                                        placeholder="Masukkan NIP" required />
                                    <InputError :message="errors.nip" class="mt-2" />
                                </div>

                                <!-- Nama -->
                                <div>
                                    <InputLabel for="name" value="Nama Lengkap" />
                                    <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full"
                                        placeholder="Masukkan nama lengkap" required />
                                    <InputError :message="errors.name" class="mt-2" />
                                </div>

                                <!-- Pangkat/Golongan -->
                                <div>
                                    <InputLabel for="rank_grade" value="Pangkat/Golongan" />
                                    <TextInput id="rank_grade" v-model="form.rank_grade" type="text"
                                        class="mt-1 block w-full" placeholder="Contoh: Pembina Tk. I / IV.b" />
                                    <InputError :message="errors.rank_grade" class="mt-2" />
                                </div>

                                <!-- Jabatan -->
                                <div>
                                    <InputLabel for="position" value="Jabatan" />
                                    <TextInput id="position" v-model="form.position" type="text"
                                        class="mt-1 block w-full" placeholder="Masukkan jabatan" />
                                    <InputError :message="errors.position" class="mt-2" />
                                </div>

                                <!-- Unit Kerja -->
                                <div>
                                    <InputLabel for="work_unit_id" value="Unit Kerja" />
                                    <select id="work_unit_id" v-model="form.work_unit_id"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">Pilih Unit Kerja</option>
                                        <option v-for="workUnit in workUnits" :key="workUnit.id" :value="workUnit.id">
                                            {{ workUnit.name }}
                                        </option>
                                    </select>
                                    <InputError :message="errors.work_unit_id" class="mt-2" />
                                </div>

                                <!-- Status -->
                                <div>
                                    <InputLabel for="employee_status" value="Status Kepegawaian" />
                                    <select id="employee_status" v-model="form.employee_status"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        required>
                                        <option value="active">Aktif</option>
                                        <option value="inactive">Tidak Aktif</option>
                                        <option value="retired">Pensiun</option>
                                    </select>
                                    <InputError :message="errors.employee_status" class="mt-2" />
                                </div>

                                <!-- Tanggal Masuk -->
                                <div class="md:col-span-2">
                                    <InputLabel for="hire_date" value="Tanggal Masuk" />
                                    <TextInput id="hire_date" v-model="form.hire_date" type="date"
                                        class="mt-1 block w-full" />
                                    <InputError :message="errors.hire_date" class="mt-2" />
                                </div>
                            </div>

                            <div class="flex items-center justify-between mt-6">
                                <DangerButton type="button" @click="deleteEmployee" :disabled="processing">
                                    Hapus Pegawai
                                </DangerButton>

                                <div class="flex space-x-2">
                                    <SecondaryButton type="button"
                                        @click="$inertia.visit(route('employees.show', employee.id))"
                                        :disabled="processing">
                                        Batal
                                    </SecondaryButton>

                                    <PrimaryButton :disabled="processing">
                                        <span v-if="processing">Menyimpan...</span>
                                        <span v-else>Simpan Perubahan</span>
                                    </PrimaryButton>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>