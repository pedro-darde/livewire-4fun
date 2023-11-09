import { ResponseAdapter } from './ResponseAdapter';
import axios, { AxiosResponse } from "axios";
import { axiosInstance } from './axios';

export class RequestProcessor {

    // @ts-ignore
    static cacheResponses: Map<string, ResponseAdapter> = new Map<string, ResponseAdapter>()

    constructor(private readonly url: string,
        private readonly method = 'get',
        private readonly data = {},
    ) {
    }

    async process() {
        let response
        try {
            response = await axiosInstance[this.method](this.url, this.data)
        } catch (err:any) {
            response = err.response
        } finally {
            return new ResponseAdapter(response)
        }
    }

    async processFormData() {
        let response
        try {
            response = await axiosInstance[this.method](this.url, this.data, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
        } catch (err:any) {
            response = err.response
        } finally {
            return new ResponseAdapter(response)
        }
    }

    async processCached() {
        let response
        if (RequestProcessor.cacheResponses.has(this.url)) {
            console.info(`Caching request for: ${this.url}`)
            return RequestProcessor.cacheResponses.get(this.url)
        }

        try {
            response = await axiosInstance[this.method](this.url, {
                data: this.data
            })
        } catch (err) {
            response = err.response
        } finally {
            RequestProcessor.cacheResponses.set(this.url, new ResponseAdapter(response))
            return RequestProcessor.cacheResponses.get(this.url)
        }
    }
}
