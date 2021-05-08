import { createPopper } from '@popperjs/core';

export default {
    mounted(el, binding) {
        let placement = 'bottom';
        if (['top', 'left', 'bottom', 'right', 'auto'].includes(binding.arg)) {
            placement = binding.arg;
        } else if (binding.arg) {
            console.error(`The placement '${binding.arg}' is not valid for a tooltip. It can be top, bottom, left, right or auto.`);
        }
        
        let uuid = createUUID();

        el.uuid = uuid;
        if (binding.value !== null && binding.value !== undefined) {
            el.value = binding.value;
        }

        let popper;
        let tooltip;

        // Register event listener
        el.addEventListener('mouseenter', () => {
            if (!popper && el.value) {
                // Create tooltip
                tooltip = document.createElement('div');
                tooltip.setAttribute('id', uuid);
                tooltip.classList.add('tooltip');
                tooltip.classList.add('pointer-events-none');

                // Create tooltip content
                let content = document.createElement('div');
                content.setAttribute('class', 'tooltip-content');
                content.innerHTML = el.value;
                tooltip.appendChild(content);

                // Create arrow
                let arrow = document.createElement('div');
                arrow.classList.add('arrow');
                arrow.setAttribute('data-popper-arrow', '');
                tooltip.appendChild(arrow);

                document.getElementById('tooltips').appendChild(tooltip);

                popper = createPopper(el, tooltip, {
                    placement: placement,
                });
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
            el.value = binding.value;
        }
    },
    unmounted(el) {
        el.replaceWith(el.cloneNode(true)); // Instead of removing all event listeners, we simply replace the element with its own clone
    }
};

function createUUID() {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
    });
}