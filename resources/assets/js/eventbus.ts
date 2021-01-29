interface eventCallback { (...payload: any): void }

let handler: Array<{ event: string, callback: eventCallback }> = [];

export default {
    on(event: string, callback: eventCallback) {
        handler.push({
            event: event,
            callback: callback,
        });
    },

    emit() {
        var args = Array.from(arguments);
        var event = args[0];
        var payload = args.splice(1);

        handler.filter((h: { event: string, callback: eventCallback }) => {
            return h.event == event;
        }).forEach((h: { event: string, callback: eventCallback }) => {
            h.callback(...payload);
        });
    },

    hasListener(event: string) {
        return handler.filter((h: { event: string, callback: eventCallback }) => {
            return h.event == event;
        }).length > 0;
    },
};