let handler = [];

export default {
    on: function (event, callback) {
        handler.push({
            event: event,
            callback: callback,
        });
    },

    emit: function () {
        var args = Array.from(arguments);
        var event = args[0];
        var payload = args.splice(1);
        handler.where('event', event).forEach(function (h) {
            h.callback(...payload);
        });
    },

    hasListener: function (event) {
        return handler.where('event', event).length > 0;
    },
};