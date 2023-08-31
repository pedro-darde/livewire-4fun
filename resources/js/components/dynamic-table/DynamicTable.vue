<template>
    <div class="p-5">
        <div class="grid grid-cols-3 gap-2 p-2 mb-2">
            <div class="col-span-2">
                <label for="searchString" class="font-bold mr-2">Search</label>
                <input type="text"
                       v-model="searchString"
                       id="searchString"
                       name="searchString"
                       class="border border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>
            <div >
                <label for="columnOrder" class="font-bold mr-2">Order by</label>
                <select v-model="columnOrder" id="columnOrder" name="columnOrder" class="border border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="">Select a column</option>
                    <option v-for="column in columnOrderOptions" :value="column.value">{{ column.text }}</option>
                </select>
                <select v-model="orderDirection" id="orderDirection" name="orderDirection" class="border border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="asc">Ascending</option>
                    <option value="desc">Descending</option>
                </select>
            </div>
        </div>
        <table class="w-full">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr >
                <th v-for="column in columnDefinitions" :id="column.columnName" scope="col" class="px-6 py-3"> {{ column.columnDescription }}</th>
                <th :colspan="getColspanByAllowedCrudActions"></th>
            </tr>
            </thead>
            <tbody>
                <tr v-for="register in filteredRegisters">
                    <template  v-for="key in Object.keys(register)">
                        <td
                            v-if="hasColOnHeader(key)"
                            class="text-center"
                            >
                                {{ register[key]}}
                        </td>
                    </template>
                    <td v-for="action in allowedCrudActions">
                       <i :class="getIconClassByCrudOption(action)" style="cursor: pointer" @click="emitCrudEvent(action, register)"></i>
                    </td>
                </tr>
            </tbody>
        </table>
        <nav class="w-full">
            <ul  class="flex flex-row items-center justify-center gap-2">
                <li v-for="link in registers.links">
                    <a :href="link.url" v-html="link.label" :class="['' +
                     'flex items-center justify-center px-3 h-8 ml-0 leading-tight text-gray-500 border border-gray-300 rounded-full hover:bg-gray-100 hover:text-gray-700',
                     link.active ? 'font-bold text-black bg-slate-300' : 'bg-white'
                     ]"> </a>
                </li>
            </ul>
        </nav>

    </div>
</template>

<script setup>
import {computed, onMounted, ref} from "vue"
    const { table,  columnDefinitions , registers, modelProps } = defineProps({
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
        }
    })

    const searchString = ref('')
    const columnOrder = ref('')
    const orderDirection = ref('asc')

    const columnOrderOptions = ref(columnDefinitions.map(column => {
        return {
            value: column.columnName,
            text: column.columnDescription
        }
    }))

    const filteredRegisters = computed(() => {
        return registers.data.filter(register => {
            return Object.keys(register).some(key => {
                return register[key].toString().toLowerCase().includes(searchString.value.toLowerCase())
            })
        })
    })

    const { crudActions } = modelProps

    const allowedCrudActions = computed(() => {
        return Object.keys(crudActions).filter(key => crudActions[key])
    })

    const getColspanByAllowedCrudActions = computed(() => {
        return allowedCrudActions.value.length
    });

    const emit = defineEmits(['crudEvent'])
    const hasColOnHeader = (col) => {
        return columnDefinitions.find(column => column.columnName === col)
    }

    const emitCrudEvent = (crudOperation, register) => {
        emit('crudEvent', {
            crudOperation,
            register
        })
    }

    const getIconClassByCrudOption = (crudOption) => {
        switch (crudOption) {
            case 'edit':
                return 'fas fa-edit'
            case 'delete':
                return 'fas fa-remove'
            case 'create':
                return 'fas fa-plus'
        }
    }
    const onEditClick = (id) => {
        console.log(id)
    }
</script>
