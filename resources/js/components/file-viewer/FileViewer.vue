<script setup>
    import useRequest from '../../composables/useRequest'

    const { processRequest } = useRequest()  
    const props = defineProps({
        file: {
            type: Object,
            required: true
        }
    })


    const openFile = () => {
        window.open(props.file.full_name, '_blank')
    }

    const downloadFile = async () => 
    {
        const response = await processRequest(`downloadFile`, 'post', {
            path: props.file.normalized_file_path
        }, null, false)
        const url = window.URL.createObjectURL(new Blob([response.getBody()]));
        const link = document.createElement("a");
        link.href = url;
        link.setAttribute("download", props.file.name);
        document.body.appendChild(link);
        link.click();
        link.remove();
    }

</script>

<template> 
    <v-container class="bg-slate-200 bg-opacity-90 p-8 rounded-lg overflow-auto">
        <v-expansion-panels variant="inset">
            <v-expansion-panel>
                <v-expansion-panel-title expand-icon="mdi-eye">
                  <h2 class="text-h5 text-center my-4"> {{ file.name }}</h2>
                </v-expansion-panel-title>
                <v-expansion-panel-text class="max-h-96 overflow-auto p-5 m-5">
                    <div class="flex flex-row gap-2" @click="downloadFile">
                        <v-btn text="Download"></v-btn>
                        <img :src="file.full_name">
                    </div>
                </v-expansion-panel-text>
            </v-expansion-panel>
        </v-expansion-panels>
    </v-container>
</template>