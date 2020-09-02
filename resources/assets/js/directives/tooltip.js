import EventBus from '../eventbus';

export default {
    inserted: function (el, binding) {
        EventBus.$emit('addTooltip', el, binding);
    }
};