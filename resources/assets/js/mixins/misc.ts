export default {
    methods: {
        copyToClipboard: function (message: string) {
            var dummy = document.createElement('textarea');
            document.body.appendChild(dummy);
            dummy.value = message.replace(/\'/g, "'");
            dummy.select();
            document.execCommand('copy');
            document.body.removeChild(dummy);

            return false;
        },
        getCookie: function(name: string) {
            var name = name + '=';
            var decodedCookie = decodeURIComponent(document.cookie);
            var ca = decodedCookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(name) == 0) {
                    return c.substring(name.length, c.length);
                }
            }
            return '';
        },
        setCookie: function (name: string, value: string, exdays: number) {
            var d = new Date();
            d.setTime(d.getTime() + (exdays*24*60*60*1000));
            var expires = "expires="+ d.toUTCString();
            document.cookie = name + "=" + value + ";" + expires + ";path=/";
        },
    }
};