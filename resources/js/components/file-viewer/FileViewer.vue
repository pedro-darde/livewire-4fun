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
            const link = document.createElement("a");
            link.href = props.file.full_name;
            link.setAttribute("download", props.file.name);
            document.body.appendChild(link);
            link.click();
            link.remove();
    }

    const hasImageExt = (ext) => {
        return ['png', 'jpg', 'jpeg', 'gif'].includes(ext)
    }

</script>

<template>
    <v-container class="bg-white bg-opacity-90 p-8 rounded-lg overflow-auto min-h-[500px]">
      <h2 class="text-h5 text-center my-4"> {{ file.name }} </h2>
        <v-btn @click="downloadFile()"> download </v-btn>
            <iframe v-if="file.extension === 'pdf'" :src="file.full_name" style="width: 100%; height: 100vh"></iframe>
            <img v-if="hasImageExt(file.extension)" :src="file.full_name" :alt="file.name">
    </v-container>
</template>
