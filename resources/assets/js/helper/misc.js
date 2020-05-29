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

Vue.prototype.firstExistingComponent = function () {
    var components = '';
    for (i = 0; i < arguments.length; i++) {
        components = components + arguments[i] + ', ';

        if (Vue.options.components[arguments[i]] !== undefined) {
            return arguments[i];
        }
    }

    console.error('None of the following components exists: ' + components.slice(0, -2));
}