import { AxiosResponse } from 'axios'
import {Str} from "./Str";
export class ResponseAdapter {
    private statusCode: number
    constructor(private readonly response: AxiosResponse) {
        this.statusCode = response.status;
    }

    getStatusCode() {
        return this.statusCode;
    }

    getErrors(formatToHtml = false) {
        if (this.statusCode === 422) {
            if (formatToHtml) {
                const errors = this.response.data.errors
                let html = '<ul>'
                for (const key in errors) {
                    html += `<li><b> ${Str.capitalize(key)}: </b> ${errors[key].join(', ')}</li>`
                }
                html += '</ul>'
                return html
            }
            return this.response.data.errors
        }

        if (this.statusCode === 401) {
            return ['Unauthorized']
        }

        if (this.statusCode === 403) {
            return ['Forbidden']
        }

        if (this.statusCode === 404) {
            return ['Not Found']
        }
    }

    hasErrors() {
        console.log(this.statusCode)
        return this.statusCode > 301
    }
}
