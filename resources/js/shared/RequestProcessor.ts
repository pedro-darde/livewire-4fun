import {ResponseAdapter} from "./ResponseAdapter";

export class RequestProcessor {
    constructor(private readonly request: Promise<any>) {
    }
    async process() {
        let response
        try {
            response = await this.request
        } catch (err) {
            response = err.response
        } finally {
            return new ResponseAdapter(response)
        }
    }
}
