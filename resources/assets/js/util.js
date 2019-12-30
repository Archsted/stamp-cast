
const isNumber = (val) => {
    var regex = new RegExp(/^[0-9]+$/);
    return regex.test(val);
}

const isFloat = (val) => {
    var regex = new RegExp(/^[0-9]+(\.[0-9])?$/);
    return regex.test(val);
}

export default {
    isNumber,
    isFloat,
}