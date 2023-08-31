import {ref} from "vue";

export function usePopup() {
    const open = ref(false)
    const togglePopup = () => {
        open.value = !open.value
    }

    const closePopup = () => { open.value = false }

    return {
        open,
        togglePopup,
        closePopup
    }
}
