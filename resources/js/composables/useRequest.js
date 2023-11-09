import {RequestProcessor} from "../shared/RequestProcessor";
import {usePage} from "@inertiajs/vue3";

export default function useRequest() {
    async function processRequest(url, method, data = null, isCached = false) {
        const page = usePage()
        const baseURL = page.props.app.base_url
        const urlWithBase = `${baseURL}/${url}`
        if (isCached) {
            return await (new RequestProcessor(urlWithBase, method, data).processCached())
        }
       return await (new RequestProcessor(urlWithBase, method, data).process())
    }

    async function processRequestWithFormData(url, method, data = null) {
        const page = usePage()
        const baseURL = page.props.app.base_url
        const urlWithBase = `${baseURL}/${url}`
        return await (new RequestProcessor(urlWithBase, method, data).processFormData())
    }

    return {
        processRequest,
        processRequestWithFormData
    }
}
