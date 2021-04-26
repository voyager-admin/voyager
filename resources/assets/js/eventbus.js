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

        handler.filter((h) => {
            return h.event == event;
        }).forEach((h) => {
            h.callback(...payload);
        });
    },

    hasListener(event) {
        return handler.filter((h) => {
            return h.event == event;
        }).length > 0;
    },
};