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
        getParameterFromUrl: function (key, default_value, url = null) {
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
            return window.route(...arguments);
        },
        asset: function (path) {
            return this.route('voyager.voyager_assets')+'?path='+encodeURI(path);
        }
    }
};