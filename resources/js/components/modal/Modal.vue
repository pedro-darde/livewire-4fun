<script setup>
import {defineProps, defineEmits, onMounted, onUnmounted} from 'vue'

const props = defineProps({
  showHeaderButtons: {
    type: Boolean,
    default: true
  },
  closeOnEsc: {
    default: true
  },
  closeOnClickOutside: {
    default: true
  }
})
const emit = defineEmits(['close'])

const keyDownEscEvent = (e) => {
  if (e.key === 'Escape' && props.closeOnEsc) {
    emit('close')
  }
}

const clickOutSideEvent = (e) => {
  if (e.target.id === 'defaultModal' && props.closeOnClickOutside) {
    emit('close')
  }
}

onMounted(() => {
  document.addEventListener('keydown', keyDownEscEvent)
  document.addEventListener('click', clickOutSideEvent)
})

onUnmounted(() => {
  document.removeEventListener('keydown', keyDownEscEvent)
  document.removeEventListener('click', clickOutSideEvent)
})

</script>

<template>
  <div id="defaultModal" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full bg-slate-300 bg-opacity-20 flex flex-row items-center justify-center">
    <div class="relative w-full max-w-2xl max-h-full" id="modal-body" >
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
        <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
            <slot name="title">
              <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                Modal title
              </h3>
            </slot>
        </div>
        <slot name="body">
          <div class="relative p-4 h-[calc(100%-2rem)] overflow-y-auto">
            <p class="text-sm text-gray-700 dark:text-gray-400">
              Lorem ipsum dolor sit amet consectetur adipisicing elit. Excepturi
              eligendi similique veniam modi dolorum at?
            </p>
          </div>
        </slot>
        <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
            <slot name="button-save">
                <button data-modal-hide="defaultModal" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        Default Save Button
                </button>
            </slot>
            <slot name="button-close">
                <button data-modal-hide="defaultModal" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600" @click="$emit('close')">Close</button>
            </slot>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>
