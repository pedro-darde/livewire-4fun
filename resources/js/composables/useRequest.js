import {RequestProcessor} from "../shared/RequestProcessor";

export default function useRequest() {
    async function processRequest(url, method, data = null) {
       return await (new RequestProcessor(url, method, data).process())
    }

    return {
        processRequest
    }
}
