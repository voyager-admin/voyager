import { createPopper } from '@popperjs/core/lib/popper-lite';
import { placements } from '@popperjs/core/lib/enums';

export default {
    mounted(el, binding) {
        let placement = 'bottom';
        if (placements.includes(binding.arg)) {
            placement = binding.arg;
        } else if (binding.arg) {
            console.error(`'${binding.arg}' is not a valid placement for a tooltip. It can be ${placements.join(', ')}.`);
        }
        
        let uuid = createUUID();

        el.uuid = uuid;
        if (binding.value !== null && binding.value !== undefined) {
            el.tooltip_value = binding.value;
        }

        let popper;
        let tooltip;

        // Register event listener
        el.addEventListener('mouseenter', () => {
            if (!popper && el.tooltip_value) {
                // Create tooltip
                tooltip = document.createElement('div');
                tooltip.setAttribute('id', uuid);
                tooltip.classList.add('tooltip');
                tooltip.classList.add('pointer-events-none');

                // Create tooltip content
                let content = document.createElement('div');
                content.setAttribute('class', 'tooltip-content');
                content.innerHTML = el.tooltip_value;
                tooltip.appendChild(content);

                document.getElementById('tooltips').appendChild(tooltip);

                popper = createPopper(el, tooltip, {
                    placement: placement,
                });

                el.popper = popper;
            }
        });
        el.addEventListener('mouseleave', () => {
            if (popper) {
                popper.destroy();
                popper = null;
                tooltip.parentNode.removeChild(tooltip);
                tooltip = null;
            }
        });
    },
    updated(el, binding) {
        if (binding.value !== binding.oldValue && el.uuid && binding.value !== null && binding.value !== undefined) {
            if (document.getElementById(el.uuid)) {
                document.getElementById(el.uuid).children[0].innerHTML = binding.value;
            }
            el.tooltip_value = binding.value;
        }
    },
    unmounted(el) {
        if (el.popper) {
            el.popper.destroy();
        }
        el.replaceWith(el.cloneNode(true)); // Instead of removing all event listeners, we simply replace the element with its own clone
    }
};

function createUUID() {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
    });
}