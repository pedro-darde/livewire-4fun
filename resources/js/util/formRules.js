export const REQUIRED = (fieldName) => {
    return [
        v => !!v || `${fieldName} é obrigatório`
    ]
}

export const MIN_LENGTH = (fieldName, minLength) => {
    return [
        v => (v && v.length >= minLength) || `${fieldName} deve ter pelo menos ${minLength} caracteres`
    ]
}
