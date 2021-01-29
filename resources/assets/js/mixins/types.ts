export default {
    methods: {
        isArray: function (input: any) {
            return (input && typeof input === 'object' && input instanceof Array);
        },
        isObject: function (input: any) {
            return (input && typeof input === 'object' && input.constructor === Object);
        },
        isString: function (input: any) {
            return (typeof input === 'string');
        },
        isNumber: function (input: any) {
            return (typeof input === 'number');
        },
        isBoolean: function (input: any) {
            return (typeof input === 'boolean');
        },
    }
};