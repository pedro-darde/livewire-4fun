import {parse, format, isValid, addHours} from 'date-fns'

export const LOCALE_FORMATS = {
    en: 'MM/DD/yyyy',
    fr: 'DD/MM/yyyy',
    es: 'DD/MM/yyyy',
    de: 'DD.MM.yyyy',
    it: 'DD/MM/yyyy',
    pt: 'DD/MM/yyyy',
    ru: 'DD.MM.yyyy',
    ja: 'yyyy/MM/DD',
    ko: 'yyyy/MM/DD',
    'pt-BR': 'dd/MM/yyyy',
    'pt-BR datetime': 'dd/MM/yyyy HH:mm',
}

export function useDateFormatter() {
    function createFromFormat(value, format = LOCALE_FORMATS["pt-BR"]) {
        return parse(value, format, new Date())
    }

    function toDatabaseDate(value, formatDate = LOCALE_FORMATS["pt-BR"]) {
        if (isValid(value)) {
            return format(value, 'yyyy-MM-dd')
        }

        return format(createFromFormat(value, formatDate), 'yyyy-MM-dd')
    }

    function formatDate(value = new Date(), formatDate = LOCALE_FORMATS["pt-BR"]) {
        return format(value, formatDate)
    }

    function addHoursToDate(date, hours) {
        return addHours(date, hours)
    }

    return {
        createFromFormat,
        toDatabaseDate,
        formatDate,
        addHoursToDate
    }
}
