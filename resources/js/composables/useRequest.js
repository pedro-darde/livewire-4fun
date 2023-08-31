import {RequestProcessor} from "../shared/RequestProcessor";

export default function useRequest() {
    async function processRequest(request) {
       return await (new RequestProcessor(request).process())
    }

    return {
        processRequest
    }
}
