<script setup>

import DynamicTable from "../dynamic-table/DynamicTable.vue";
import {useAlert} from "../../composables/useAlert.js";
import {computed, ref, toRefs, watch} from "vue";
import {router, useForm, usePage} from "@inertiajs/vue3";
import useRequest from "../../composables/useRequest.js";
import {Str} from "../../shared/Str";
import ModalCreate from "./ModalCreate.vue";
import {VAutocomplete, VTextField} from "vuetify/components";
import axios from 'axios'

const {fireAlertDelete, toast, fireError} = useAlert()
const {processRequest} = useRequest()
const props = defineProps({
    table: {
        type: String,
        required: true
    },
    columnDefinitions: {
        type: Array,
        required: true
    },
    registers: {
        type: Object,
        required: true
    },
    modelProps: {
        type: Object
    },
    errors: {
        type: Object
    },
    screen: {
        type: Object
    },
})

const page = usePage()
const currentEditRegister = ref(null)

const {props: $props} = toRefs(page)

const computedRegisters = computed(() => {
    return $props.value.registers
})


watch(computedRegisters, (newValue, oldValue) => {
    props.registers.data = newValue?.data ?? []
})

const open = ref(false)

const openPopup = () => {
    open.value = true
}

const closePopup = () => {
    open.value = false
}

const togglePopup = () => {
    open.value = !open.value
}

const fieldConfigs = computed(() => {
    return props.columnDefinitions.reduce((acc, item) => {
        acc[item.columnName] = {...item.extraProps}
        return acc
    }, {})
})

const createRegister = () => {
    const registerKeys = props.columnDefinitions.map(column => column.columnName)
    currentEditRegister.value = {
        ...Object.fromEntries(registerKeys.map(key => [key, ''])),
    }
    openPopup()
}


const handleTableEmits = async ({register, crudOperation}) => {
    if (crudOperation === 'delete') {
        await deleteOperation(register)
        return
    }

    if (crudOperation === 'edit') {
        currentEditRegister.value = {...register}
        togglePopup()
        return
    }
}
const deleteOperation = async (register) => {
    const response = await fireAlertDelete()
    if (response.isConfirmed) {
        await axios.delete(`/${props.table}/${register.id}`)
        toast('Registro eliminado correctamente')
        refreshRegisters()
    }
}


const errors = ref({})
const saveRegister = async () => {

    const url = `/api/dynamic/save`
    const response = await (processRequest(url, 'post', {
        register: currentEditRegister.value,
        table: props.table,
    }))

    togglePopup()
    refreshRegisters();
}

const refreshRegisters = () => {
    router.reload({
        only: ['registers'],
        headers: {
            'X-Inertia': true,
            "x-inertia-partial-component": page.component,
        }
    })
}

const routineTitle = computed(() => {
    return Str.capitalize(props.table)
})


watch(open, (value) => {
    if (!value) {
        setTimeout(() => {
            currentEditRegister.value = {}
        }, 500)
    }
})
</script>

<template>
    <div class="p-5 flex flex-row justify-between">
        <h1 class="text-xl font-bold"> {{ routineTitle }}</h1>
        <button class="font-bold border-black rounded-lg bg-indigo-500 p-2 text-center" @click="createRegister">
            <i class="fas fa-plus"></i> {{ routineTitle }}
        </button>
    </div>
    <div class="flex flex-col">
        <DynamicTable
            :column-definitions="props.columnDefinitions"
            :table="props.table"
            :registers="props.registers"
            :model-props="modelProps"
            @crud-event="handleTableEmits"
        >

        </DynamicTable>
    </div>

    <ModalCreate
        v-model="open"
        :fields-configs="fieldConfigs"
        :register="currentEditRegister"
        @save="saveRegister"
    />
</template>

<style scoped>

</style>
