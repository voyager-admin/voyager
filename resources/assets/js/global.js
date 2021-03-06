export default {
    install(app) {
        app.config.globalProperties.toggleDirection = function () {
            this.$store.rtl = !this.$store.rtl;
            if (this.$store.rtl) {
                document.querySelector('html').setAttribute('dir', 'rtl');
            } else {
                document.querySelector('html').setAttribute('dir', 'ltr');
            }
        };

        // Dark mode
        app.config.globalProperties.toggleDarkMode = function () {
            if (this.$store.darkmode == 'light') {
                this.$store.darkmode = 'dark';
            } else if (this.$store.darkmode == 'dark') {
                this.$store.darkmode = 'system';
                this.setDarkMode(this.$store.systemDarkmode ? 'dark' : 'light');
            } else {
                this.$store.darkmode = 'light';
            }
            localStorage.mode = this.$store.darkmode;
            if (['dark', 'light'].includes(this.$store.darkmode)) {
                this.setDarkMode(this.$store.darkmode);
            }
        };

        app.config.globalProperties.setDarkMode = function (mode) {
            if (mode == 'dark') {
                document.documentElement.classList.add('dark')
            } else if (mode == 'light') {
                document.documentElement.classList.remove('dark')
            }
            this.$store.darkmode == mode;
        };

        app.config.globalProperties.initDarkMode = function () {
            if (('mode' in localStorage) && ['dark', 'light'].includes(localStorage.mode)) {
                this.setDarkMode(localStorage.mode);
                this.$store.darkmode = localStorage.mode;
            } else {
                localStorage.mode = 'system';
            }
    
            //systemDarkmode
            var match = window.matchMedia('(prefers-color-scheme: dark)');
            match.addListener(() => {
                this.$store.systemDarkmode = match.matches;
                if (this.$store.darkmode == 'system') {
                    match.matches ? this.setDarkMode('dark') : this.setDarkMode('light');
                }
            });
            this.$store.systemDarkmode = match.matches;
            if (this.$store.darkmode == 'system') {
                match.matches ? this.setDarkMode('dark') : this.setDarkMode('light');
            }
        };

        // Sidebar
        app.config.globalProperties.toggleSidebar = function () {
            this.$store.sidebarOpen = !this.$store.sidebarOpen;
            $eventbus.emit('sidebar-open', this.$store.sidebarOpen);
        };
        app.config.globalProperties.openSidebar = function () {
            this.$store.sidebarOpen = true;
            $eventbus.emit('sidebar-open', true);
        };
        app.config.globalProperties.closeSidebar = function () {
            this.$store.sidebarOpen = false;
            $eventbus.emit('sidebar-open', false);
        };

        // Formfield
        app.config.globalProperties.getFormfieldByType = function (type) {
            var formfield = this.$store.formfields.where('type', type).first();
            if (!formfield) {
                console.error('Formfield with type "'+type+'" does not exist!');
            }

            return formfield || {};
        };

        // Initialize darkmode
        app.config.globalProperties.initDarkMode();
    }
};