export default {
    methods: {
        kebabCase: function (input, char = '-') {
            let parts = input.match(/[A-Z]{2,}(?=[A-Z][a-z]+[0-9]*|\b)|[A-Z]?[a-z]+[0-9]*|[A-Z]|[0-9]+/g);
            if (parts) {
                return parts.map(x => x.toLowerCase()).join(char);
            }

            return input;
        },
        snakeCase: function (input) {
            return this.kebabCase(input, '_');
        },
        titleCase: function (input, join = ' ') {
            return this.kebabCase(input, '_')
                .split('_')
                .map((x) => x.charAt(0).toUpperCase() + x.slice(1))
                .join(join);
        },
        studly: function (input) {
            return this.titleCase(input, '');
        },
        nl2br: function (input) {
            return input.replace(/\\n/g, '<br>');
        },
        ucFirst: function (input) {
            return input[0].toUpperCase() + input.slice(1);
        },
        numberFormat: function (amount, decimalCount = 2, decimal = ".", thousands = ",") {
            decimalCount = Math.abs(decimalCount);
            decimalCount = isNaN(decimalCount) ? 2 : decimalCount;
        
            const negativeSign = amount < 0 ? "-" : "";
        
            let i = parseInt(Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
            let j = (i.length > 3) ? i.length % 3 : 0;
        
            return negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) + (decimalCount ? decimal + Math.abs(amount - parseInt(i)).toFixed(decimalCount).slice(2) : "");
        },
        readableFileSize: function (bytes, decimals = 2)
        {
            if (bytes === 0) return '0 Bytes';
        
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        
            const i = Math.floor(Math.log(bytes) / Math.log(k));
        
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        },
        stringAfterLast: function (char, input) {
            var parts = input.split(char);

            return parts[parts.length - 1];
        },
    }
};