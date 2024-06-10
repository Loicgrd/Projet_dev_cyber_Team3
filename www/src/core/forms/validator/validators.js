export class Validators {
    static required(control) {
        return requiredValidator(control)
    }

    static email(control) {
        return isAnEmailValidator(control)
    }

    static phone(control) {
        return isAPhoneValidator(control)
    }
}

export function requiredValidator(control) {
    if (control.value === undefined) {
        return {required: true}
    }

    control.value = control.value.replace(/[^a-z0-9áéíóúñü \.,_-]/gim,""); // Sanitization
    return (control.value.trim().length === 0) ? {required: true} : null
}

export function isAnEmailValidator(control) {
    if (control.value === undefined || control.value.trim().length === 0) {
        return {email: true}
    }

    const emailRegex = /[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/
    return emailRegex.test(control.value.trim()) ? null : {email: true}
}

export function isAPhoneValidator(control) {
    if (control.value === undefined || control.value.trim().length === 0) {
        return {phone: true}
    }
    const phoneSpaceLess = control.value.replace(' ', '')
    if (phoneSpaceLess.length < 10) {
        return {phone: true}
    }
    return null
}
