import {capitalize as vueCapitalize } from "vue";

export abstract class Str {
    static capitalize(str: string): string {
        return vueCapitalize(str)
    }
}
