export const REQUIRED = (fieldName) => {
    return [
        v => !!v || `${fieldName} é obrigatório`
    ]
}
