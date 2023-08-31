<script setup>

import DynamicTable from "../components/dynamic-table/DynamicTable.vue";
import Layout from "../components/layout/Layout.vue";
import {useAlert} from "../composables/useAlert.js";
import {computed, ref, toRefs, watch} from "vue";
import {usePopup} from "../composables/usePopup.js";
import Modal from "../components/modal/Modal.vue";
import {router, useForm, usePage} from "@inertiajs/vue3";
import useRequest from "../composables/useRequest.js";
import {Str} from "../shared/Str";

const { fireAlertDelete, toast, fireError } = useAlert()
const { open, togglePopup, closePopup } = usePopup()
const { processRequest } = useRequest()
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
    }
})

const page = usePage()
const currentEditRegister = ref(null)

const { props: $props } = toRefs(page)

const computedRegisters = computed(() => {
    return $props.value.registers
})


watch(computedRegisters, (newValue, oldValue) => {
    props.registers.data = newValue?.data ?? []
})

const createRegister = () => {
    const registerKeys = props.columnDefinitions.map(column => column.columnName)
    currentEditRegister.value = {
        ... Object.fromEntries(registerKeys.map(key => [key, ''])),
    }
    togglePopup()
}

const handleTableEmits = async ({ register, crudOperation }) => {
    if (crudOperation === 'delete') {
        await deleteOperation(register)
        return
    }

    if (crudOperation === 'edit') {
        currentEditRegister.value = { ... register }
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
    if (currentEditRegister.value?.id) {
        await axios.put(`/${props.table}/${currentEditRegister.value.id}`, currentEditRegister.value)
        console.log('fiz o update')
        toast('Registro actualizado correctamente')
        currentEditRegister.value = null
    } else {
        const response = await processRequest(axios.post(`/${props.table}`, currentEditRegister.value))
        if (response.hasErrors()) {
            const errors = response.getErrors(true)
            fireError('An error has ocurred', errors)
            return;
        }
        toast('Registro creado correctamente')
    }
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

const modalTitle = computed(() => {
    return currentEditRegister.value?.id ? `Editar ${props.table} #${currentEditRegister.value.id}` : 'Criar'
})

const routineTitle = computed(() => {
    return Str.capitalize(props.table)
})

</script>

<template>
    <Layout>
        <div class="p-5 flex flex-row justify-between">
            <h1 class="text-xl font-bold" > {{ routineTitle }}</h1>
            <button class="font-bold border-black rounded-lg bg-indigo-500 p-2 text-center" @click="createRegister">
                <i class="fas fa-plus"></i>   {{ routineTitle }}
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

        <Modal @close="closePopup()" v-if="open">
            <template #title>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    {{ modalTitle }}
                </h3>
            </template>
            <template #body>
               <div class="p-5">
                     <div class="flex flex-col gap-2">
                         <template v-for="column in columnDefinitions">
                             <div class="flex flex-col" v-if="column.allowEdit">
                                 <label :for="column.columnName" class="text-sm font-semibold text-gray-700 dark:text-gray-400">{{ column.columnDescription }}</label>
                                 <input
                                     :id="column.columnName"
                                     type="text"
                                     class="px-4 py-2 border rounded-md dark:bg-gray-800 dark:text-gray-300"
                                     v-model="currentEditRegister[column.columnName]"
                                 />
                             </div>
                         </template>
                     </div>
               </div>
            </template>
            <template #button-save>
                <button
                    data-modal-hide="defaultModal"
                    type="button"
                    @click="saveRegister()"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    Save
                </button>
            </template>
        </Modal>
    </Layout>
</template>

<style scoped>

</style>
