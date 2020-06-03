Vue.prototype.$notify = new Vue({
    data: function () {
        return {
            notifications: [],
        };
    },
    methods: {
        addNotification: function(obj) {
            var vm = this;

            return new Promise(function (resolve, reject) {
                obj.resolve = resolve;
                obj.reject = reject;
                obj._timeout_running = true;

               vm.notifications.push(obj);
            });
        },
        removeNotification: function(obj, result, message = null) {
            if (obj._prompt == true) {
                obj.resolve((result == true ? message : false));
            } else {
                obj.resolve(result);
            }
            this.notifications.splice(this.notifications.indexOf(obj), 1);
        }
    }
});

Vue.prototype.$notification = class Notification {
    constructor(message) {
        this._message = message;
        this._icon = 'information-circle';
        this._color = 'blue';
        this._buttons = [];
        this._uuid = this.uuid();

        return this;
    }

    title(title) {
        this._title = title;

        return this;
    }

    icon(icon) {
        this._icon = icon;

        return this;
    }

    color(color) {
        this._color = color;

        return this;
    }

    timeout(timeout = 7500) {
        this._timeout = timeout;

        return this;
    }

    indeterminate() {
        this._indeterminate = true;

        return this;
    }

    prompt(value = '') {
        this._prompt = true;
        this._prompt_value = value;

        return this;
    }

    confirm() {
        this._confirm = true;

        return this;
    }

    addButton(button) {
        this._buttons.push(button);

        return this;
    }

    show() {
        var vm = this;

        if (this._confirm && this._buttons.length == 0) {
            this.addButton({
                key: true,
                value: 'voyager::generic.yes',
                color: 'green',
            }).addButton({
                key: false,
                value: 'voyager::generic.no',
                color: 'red',
            });
        } else if (this._prompt && this._buttons.length == 0) {
            this.addButton({
                key: true,
                value: 'voyager::generic.save',
                color: 'green',
            }).addButton({
                key: false,
                value: 'voyager::generic.cancel',
                color: 'red',
            });
        }

        return new Promise(function(resolve, reject) {
            Vue.prototype.$notify.addNotification(vm)
            .then(function (result, message = null) {
                resolve(result, message);
            });
        });
    }

    uuid() {
        var dt = new Date().getTime();
        var uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = (dt + Math.random()*16)%16 | 0;
            dt = Math.floor(dt/16);
            return (c=='x' ? r :(r&0x3|0x8)).toString(16);
        });
    
        return uuid;
    }

    remove() {

    }
}