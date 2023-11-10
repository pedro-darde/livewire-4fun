<script setup>
import {computed, ref} from "vue";
import {DEFAULT_APPOINTMENT_TEXT} from "../../util/constants.js";
import FileViewer from "../file-viewer/FileViewer.vue";
    const props = defineProps({
        appointment: {
            type: Object,
            required: true
        }
    })

    const filesToRemove = ref([])

    const emit = defineEmits(['close', 'save'])

    const note = ref({...props.appointment?.note ?? {
        notes: '<b> thomas vadia </b>',
        evolution: DEFAULT_APPOINTMENT_TEXT,
        appointment_id: props.appointment.id,
        files: []
    }})

    const availableTabs = ['option-1', 'option-2', 'option-3']

    const tab = ref('option-1')

    const isLastTab = computed(() => {
        return tab.value === availableTabs[availableTabs.length - 1]
    })

    const isFirstTab = computed(() => {
        return tab.value === 'option-1'
    })

    const nextTab = (isReturn = false) => {
        let currentTabIndex = availableTabs.indexOf(tab.value)
        if (isReturn) {
            currentTabIndex--
        } else {
            currentTabIndex++
        }
        tab.value = availableTabs[currentTabIndex]
    }

    const newFiles = ref([])

    const addFile = () => {
        if (!newFiles.value.length) {
            return
        }
       const [file] = newFiles.value
       note.value.files.push(file)
       newFiles.value = {}
    }

    const showPreviewOfFile = (file) => {
        if (file.id) {

            file.toggleViewer = true
            return

            window.open(file.full_name, "_blank")
            return
        }
        const base64 = URL.createObjectURL(file)
        window.open(base64, "_blank")
    }

    const removeFile = ({ name, id }) => {
        if (!!id) {
            filesToRemove.value.push(id)
            note.value.files = note.value.files.filter(file => file.id !== id)
            return;
        }
        note.value.files = note.value.files.filter(file => file.name !== name)
    }

    const emitSave = () => {
        emit('save', {
            ... note.value,
            idAppointment: props.appointment.id,
            filesToRemove: filesToRemove.value,
            files: note.value.files.filter(item => !item.id)
        })
    }
</script>

<template>
    <v-container class="bg-slate-200 bg-opacity-90 p-8 rounded-lg overflow-auto">
        <v-card class="w-full m-5">
            <v-toolbar color="primary">
                <div class="flex flex-row justify-between p-3">
                    <h1 class="text-xl font-bold text-center">Notas/Evolução da Consulta </h1>
                </div>
            </v-toolbar>
            <div class="d-flex flex-row w-full">
                <v-tabs v-model="tab" direction="vertical" color="primary">
                    <v-tab value="option-1">
                        <v-icon start>
                            mdi-account
                        </v-icon>
                        Notas
                    </v-tab>
                    <v-tab value="option-2">
                        <v-icon start>
                            mdi-stethoscope
                        </v-icon>
                        Evolução
                    </v-tab>
                    <v-tab value="option-3">
                        <v-icon start>
                            mdi-file
                        </v-icon>
                        Arquivos
                    </v-tab>
                </v-tabs>
                <v-window v-model="tab" class="w-full">
                    <v-window-item value="option-1" class="w-full">
                        <v-row>
                            <v-col cols="12">
                                <div class="min-h-[300px]">
                                    <QuillEditor theme="snow" v-model:content="note.notes" contentType="html"/>
                                </div>
                            </v-col>
                        </v-row>
                    </v-window-item>
                    <v-window-item value="option-2" class="w-full">
                        <v-row>
                            <v-col cols="12">
                                <div class="min-h-[300px]">
                                    <QuillEditor theme="snow" v-model:content="note.evolution" contentType="html"/>
                                </div>
                            </v-col>
                        </v-row>

                    </v-window-item>
                    <v-window-item value="option-3" class="w-full">
                        <v-row align="center" justify="center">
                            <v-col cols="6" class="m-6">
                                <v-file-input variant="solo-filled" v-model="newFiles" label="Adicione os arquivos" :multiple="false" />
                            </v-col>
                            <v-col cols="2">
                                <v-btn icon="mdi-content-save" @click="addFile"></v-btn>
                            </v-col>
                        </v-row>
                        <v-row align="center" justify="center">
                            <v-table>
                                <thead>
                                <tr>
                                    <th> Nome </th>
                                    <th> Arquivo </th>
                                    <th>  # </th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="file in note.files">
                                        <td  class="p-5"> {{ file.name }}</td>
                                        <td  class="p-5"></td>
                                        <td  class="p-5 m-5">
                                            <v-btn icon="mdi-eye" @click="showPreviewOfFile(file)">
                                            </v-btn>
                                            <v-btn icon="mdi-delete" flat @click="removeFile(file)"></v-btn>
                                            <v-dialog v-model="file.toggleViewer">
                                                    <FileViewer v-if="note.id" :file="file"  @close="file.toggleViewer = false"/>
                                            </v-dialog>
                                        </td>
                                    </tr>
                                </tbody>
                            </v-table>
                        </v-row>
                    </v-window-item>
                </v-window>
            </div>

            <v-row justify="space-between" class="p-5">
                <v-col cols="6">
                    <v-btn color="primary" :disabled="isFirstTab" @click="nextTab(true)"> Voltar </v-btn>
                </v-col>
                <v-col cols="6">
                    <v-row justify="end">
                        <v-btn color="primary" :disabled="isLastTab" @click="nextTab()"> Próximo </v-btn>
                    </v-row>

                </v-col>
            </v-row>
        </v-card>
        <v-row justify="space-between">
            <v-btn append-icon="mdi-cancel" text="Cancel" color="error" variant="elevated" @click="$emit('close')">
            </v-btn>
            <v-btn append-icon="mdi-content-save" text="Save" color="primary" variant="elevated" @click="emitSave">
            </v-btn>
        </v-row>
    </v-container>
</template>

<style>

</style>
