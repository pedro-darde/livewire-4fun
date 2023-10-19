import {ref} from "vue";

export function usePopup() {
    const open = ref(false)
    const togglePopup = () => {
        open.value = !open.value
    }
    const closePopup = () => {
        console.log('estou fechando o popup')
        open.value = false
    }

    const openPopup = () => {
        open.value = true
    }

    return {
        open,
        togglePopup,
        closePopup,
        openPopup
    }
}
