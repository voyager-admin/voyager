let handler = [];

export default {
    on(event, callback) {
        handler.push({
            event: event,
            callback: callback,
        });
    },

    emit() {
        var args = Array.from(arguments);
        var event = args[0];
        var payload = args.splice(1);
        handler.where('event', event).forEach((h) => {
            h.callback(...payload);
        });
    },

    hasListener(event) {
        return handler.where('event', event).length > 0;
    },
};