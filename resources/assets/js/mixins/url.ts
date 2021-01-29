export default {
    methods: {
        addParameterToUrl: function (parameter: string, value: string, url?: string) {
            var newurl = new URL(document.location.href);
        
            if (url)  {
                newurl = new URL(url);
            }
            newurl.searchParams.set(parameter, value);
        
            return newurl;
        },
        getParameterFromUrl: function (key: string, default_value: any, url?: string) {
            var newurl = new URL(document.location.href);
            if (url)  {
                newurl = new URL(url);
            }
        
            return newurl.searchParams.get(key) || default_value;
        },
        getParametersFromUrl: function (url?: string) {
            var newurl = new URL(document.location.href);
        
            if (url)  {
                newurl = new URL(url);
            }
            // @ts-ignore
            return newurl.searchParams.entries();
        },
        pushToUrlHistory: function (url: URL) {
            window.history.pushState({ path:  url.href }, '', url.search);
        },
        route: function (): string {
            var args = Array.prototype.slice.call(arguments);
            var name = args.shift();
            // @ts-ignore
            if (this.$store.routes[name] === undefined) {
                console.error('Route not found ', name);
            } else {
                // @ts-ignore
                return this.$store.routes[name].split('/').map(s => s[0] == '{' ? args.shift() : s).join('/');
            }

            return '';
        },
        asset: function (path: string): string {
            // @ts-ignore
            return this.route('voyager.voyager_assets')+'?path='+encodeURI(path);
        }
    }
};