import { createPopper } from '@popperjs/core/lib/popper-lite';
import { placements } from '@popperjs/core/lib/enums';
import { v4 as uuidv4 } from 'uuid';

export default {
    mounted(el, binding) {
        let placement = 'bottom';
        if (placements.includes(binding.arg)) {
            placement = binding.arg;
        } else if (binding.arg) {
            console.error(`'${binding.arg}' is not a valid placement for a tooltip. It can be ${placements.join(', ')}.`);
        } else {
            let matches = Object.keys(binding.modifiers).filter(v => placements.includes(v));
            if (matches.length == 1) {
                placement = matches[0];
            }
        }
        
        let uuid = uuidv4();

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
                tooltip.classList.add('opacity-0');
                setTimeout(() => {
                    // There is a really low chance that the tooltip is already destroyed
                    if (tooltip) {
                        tooltip.classList.remove('opacity-0');
                        tooltip.classList.add('opacity-100');
                    }
                }, 1);
                

                // Create tooltip content
                let content = document.createElement('div');
                content.setAttribute('class', 'tooltip-content');
                content.innerHTML = el.tooltip_value;
                tooltip.appendChild(content);

                document.getElementById('tooltips').appendChild(tooltip);

                popper = createPopper(el, tooltip, {
                    placement: placement,
                    strategy: 'fixed',
                });

                el.popper = popper;
            }
        });
        el.addEventListener('mouseleave', () => {
            if (binding.modifiers.show) {
                return;
            }
            if (popper) {
                popper.destroy();
                popper = null;
                tooltip.parentNode.removeChild(tooltip);
                tooltip = null;
            }
        });

        if (binding.modifiers.show) {
            el.dispatchEvent(new Event('mouseenter'));
        }
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
        if (el.uuid) {
            let tooltipEl = document.getElementById(el.uuid);
            if (tooltipEl) {
                tooltipEl.parentElement.removeChild(tooltipEl);
            }
        }
        el.replaceWith(el.cloneNode(true)); // Instead of removing all event listeners, we simply replace the element with its own clone
    }
};