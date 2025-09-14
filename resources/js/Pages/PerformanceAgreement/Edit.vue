<script setup>
import { ref, reactive, onMounted } from 'vue';
import { router, Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    agreement: {
        type: Object,
        required: true
    },
    employees: {
        type: Array,
        default: () => []
    }
});

const form = useForm({
    employee_id: props.agreement.employee_id,
    year: props.agreement.year,
    targets: []
});

const addTarget = () => {
    form.targets.push({
        activity_goal: '',
        performance_indicator: '',
        quality_target: '',
        quantity_target: '',
        unit: '',
        months: []
    });
};

const removeTarget = (index) => {
    form.targets.splice(index, 1);
};

const toggleMonth = (targetIndex, month) => {
    const target = form.targets[targetIndex];
    const monthIndex = target.months.indexOf(month);
    if (monthIndex > -1) {
        target.months.splice(monthIndex, 1);
    } else {
        target.months.push(month);
    }
};

const isMonthSelected = (targetIndex, month) => {
    return form.targets[targetIndex].months.includes(month);
};

const submit = () => {
    form.put(route('performance-agreements.update', props.agreement.id), {
        onSuccess: () => {
            router.visit(route('performance-agreements.show', props.agreement.id));
        },
        onError: (errors) => {
            console.error('Validation errors:', errors);
        }
    });
};

const monthNames = [
    'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
    'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'
];

// Load existing targets
onMounted(() => {
    if (props.agreement.performance_targets) {
        form.targets = props.agreement.performance_targets.map(target => ({
            activity_goal: target.activity_goal,
            performance_indicator: target.performance_indicator,
            quality_target: target.quality_target,
            quantity_target: target.quantity_target,
            unit: target.unit,
            months: target.scheduled_months || []
        }));
    }

    // If no targets, add one
    if (form.targets.length === 0) {
        addTarget();
    }
});
</script>

<template>

    <Head title="Edit Perjanjian Kinerja" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Edit Perjanjian Kinerja - {{ agreement.employee.name }}
                </h2>
                <SecondaryButton @click="router.visit(route('performance-agreements.show', agreement.id))">
                    Kembali
                </SecondaryButton>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6">
                        <!-- Employee Selection -->
                        <div class="mb-6">
                            <InputLabel for="employee_id" value="Pilih Pegawai" />
                            <select v-model="form.employee_id" id="employee_id"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                required disabled>
                                <option v-for="employee in employees" :key="employee.id" :value="employee.id">
                                    {{ employee.name }} ({{ employee.nip }})
                                </option>
                            </select>
                            <p class="text-sm text-gray-500 mt-1">Pegawai tidak dapat diubah setelah perjanjian dibuat
                            </p>
                            <InputError :message="form.errors.employee_id" class="mt-2" />
                        </div>

                        <!-- Year Selection -->
                        <div class="mb-6">
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
                            <InputError :message="form.errors.year" class="mt-2" />
                        </div>

                        <!-- Performance Targets -->
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Sasaran Kinerja</h3>
                                <PrimaryButton type="button" @click="addTarget" class="text-sm">
                                    + Tambah Sasaran
                                </PrimaryButton>
                            </div>

                            <div v-for="(target, index) in form.targets" :key="index"
                                class="border border-gray-200 rounded-lg p-4 mb-4">
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="text-md font-medium">Sasaran {{ index + 1 }}</h4>
                                    <button v-if="form.targets.length > 1" type="button" @click="removeTarget(index)"
                                        class="text-red-600 hover:text-red-800">
                                        Hapus
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <InputLabel :for="`activity_goal_${index}`" value="Sasaran Kegiatan" />
                                        <textarea :id="`activity_goal_${index}`" v-model="target.activity_goal"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                            rows="3" required></textarea>
                                        <InputError :message="form.errors[`targets.${index}.activity_goal`]"
                                            class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel :for="`performance_indicator_${index}`" value="Indikator Kinerja" />
                                        <textarea :id="`performance_indicator_${index}`"
                                            v-model="target.performance_indicator"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                            rows="3" required></textarea>
                                        <InputError :message="form.errors[`targets.${index}.performance_indicator`]"
                                            class="mt-2" />
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                    <div>
                                        <InputLabel :for="`quality_target_${index}`" value="Target Mutu (%)" />
                                        <TextInput :id="`quality_target_${index}`" v-model="target.quality_target"
                                            type="number" class="mt-1 block w-full" min="0" max="100" required />
                                        <InputError :message="form.errors[`targets.${index}.quality_target`]"
                                            class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel :for="`quantity_target_${index}`" value="Target Kuantitas" />
                                        <TextInput :id="`quantity_target_${index}`" v-model="target.quantity_target"
                                            type="number" class="mt-1 block w-full" min="0" required />
                                        <InputError :message="form.errors[`targets.${index}.quantity_target`]"
                                            class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel :for="`unit_${index}`" value="Satuan" />
                                        <TextInput :id="`unit_${index}`" v-model="target.unit" type="text"
                                            class="mt-1 block w-full" required />
                                        <InputError :message="form.errors[`targets.${index}.unit`]" class="mt-2" />
                                    </div>
                                </div>

                                <!-- Month Selection -->
                                <div class="mb-4">
                                    <InputLabel value="Waktu Penyelesaian (Pilih Bulan)" />
                                    <div class="grid grid-cols-6 gap-2 mt-2">
                                        <button v-for="(monthName, monthIndex) in monthNames" :key="monthIndex"
                                            type="button" @click="toggleMonth(index, monthIndex + 1)" :class="[
                                                'py-2 px-3 text-sm font-medium rounded-md border transition-colors',
                                                isMonthSelected(index, monthIndex + 1)
                                                    ? 'bg-blue-600 text-white border-blue-600'
                                                    : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                                            ]">
                                            {{ monthName }}
                                        </button>
                                    </div>
                                    <InputError :message="form.errors[`targets.${index}.months`]" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-end space-x-4">
                            <SecondaryButton type="button"
                                @click="router.visit(route('performance-agreements.show', agreement.id))">
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