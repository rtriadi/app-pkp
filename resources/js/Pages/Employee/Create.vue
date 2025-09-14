<script setup>
import { ref } from 'vue';
import { router, Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    workUnits: {
        type: Array,
        default: () => []
    }
});

const form = ref({
    nip: '',
    name: '',
    rank_grade: '',
    position: '',
    work_unit_id: '',
    employee_status: 'active',
    hire_date: ''
});

const errors = ref({});
const processing = ref(false);

const submit = () => {
    processing.value = true;

    router.post(route('employees.store'), form.value, {
        onSuccess: () => {
            router.visit(route('employees.index'));
        },
        onError: (err) => {
            errors.value = err;
        },
        onFinish: () => {
            processing.value = false;
        }
    });
};
</script>

<template>

    <Head title="Tambah Pegawai" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Tambah Pegawai Baru
                </h2>
                <SecondaryButton @click="router.visit(route('employees.index'))">
                    Kembali
                </SecondaryButton>
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

                            <div class="flex items-center justify-end mt-6">
                                <SecondaryButton type="button" @click="router.visit(route('employees.index'))"
                                    :disabled="processing">
                                    Batal
                                </SecondaryButton>

                                <PrimaryButton class="ms-4" :disabled="processing">
                                    <span v-if="processing">Menyimpan...</span>
                                    <span v-else>Simpan Pegawai</span>
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>