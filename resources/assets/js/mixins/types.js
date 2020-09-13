export default {
    methods: {
        isArray: function (input) {
            return (input && typeof input === 'object' && input instanceof Array);
        },
        isObject: function (input) {
            return (input && typeof input === 'object' && input.constructor === Object);
        },
        isString: function (input) {
            return (typeof input === 'string');
        },
        isNumber: function (input) {
            return (typeof input === 'number');
        },
        isBoolean: function (input) {
            return (typeof input === 'boolean');
        },
    }
};