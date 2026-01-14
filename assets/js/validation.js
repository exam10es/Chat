const Validator = {
    required: (value) => value !== null && value !== undefined && value !== '',
    email: (value) => /\S+@\S+\.\S+/.test(value),
    minLength: (value, min) => String(value).length >= min,
    maxLength: (value, max) => String(value).length <= max,
};
