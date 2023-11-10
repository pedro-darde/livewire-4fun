import useRequest from "./useRequest.js";
import { useAlert } from "./useAlert.js";
import {isFunction} from "lodash";


export default function useNoteSaver() {
    const { processRequestWithFormData } = useRequest()
    const { toast, fireError } = useAlert()

    /**
     *
     * @param {{}} note
     * @param {Function | null} afterSaveFn
     * @returns {{saveNote: ((function(*): Promise<void>)|*)}}
     */
    const saveNote = async (note, afterSaveFn) => {
        let url = `note/${note.idAppointment}`
        if (!!note.id) {
            url += `/${note.id}`
        }
        const response = await processRequestWithFormData(url, 'post', note)
        if (response.hasErrors()) {
            fireError(response.getErrors(true))
            return
        }

        toast('Nota salva com sucesso!', 'success')

        if (isFunction(afterSaveFn)) {
            afterSaveFn()
        }
    }

    return {
        saveNote
    }
}
