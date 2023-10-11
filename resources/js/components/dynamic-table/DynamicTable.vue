<template>
    <div class="p-5">
        <div class="grid grid-cols-3 gap-2 p-2 mb-2">
            <div :class="[showOrderOptions ? 'col-span-2': 'col-span-3']">
                <v-text-field type="text"
                              v-model="searchString"
                              label="Search"
                              variant="solo-filled"
                >
                </v-text-field>
            </div>
            <v-row v-if="showOrderOptions">
                <v-col cols="8">
                    <v-autocomplete v-model="columnOrder" id="columnOrder" name="columnOrder" item-value="value"
                                    item-title="text" :items="columnOrderOptions" label="Column Order"
                                    variant="solo-filled">
                        <template v-slot:item="{ props, item }">
                            <v-list-item
                                v-bind="props"
                                :title="item?.title"
                            >
                                <template v-slot:subtitle>
                                    {{ item.value }}
                                </template>

                            </v-list-item>
                        </template>
                    </v-autocomplete>
                </v-col>
                <v-col cols="4">
                    <v-autocomplete v-model="orderDirection" id="orderDirection" name="orderDirection"
                                    variant="solo-filled" :items="['asc', 'desc']" label="Direction">
                    </v-autocomplete>
                </v-col>

            </v-row>
        </div>
        <template v-if="useApiPagination">
            <v-data-table-server
                v-model:items-per-page="registers.per_page"
                :headers="tableHeaders"
                :items-length="registers.total"
                :items="registers.data"
                :loading="requestingItems"
                :search="searchString"
                class="elevation-1"
                item-value="name"
                @update:options="loadMoreItens"
                noDataText="Nenhum item encontrado"
                itemsPerPageText="Registros por página"
            >
                <template v-slot:item="{ item, props }">
                    <tr>
                        <template v-for="key in Object.keys(item.columns)">
                            <td
                                v-if="hasColOnHeader(key)"
                                class="text-center"
                            >
                                {{ item.columns[key] }}
                            </td>
                        </template>
                        <td v-for="action in allowedCrudActions">
                            <v-icon
                                @click="emitCrudEvent(action, item.columns)"
                            >
                                {{ getIconClassByCrudOption(action) }}
                            </v-icon>
                        </td>
                    </tr>

                </template>
            </v-data-table-server>
        </template>
        <template v-else>
            <v-table fixed-header>
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th v-for="column in columnDefinitions" :id="column.columnName" scope="col"
                        class="px-6 py-3 text-center"
                        :style="{ width: tableHeaderColSizes[column.columnName] }"
                    >
                        {{ column.columnDescription }}
                    </th>
                    <th :colspan="getColspanByAllowedCrudActions"></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="register in filteredRegisters">
                    <template v-for="key in Object.keys(register)">
                        <td
                            v-if="hasColOnHeader(key)"
                            class="text-center"
                        >
                            {{ register[key] }}
                        </td>
                    </template>
                    <td v-for="action in allowedCrudActions">
                        <v-icon
                            @click="emitCrudEvent(action, register)"
                        >
                            {{ getIconClassByCrudOption(action) }}
                        </v-icon>
                    </td>
                </tr>
                </tbody>
            </v-table>
            <nav class="w-full">
                <ul class="flex flex-row items-center justify-center gap-2">
                    <li v-for="link in registers.links">
                        <a :href="link.url" v-html="link.label" :class="['' +
                                 'flex items-center justify-center px-3 h-8 ml-0 leading-tight text-gray-500 border border-gray-300 rounded-full hover:bg-gray-100 hover:text-gray-700',
                                 link.active ? 'font-bold text-black bg-slate-300' : 'bg-white'
                                 ]"> </a>
                    </li>
                </ul>
            </nav>
        </template>
    </div>
</template>

<script setup>
import {computed, onMounted, ref} from "vue"
import {VDataTableServer} from 'vuetify/labs/VDataTable'
import _ from 'lodash'

const loading = ref(false)

const {table, columnDefinitions, registers, modelProps} = defineProps({
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
    useApiPagination: {
        type: Boolean,
        default: false
    },
    requestingItems: {
        type: Boolean,
        default: false
    },
    showOrderOptions: {
        type: Boolean,
        default: true
    }
})

const searchString = ref('')
const columnOrder = ref('')
const orderDirection = ref('asc')

const columnOrderOptions = ref(columnDefinitions.map(column => {
    return {
        value: column.columnName,
        text: column.columnDescription,
        extra: column
    }
}))


const filteredRegisters = computed(() => {
    return registers.data.filter(register => {
        return Object.keys(register).some(key => {
            return register[key]?.toString()?.toLowerCase()?.includes(searchString.value.toLowerCase())
        })
    }).map(register => {
        return Object.keys(register).reduce((acc, key) => {
            const fieldExtraInfo = columnDefinitions.find(column => column.columnName === key)?.extraProps
            if (fieldExtraInfo) {
                // const isRelationField = fieldExtraInfo.relations?.every(relation => !!relation.field?.id && !!relation.screen?.id);
                acc[key] = formatFieldValueByType(fieldExtraInfo.type, register[key])
                return acc
            }
            return acc
        }, {})
    })
})

const tableHeaderColSizes = ref({})

const tableHeaders = computed(() => {
    const defaultHeaders = columnDefinitions.map(column => {
        return {
            title: column.columnDescription,
            key: column.columnName,
            sortable: true,
            align: 'center',
            width: tableHeaderColSizes.value[column.columnName]
        }
    });

    defaultHeaders.push({
        title: 'Actions',
        key: 'actions',
        sortable: false,
        align: "center",
        colspan: allowedCrudActions.value.length
    })

    return defaultHeaders
})

const withThrottleLoad = _.throttle(({page, itemsPerPage, sortBy}) => {
    emit('loadMore', {
        page,
        itemsPerPage,
        sortBy,
        searchString: searchString.value,
    })
}, 1000);

const loadMoreItens = ({page, itemsPerPage, sortBy}) => {
    if (searchString.value) {
        withThrottleLoad({page, itemsPerPage, sortBy})
    } else {
        emit('loadMore', {
            page,
            itemsPerPage,
            sortBy,
            searchString: searchString.value,
        })
    }
}
const defineTableHeaderColSizesPercentagesByColContent = () => {
    const columnNameAndAvarages = {}

    columnDefinitions.forEach(column => {
        columnNameAndAvarages[column.columnName] =
            filteredRegisters.value.reduce((acc, register) => {
                return acc + register[column.columnName]?.toString()?.length
            }, 0) / filteredRegisters?.value?.length
    })

    const totalAvarage = Object.values(columnNameAndAvarages).reduce((acc, value) => acc + value, 0)

    const colsPercentageOverAvarage = Object.keys(columnNameAndAvarages).reduce((acc, columnName) => {
        acc[columnName] = (columnNameAndAvarages[columnName] / totalAvarage) * 100
        return acc
    }, {})

    tableHeaderColSizes.value = Object.keys(colsPercentageOverAvarage).reduce((acc, columnName) => {
        acc[columnName] = `${colsPercentageOverAvarage[columnName]}%`
        return acc
    }, {})
}

onMounted(() => {
    defineTableHeaderColSizesPercentagesByColContent()
    console.log(registers)
})

const formatFieldValueByType = (type, value) => {
    switch (type) {
        case "text":
        case "numeric":
            return value
        case "date":
            return new Date(value).toLocaleDateString();
        case "checkbox":
            return value ? 'Yes' : 'No'
        default:
            return value
    }
}


const {
    crudActions
} = modelProps

const allowedCrudActions = computed(() => {
    return Object.keys(crudActions).filter(key => crudActions[key])
})

const getColspanByAllowedCrudActions = computed(() => {
    return allowedCrudActions.value.length
});

const emit = defineEmits(['crudEvent', 'loadMore'])
const hasColOnHeader = (col) => {
    return columnDefinitions.find(column => column.columnName === col)
}

const getWidthColByFieldLength = (value) => {
    return `${value.toString().length * 10}px`
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
        case 'update':
            return 'mdi-pencil-box'
        case 'delete':
            return 'mdi-trash-can'
    }
}
const onEditClick = (id) => {
    console.log(id)
}
</script>
