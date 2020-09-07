let handler = [];

export default {
    on: function (event, callback) {
        handler.push({
            event: event,
            callback: callback,
        });
    },

    emit: function (event, payload) {
        var args = Array.from(arguments);
        handler.where('event', event).forEach(function (h) {
            h.callback(...args.splice(1));
        });
    }
};