<script setup>
import {onMounted, ref} from "vue";
const screen = ref({
  title: "",
  url: "",
  table: "",
  icon: "",
  description: "",
  pk_name: "",
  fields: []
})

const icons = ref({
  data: [],
  page: 0,
  hasMore: true,
  total: 0,
  perPage: 100
})

const autocompleteSearch = ref('')
const loadIcons = async (filter = '', keepPage = true) => {
  if (!icons.value.hasMore) return;
  autocompleteSearch.value = filter
  if (!keepPage) {
    icons.value.page = 0
  } else {
    icons.value.page += 1
  }
  const { data } = await axios.get( `/api/icons?page=${icons.value.page}`, {
    params: {
      filter: autocompleteSearch.value
    }
  })
  icons.value.page = +data.page
  icons.value.data.push(...Object.values(data.data))
}

onMounted(async () => {
  const { data } = await axios.get('/api/icons')
  icons.value = data
})

const endIntersect = (endReach) => {
  if (endReach) {
    loadIcons( '', true)
  }
}

const rules = {
  title: [
    v => !!v || 'Title is required',
  ],
  url: [
    v => !!v || 'Url is required',
  ],
  table: [
    v => !!v || 'Table is required',
  ],
  icon: [
    v => !!v.length || 'Icon is required',
  ],
  pk_name: [
    v => !!v || 'Primary Key is required',
  ]
}
</script>

<template>
  <v-row>
    <v-col cols="6">
      <v-text-field label="Title" v-model="screen.title" variant="solo-filled" clearable :rules="rules.title" />
    </v-col>
    <v-col cols="3">
      <v-text-field label="Url" v-model="screen.url" variant="solo-filled" prepend-inner-icon="mdi-link" :rules="rules.url" />
    </v-col>
    <v-col cols="3">
      <v-text-field label="Table" v-model="screen.table" variant="solo-filled" prepend-inner-icon="mdi-table" :rules="rules.table"/>
    </v-col>
  </v-row>
  <v-row>
    <v-col cols="8">
      <v-autocomplete
          :items="icons.data"
          v-model="screen.icon"
          item-text="name"
          item-value="id"
          label="Select an icon"
          @input.native="loadIcons($event.target.value, false)"
          :chips="true"
          variant="solo-filled"
          :rules="rules.icon"
      >
        <template v-slot:chip="{ props, item }">
          <v-chip
              v-bind="props"
              :prepend-icon="`mdi-${item?.value}`"
              :text="item.value"
          ></v-chip>
        </template>

        <template v-slot:item="{ props, item }">
          <v-list-item
              v-bind="props"
              :prepend-icon="`mdi-${item?.value}`"
              :title="item?.value"
          ></v-list-item>
        </template>
        <template v-slot:append-item>
          <div v-intersect="endIntersect" />
        </template>
      </v-autocomplete>
    </v-col>
    <v-col cols="4">
      <v-text-field label="Primary Key" v-model="screen.pk_name" variant="solo-filled" prepend-inner-icon="mdi-key" :rules="rules.pk_name"/>
    </v-col>
  </v-row>
  <v-row>
    <v-col cols="12">
      <v-textarea v-model="screen.description" variant="solo-filled" label="Description"/>
    </v-col>
  </v-row>
</template>

<style scoped>

</style>
