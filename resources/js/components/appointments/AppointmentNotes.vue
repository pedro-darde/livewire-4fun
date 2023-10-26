<script setup>
    import {ref} from "vue";
    import {DEFAULT_APPOINTMENT_TEXT} from "../../util/constants.js";

    const props = defineProps({
        appointment: {
            type: Object,
            required: true
        }
    })

    const emit = defineEmits(['close', 'save'])

    const note = ref({...props.appointment?.note ?? {
        notes: '<b> thomas vadia </b>',
        evolution: DEFAULT_APPOINTMENT_TEXT,
        appointment_id: props.appointment.id,
        files: []
    }})
</script>

<template>
    <v-container class="bg-slate-200 bg-opacity-90 p-8 rounded-lg overflow-auto">
        <h2 class="text-xl font-bold text-center"> Notas/Evolução da Consulta </h2>
        <v-divider></v-divider>

        <v-row>
            <v-col cols="12">
                <b> Notas da consulta </b>:
                <div class="min-h-[300px]">
                    <QuillEditor theme="snow" v-model:content="note.notes" contentType="html"/>
                </div>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="12">
                <b> Evolução da consulta </b>:
                <div class="min-h-[300px]">
                    <QuillEditor theme="snow" v-model:content="note.evolution" contentType="html"/>
                </div>
            </v-col>
        </v-row>

        <v-row justify="space-between">

            <v-btn append-icon="mdi-cancel" text="Cancel" color="error" variant="elevated" @click="$emit('close')">
            </v-btn>
            <v-btn append-icon="mdi-content-save" text="Save" color="primary" variant="elevated">
            </v-btn>
        </v-row>
    </v-container>
</template>

<style>

</style>
