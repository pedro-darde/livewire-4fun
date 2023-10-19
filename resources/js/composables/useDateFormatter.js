import { parse, format, isValid, addHours,  isBefore, isAfter } from 'date-fns'
import { ptBR } from 'date-fns/locale'

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
    'pt-BR only_day_and_hour': 'PPPPp',
}

export function useDateFormatter() {
    function createFromFormat(value, format = LOCALE_FORMATS["pt-BR"]) {
        return parse(value, format, new Date())
    }

    function toDatabaseDate(value, formatDate = LOCALE_FORMATS["pt-BR"], tryToValidate = true, isDateTime = false) {
        if (isValid(value) && tryToValidate) {
            return format(value, 'yyyy-MM-dd')
        }

        let formatTo = !isDateTime ? 'yyyy-MM-dd' : 'yyyy-MM-dd HH:mm'

        return format(createFromFormat(value, formatDate), formatTo)
    }

    function formatDate(value = new Date(), formatDate = LOCALE_FORMATS["pt-BR"], locale = ptBR) {
        try {
            return format(value, formatDate, {
                locale,
            })
        } catch (error) {
            return value
        }
    }

    function addHoursToDate(date, hours) {
        return addHours(date, hours)
    }

    function greatherThan(date, dateToCompare = new Date()) {
        if (!(date instanceof Date)) {
            date = new Date(date)
        }
        return date.getTime() > dateToCompare.getTime()
    }

    function getDayOfWeek(date, locale = ptBR) {
        return format(date, 'EEEE', {
            locale
        })
    }

    return {
        createFromFormat,
        toDatabaseDate,
        formatDate,
        addHoursToDate,
        greatherThan,
        getDayOfWeek,
        isAfter,
        isBefore,
    }
}
