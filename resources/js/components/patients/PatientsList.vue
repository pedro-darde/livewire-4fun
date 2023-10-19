<script setup>
import { defineComponent, ref } from "vue";
import DynamicTable from "../dynamic-table/DynamicTable.vue";
import { useAlert } from "../../composables/useAlert.js";
import { router } from '@inertiajs/core'
import _ from 'lodash'
import { axiosInstance } from '../../shared/axios'

const {
    fireAlertDelete,
    toast,
    fireError
} = useAlert()
const props = defineProps({
    patients: {
        type: Object,
        required: true
    }
})

const columnDefinitions = [
    {
        columnName: "id",
        columnDescription: "ID",
        extraProps: {}
    },
    {
        columnName: "name",
        columnDescription: "Nome",
        extraProps: {}
    },
    {
        columnName: "last_name",
        columnDescription: "Sobrenome",
        extraProps: {}
    },
    {
        columnName: "nickname",
        columnDescription: "Apelido",
        extraProps: {}
    },
    {
        columnName: "email",
        columnDescription: "E-mail",
        extraProps: {}
    },
    {
        columnName: "phone",
        columnDescription: "Telefone",
        extraProps: {}
    },
    {
        columnName: "rg",
        columnDescription: "RG",
        extraProps: {}
    },
    {
        columnName: "cpf",
        columnDescription: "CPF",
        extraProps: {}
    },
    {
        columnName: "birth_date",
        columnDescription: "Data de Nascimento",
        extraProps: {}
    }
]

const modelProps = {
    crudActions: {
        'update': true,
        'delete': true
    }
}


const emit = defineEmits(['refresh'])
const registers = ref(props.patients)
const onDelete = async (register) => {
    const { isConfirmed } = await fireAlertDelete()

    if (isConfirmed) {
        try {
            await axiosInstance.delete(`/patients/${register.id}`)
            toast('Paciente excluído com sucesso', 'success')
            console.log(registers)
            loadMoreItens({
                page: registers.value.page,
                itemsPerPage: registers.value.per_page,
                sortBy: '',
                searchString: lastSearchString.value
            })
        } catch (e) {
            fireError(e)
        }
    }
}

const lastSearchString = ref('')

const onUpdate = (register) => {
    router.visit(`/patients/${register.id}`)
}

const emitCrudEvent = ({ crudOperation, register }) => {
    if (crudOperation === 'delete') {
        onDelete(register)
    } else if (crudOperation === 'update') {
        onUpdate(register)
    }
}

const requesting = ref(false)


const requestPatients = async ({ page, itemsPerPage, sortBy, searchString }) => {
    const [{
        key,
        order
    }] = sortBy.length ? sortBy : [{ key: '', order: '' }]
    requesting.value = true
    const response = await axiosInstance.get('api/patients/loadMore', {
        params: {
            page,
            per_page: itemsPerPage,
            search: searchString,
            order_by: key,
            order_direction: order
        }
    })

    const patients = response.data.patients

    if (itemsPerPage === -1) {
        registers.value = {
            per_page: -1,
            total: patients.length,
            data: patients,

        }
    } else {
        registers.value = patients
    }
    requesting.value = false
}
const loadMoreItens = async ({ page, itemsPerPage, sortBy, searchString }) => {
    lastSearchString.value = searchString
    await requestPatients({ page, itemsPerPage, sortBy, searchString })
}

</script>

<template>
    <div>
        <DynamicTable :registers="registers" :column-definitions="columnDefinitions" table="patient"
            :model-props="modelProps" @crud-event="emitCrudEvent" :use-api-pagination="true" @load-more="loadMoreItens"
            :requesting-items="requesting" :show-order-options="false" />
    </div>
</template>

<style scoped></style>
