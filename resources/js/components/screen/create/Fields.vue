<script setup>

import {computed, ref, watch} from "vue";

const props = defineProps({
    defaultRules: {
        type: Array
    },
    screens: {
        type: Array
    }
})

const fields = ref([
    {
        config: {
            "name": "",
            "label": "",
            "type": "",
            "mask": "",
            "placeholder": "",
            "options": [],
            "default": "",
            "description": "",
            "required": false,
            "rules": [],
            "disabled": false,
            "visible": true,
            "searchUrl": "",
            "multiple": false,
            "useAjaxToLoadOptions": false,
            "useIndex": false,
            optionText: "",
            optionValue: "id",
            relations: [{
                screen: '',
                field: ''
            }]
        }
    }
])

const fieldTypeOptions = [
    'numeric',
    'text',
    'textarea',
    'select',
    'checkbox',
    'radio',
    'date',
    'time',
    'datetime',
    'file',
    'image',
    'password',
    'email',
    'url',
    'color',
    'range',
    'hidden',
]

const emit = defineEmits(['update'])

const hasToShowOptionsForSelect = ({config}) => {
    return ['select'].includes(config.type)
}

const addField = () => {
    fields.value.push({
        config: {
            "name": "",
            "label": "",
            "type": "",
            "mask": "",
            "placeholder": "",
            "options": [],
            "default": "",
            "description": "",
            "required": false,
            "rules": [],
            "disabled": false,
            "visible": true,
            "searchUrl": "",
            "multiple": false,
            "useAjaxToLoadOptions": false,
            optionText: "",
            optionValue: "id",
            useIndex: false,
            relations: [{
                screen: '',
                field: ''
            }]
        }
    })
}

const isLastField = (index) => {
    return index === fields.value.length - 1
}

const canShowRemove = (item) => {
    return fields.value.indexOf(item) !== 0
}

const removeField = (index) => {
    fields.value.splice(index, 1)
}

const rules = (field) => {
    return {
        name: [
            v => !!v || 'Name is required',
        ],
        label: [
            v => !!v || 'Label is required',
        ],
        type: [
            v => !!v || 'Type is required',
        ],
        placeholder: [
            v => !!v || 'Placeholder is required',
        ],
        options: [
            v => !!v?.length || 'Options is required',
        ],
        default: [
            v => !!v || 'Default is required',
        ],
        description: [
            v => !!v || 'Description is required',
        ],
        rules: [
            v => !!v?.length || 'Rules is required',
        ],
        searchUrl: [
            v => (field.config.type === "select" && field.config.useAjaxToLoadOptions) && !!v || 'Search Url is required',
        ],
    }
}

watch(fields, (value) => {
    emit('update', {prop: 'fields', value})
    fields.value = value
    console.log("value changed", value)
}, {deep: true})

const getScreenFields = (screen) => {
    return screen.fields
}
</script>

<template>
    <v-row>
        <v-col cols="12">
            <v-expansion-panels variant="inset">
                <v-expansion-panel v-for="(field, index) in fields">
                    <v-expansion-panel-title>
                        <v-col cols="12">
                                  <span class="text-h5">
                                      Field {{ field.config.name }}
                                  </span>
                        </v-col>
                    </v-expansion-panel-title>
                    <v-expansion-panel-text>
                        <v-container>
                            <v-row align="center" justify="center">
                                <v-col cols="10">

                                    <v-row>
                                        <v-col cols="8">
                                            <v-text-field variant="solo-filled"
                                                          label="Field Name"
                                                          v-model="field.config.name"
                                                          hint="Whatever you put here is gonna be parsed to lower case, this is the column name on the table"
                                                          persistent-hint
                                                          :rules="rules(field).name"
                                            />
                                        </v-col>
                                        <v-col cols="4">
                                            <v-select variant="solo-filled" label="Type" v-model="field.config.type"
                                                      :items="fieldTypeOptions" :rules="rules(field).type"/>
                                        </v-col>
                                    </v-row>
                                    <v-row v-if="hasToShowOptionsForSelect(field)">
                                        <v-col cols="2">
                                            <v-checkbox
                                                label="Use ajax to load options"
                                                v-model="field.config.useAjaxToLoadOptions"

                                            />
                                        </v-col>
                                        <v-col cols="2">
                                            <v-checkbox
                                                label="Multiple"
                                                v-model="field.config.multiple"
                                            />
                                        </v-col>
                                        <v-col cols="4">
                                            <v-combobox
                                                v-model="field.options"
                                                :chips="true"
                                                clearable
                                                multiple
                                                filled
                                                rounded
                                                append-icon=""
                                                variant="solo-filled"
                                                label="Options"
                                                :persistent-hint="true"
                                                hint="Follow this template (value;label): Ex: 1;Option 1"
                                                :rules="rules(field).options"
                                                :disabled="field.config.useAjaxToLoadOptions"
                                            >
                                                <template v-slot:selection="{ attrs, item, select, selected }">
                                                    <v-chip
                                                        small
                                                        v-bind="attrs"
                                                        :input-value="selected"
                                                        close
                                                        @click="select"

                                                    >
                                                        {{ item }}
                                                    </v-chip>
                                                </template>
                                            </v-combobox>
                                        </v-col>

                                    </v-row>
                                    <v-row v-if="field.config.useAjaxToLoadOptions">
                                        <v-col cols="4">
                                            <v-text-field
                                                :disabled="!field.config.useAjaxToLoadOptions"
                                                label="Url to search for options, put the table where the items will be. Ex: /{tableName}"
                                                v-model="field.config.searchUrl"
                                                variant="solo-filled"
                                                :rules="rules(field).searchUrl"
                                            />
                                        </v-col>
                                        <v-col cols="4">
                                            <v-text-field
                                                label="The select option Text"
                                                v-model="field.config.optionText"
                                                variant="solo-filled"
                                            />
                                        </v-col>
                                        <v-col cols="4">
                                            <v-text-field
                                                label="The select option value"
                                                v-model="field.config.optionValue"
                                                variant="solo-filled"
                                            />
                                        </v-col>
                                    </v-row>
                                    <v-row>
                                        <v-col cols="4">
                                            <v-text-field variant="solo-filled"
                                                          label="Label" v-model="field.config.label"
                                                          hint="The label of the field"
                                                          persistent-hint
                                                          :rules="rules(field).label"
                                            />
                                        </v-col>
                                        <v-col cols="4">
                                            <v-text-field variant="solo-filled"
                                                          label="Placeholder" v-model="field.config.placeholder"
                                                          hint="The placeholder of the field" persistent-hint
                                                          :rules="rules(field).placeholder"/>
                                        </v-col>
                                        <v-col cols="4">
                                            <v-text-field variant="solo-filled"
                                                          label="Mask" v-model="field.config.mask"
                                                          hint="The mask of the field, follow this template (##.###.###-##)"
                                                          persistent-hint/>
                                        </v-col>
                                    </v-row>
                                    <v-row>
                                        <v-col cols="3">
                                            <v-text-field variant="solo-filled"
                                                          label="Default Value" v-model="field.config.default"
                                                          hint="The default value of the field" persistent-hint
                                                          :rules="rules(field).default"/>
                                        </v-col>
                                        <v-col cols="3">
                                            <v-autocomplete
                                                :items="props.defaultRules"
                                                v-model="field.config.rules"
                                                :multiple="true"
                                                item-title="name"
                                                item-value="name"
                                                label="Select a rule"
                                                :chips="true"
                                                clearable
                                                hint="The rules of the field"
                                                persistent-hint
                                                variant="solo-filled"
                                            >
                                                <template v-slot:item="{ props, item }">
                                                    <v-list-item
                                                        v-bind="props"
                                                        :title="item?.title"
                                                    ></v-list-item>
                                                </template>
                                            </v-autocomplete>
                                        </v-col>
                                        <v-col cols="2">
                                            <v-checkbox
                                                label="Required"
                                                v-model="field.config.required"
                                            />
                                        </v-col>
                                        <v-col cols="2">
                                            <v-checkbox
                                                label="Disabled"
                                                v-model="field.config.disabled"
                                            />
                                        </v-col>
                                        <v-col cols="2">
                                            <v-checkbox
                                                label="Create index"
                                                v-model="field.config.useIndex"
                                            />
                                        </v-col>
                                    </v-row>
                                    <v-row v-for="relation of field.config.relations">
                                        <v-col cols="5">
                                            <v-autocomplete
                                                v-model="relation.screen"
                                                :items="screens"
                                                item-title="name"
                                                item-value="id"
                                                label="Select a screen"
                                                variant="solo-filled"
                                                return-object
                                            >
                                                <template v-slot:item="{ props, item }">
                                                    <v-list-item
                                                        v-bind="props"
                                                        :title="item?.title"
                                                    >
                                                    </v-list-item>
                                                </template>
                                            </v-autocomplete>
                                        </v-col>
                                        <v-col cols="5">
                                            <v-autocomplete
                                                :disabled="!relation.screen"
                                                v-model="relation.field"
                                                :items="getScreenFields(relation.screen)"
                                                item-title="name"
                                                item-value="id"
                                                label="Select a field"
                                                variant="solo-filled"
                                                return-object
                                            >
                                                <template v-slot:item="{ props, item }">
                                                    <v-list-item
                                                        v-bind="props"
                                                        :title="item?.title"
                                                    ></v-list-item>
                                                </template>
                                            </v-autocomplete>
                                        </v-col>
                                        <v-col cols="2">
                                            <v-btn text="Add" icon="mdi-plus"/>
                                        </v-col>
                                    </v-row>
                                    <v-row>
                                        <v-col cols="12">
                                            <v-textarea variant="solo-filled"
                                                        label="Description" v-model="field.config.description"
                                                        hint="The description of the field" persistent-hint/>
                                        </v-col>
                                    </v-row>
                                </v-col>
                                <v-col cols="2">
                                    <v-btn text="Add" @click="addField" v-if="isLastField(index)"></v-btn>
                                    <v-btn text="Remove" @click="removeField(index)"
                                           v-if="canShowRemove(field)"></v-btn>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-expansion-panel-text>
                </v-expansion-panel>
            </v-expansion-panels>
        </v-col>
    </v-row>
</template>

<style scoped>

</style>
