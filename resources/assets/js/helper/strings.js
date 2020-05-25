Vue.mixin({
    methods: {
        kebab_case: function (input, char = '-') {
            return input
                .match(/[A-Z]{2,}(?=[A-Z][a-z]+[0-9]*|\b)|[A-Z]?[a-z]+[0-9]*|[A-Z]|[0-9]+/g)
                .map(x => x.toLowerCase())
                .join(char);
        },
        snake_case: function (input) {
            return this.kebab_case(input, '_');
        },
        title_case: function (input, join = ' ') {
            return this.kebab_case(input, '_')
                .split('_')
                .map(x => x.charAt(0).toUpperCase() + x.slice(1))
                .join(join);
        },
        studly: function (input) {
            return this.title_case(input, '');
        },
        nl2br: function (input) {
            return input.replace(/\\n/g, '<br>');
        },
        ucfirst: function (input) {
            return input[0].toUpperCase() + input.slice(1);
        },
        number_format: function (amount, decimalCount = 2, decimal = ".", thousands = ",") {
            decimalCount = Math.abs(decimalCount);
            decimalCount = isNaN(decimalCount) ? 2 : decimalCount;
        
            const negativeSign = amount < 0 ? "-" : "";
        
            let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
            let j = (i.length > 3) ? i.length % 3 : 0;
        
            return negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
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
        uuid: function () {
            var dt = new Date().getTime();
            var uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                var r = (dt + Math.random()*16)%16 | 0;
                dt = Math.floor(dt/16);
                return (c=='x' ? r :(r&0x3|0x8)).toString(16);
            });
        
            return uuid;
        },
    }
});