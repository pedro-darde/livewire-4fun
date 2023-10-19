import Swal from "sweetalert2";
export function useAlert() {
    const fire = async (title, text, icon = 'info') => {
        return await Swal.fire({
            title,
            text: text,
            icon: icon,
            confirmButtonText: 'OK',
            showConfirmButton: true
        })
    }

    const fireAlertDelete = async () => {
        return await fire('Are you sure?', 'You won\'t be able to revert this!', 'warning')
    }

    const toast = async (text, icon = 'success', position = 'bottom-end') => {
        // show a toast on bottom right
        return await Swal.fire({
            position,
            icon,
            text,
            toast: true,
            timer: 3000,
        })
    }

    const fireError = async (text = 'An error has ocurred', body = '') => {
        return await Swal.fire({
            text,
            html: body,
            icon: 'error',
            confirmButtonText: 'OK, i will fix it.',
            showConfirmButton: true,
            position: "center"
        })
    }

    return {
        fire,
        fireAlertDelete,
        toast,
        fireError
    }
}
