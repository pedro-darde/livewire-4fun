<script setup>

import {inject, onMounted, ref, watch} from "vue";
import {VTextarea} from "vuetify/components";

const props = defineProps({
    register: {},
    fieldsConfigs: {
        type: Object
    },
})

const emit = defineEmits(['close', 'save'])
const fieldsComponents = inject('fields')
const screen = inject('screen')

const isShowableField = (columnName) => {
    return !!props.fieldsConfigs[columnName].visible
}

const save = () => {
    console.log(props.register)
    emit('save')
}

const getFieldComponent = (columnName) => {
    return fieldsComponents.value[columnName]
}
</script>

<template>
    <v-dialog>
        <v-container class="bg-slate-200 bg-opacity-90 p-8 rounded-lg overflow-auto">
            <h2 class="text-xl font-bold text-center"> {{ screen.title }}</h2>
            <v-divider></v-divider>
            <v-row justify="center">
                <template v-for="colName in Object.keys(register)">
                    <v-col cols="4" v-if="isShowableField(colName)">
                        <component
                            :is="getFieldComponent(colName).component"
                            v-bind="getFieldComponent(colName).props"
                            v-model="register[colName]"
                        />
                    </v-col>
                </template>
            </v-row>
            <v-row justify="space-between">

                <v-btn append-icon="mdi-cancel" text="Cancel" color="error" variant="elevated">
                </v-btn>
                <v-btn append-icon="mdi-content-save" text="Save" color="primary" variant="elevated" @click="save">
                </v-btn>
            </v-row>
        </v-container>
    </v-dialog>
</template>

<style scoped>

</style>
