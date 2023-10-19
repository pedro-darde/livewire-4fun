import {ref} from "vue";
import {RequestProcessor} from "../shared/RequestProcessor";
import {VAutocomplete, VCheckbox, VTextField} from "vuetify/components";
import {usePage} from "@inertiajs/vue3";

export function useFieldComponents() {
    const fieldsComponents = ref({})
    const page = usePage()


    const fieldTypeToHtmlType = (fieldType) => {
        switch (fieldType) {
            case 'numeric':
                return 'number'
            case 'text':
                return 'text'
            case 'email':
                return 'email'
            case 'password':
                return 'password'
            case 'select':
                return 'select'
            default:
                return 'text'
        }
    }
    const setComponentByFieldType = async (field) => {
        const {
            type: fieldType,
            name,
            label,
            mask,
            placeholder,
            options,
            default: defaultValue,
            description,
            required,
            rules,
            disabled,
            visible,
            searchUrl,
            multiple,
            useAjaxToLoadOptions,
            relations,
            optionText,
            optionValue,
            checked
        } = field
        const variant = 'solo-filled'

        const defaultProps = {
            required,
            rules,
            label,
            type: fieldTypeToHtmlType(fieldType),
            variant
        }

        let configComponent = {}
        switch (fieldType) {
            case 'numeric':
            case 'text':
            case 'email':
            case 'password':
                configComponent = {
                    component: VTextField,
                    props: {
                        ...defaultProps
                    }
                }
                break;
            case 'select':
                const items = await getOptionsForField(useAjaxToLoadOptions, searchUrl, options, optionText, optionValue)
                configComponent = {
                    component: VAutocomplete,
                    props: {
                        items,
                        ...defaultProps,
                        'item-title': 'text',
                        'item-value': 'value'
                    }
                }
                break;
            case 'checkbox':

                configComponent = {
                    props: {
                        ...defaultProps,
                        checked,
                    },
                    component: VCheckbox
                }
                break;

            default:

                configComponent = {
                    component: VTextField,
                    props: {
                        ...defaultProps,
                    }
                }
                break;
        }

        return configComponent
    }
    const getOptionsForField = async (useAjax, ajaxUrl, defaultOptions = [], optionText = '', optionValue = 'id') => {
        if (!useAjax) {
            return defaultOptions.split(";").map(([value, text]) => {
                return {
                    value,
                    text
                }
            })
        }
        const baseURL = page.props.app.base_url
        return (await (new RequestProcessor(`${baseURL}/api/dynamic/getOptions/${ajaxUrl}`, 'post', {
            optionText,
            optionValue
        })).processCached()).getBody()?.items || []
    }

    async function defineFieldsComponents(fieldConfigs) {
        for await (const field of fieldConfigs) {
            fieldsComponents.value[field.name] = await setComponentByFieldType(field)
        }
    }

    function getFieldsComponents() {
        return fieldsComponents.value
    }

    return {
        fieldsComponents,
        defineFieldsComponents,
        getFieldsComponents
    }
}
