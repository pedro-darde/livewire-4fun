<script setup>

import {
    VStepper,
    VStepperHeader,
    VStepperItem,
    VStepperWindow,
    VStepperWindowItem
} from 'vuetify/labs/VStepper'
import {computed, ref} from "vue";
import Fields from "./Fields.vue";
import Screen from "./Screen.vue";
import DescriptionColumn from "./DescriptionColumn.vue";

const props = defineProps({
    defaultRules: {
        type: Array
    },
    screens: {
        type: Array
    },
    loading: {
        type: Boolean,
        default: false
    }
})

const fields = ref([])


const currentStep = ref(1)
const steps = ref([
    {
        title: 'Create the screen itself',
        component: Screen,
        props: {},
        form: ref(null),
    },
    {
        title: 'Create Fields',
        component: Fields,
        props: {
            defaultRules: props.defaultRules,
            screens: props.screens
        },
        form: ref(null),
    },
    {
        name: "Description",
        hint: "Choose the field that will be the description column, this column will be used for us to know what field to load on the relations fields references.",
        title: "Description Column",
        component: DescriptionColumn,
        props: {
            fields: ref(fields.value)
        },
        form: ref(null),
    }
])


const screen = ref({
    title: "",
    url: "",
    table: "",
    icon: "",
    description: "",
    pk_name: "",
})

const descriptionColumn = ref(null)


const handleUpdateScreen = ({prop, value}) => {
    switch (prop) {
        case 'fields':
            fields.value = value
            steps.value.find(item => item?.name == 'Description').props.fields = value
            break;
        case 'description':
            descriptionColumn.value = value
            break;
        default:
            screen.value = value
    }
}

const handleNextStep = async (isReturn = false) => {
    const form = steps[currentStep.value - 1].form.value[0]
    if (!isReturn) {
        const {valid, errors} = await form.validate()
        if (!valid) return
    }

    if (currentStep.value === steps.length && !isReturn) {
        const data = {
            ...screen.value,
            fields: [...fields.value]
        }

        emit('save', data)
        return;
    }

    if (isReturn && currentStep.value === 1) {
        return;
    }
    if (isReturn) {
        currentStep.value -= 1
        return;
    }
    currentStep.value += 1
}


const btnAdvanceText = computed(() => {
    if (currentStep.value === steps.length) {
        return 'Finish'
    }
    return 'Next'
})

const btnAdvanceColor = computed(() => {
    if (currentStep.value === steps.length) {
        return 'success'
    }
    return 'primary'
})

const emit = defineEmits(['save'])

</script>
<template>
    <v-container class="bg-slate-200 bg-opacity-90 p-5 rounded-lg overflow-auto">
        <h1 class="font-xl text-xl font-bold mb-2 text-center"> Create a new Screen </h1>
        <v-stepper v-model="currentStep">
            <v-stepper-header>
                <template v-for="(step, index) in steps" :key="`header-${index}`">
                    <v-stepper-item :complete="currentStep > index + 1" :value="index + 1">
                        {{ step.title }}
                    </v-stepper-item>
                    <v-divider v-if="index + 1 < steps.length" :key="index"/>
                </template>
            </v-stepper-header>
            <v-stepper-window>
                <v-stepper-window-item v-for="(step, index) in steps" :key="`content-${index}`" :value="index + 1">
                    <v-form :ref="step.form" class="max-h-80 overflow-auto">
                        <p v-if="step.hint">
                            {{ step.hint }}
                        </p>
                        <component :is="step.component" v-bind="step.props" :ref="step.ref"
                                   @update="handleUpdateScreen"/>
                    </v-form>
                </v-stepper-window-item>

            </v-stepper-window>
        </v-stepper>
        <v-row justify="space-between" class="p-5">
            <v-btn @click="handleNextStep(true)" :disabled="currentStep === 1">
                Previous
                <v-icon> mdi-arrow-left</v-icon>
            </v-btn>
            <v-btn @click="handleNextStep()" :color="btnAdvanceColor" :loading="loading">
                {{ btnAdvanceText }}
                <v-icon> mdi-arrow-right</v-icon>
            </v-btn>
        </v-row>
    </v-container>

</template>

<style scoped>

</style>
