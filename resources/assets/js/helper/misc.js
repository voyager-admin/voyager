Vue.mixin({
    methods: {
        copyToClipboard: function (message) {
            var dummy = document.createElement('textarea');
            document.body.appendChild(dummy);
            dummy.value = message.replace(/\'/g, "'");
            dummy.select();
            document.execCommand('copy');
            document.body.removeChild(dummy);

            return false;
        }
    }
});