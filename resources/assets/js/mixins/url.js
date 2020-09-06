export default {
    methods: {
        addParameterToUrl: function (parameter, value, url = null) {
            var newurl = new URL(document.location.href);
        
            if (url)  {
                newurl = new URL(url);
            }
            newurl.searchParams.set(parameter, value);
        
            return newurl;
        },
        getParameterFromUrl: function (key, default_value = null, url = null) {
            var newurl = new URL(document.location.href);
            if (url)  {
                newurl = new URL(url);
            }
        
            return newurl.searchParams.get(key) || default_value;
        },
        getParametersFromUrl: function (url = null) {
            var newurl = new URL(document.location.href);
        
            if (url)  {
                newurl = new URL(url);
            }
        
            return newurl.searchParams.entries();
        },
        pushToUrlHistory: function (url) {
            window.history.pushState({ path:  url.href }, '', url.search);
        },
        route: function () {
            var args = Array.prototype.slice.call(arguments);
            var name = args.shift();
            if (this.$store.routes[name] === undefined) {
                console.error('Route not found ', name);
            } else {
                return this.$store.routes[name]
                    .split('/')
                    .map(s => s[0] == '{' ? args.shift() : s)
                    .join('/');
            }
        },
        asset: function (path) {
            return document.head.querySelector('meta[name="asset-url"]').content + path;
        }
    }
};