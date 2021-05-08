export default {
    mounted(el, binding) {
        el.addEventListener('click', (e) => {
            let content = document.getElementById('content');
            let element = document.getElementById('top');
            if (binding.value !== '') {
                element = document.getElementById(binding.value);
            }
            if (element) {
                let handler = (e) => {
                    if (e.target.scrollTop == element.offsetTop) {
                        content.removeEventListener('scroll', handler);
                        window.location.hash = binding.value;
                    } else if (content.scrollHeight - content.clientHeight < element.offsetTop) {
                        // Cannot completely scroll to elements
                        if (e.target.scrollTop == (content.scrollHeight - content.clientHeight)) {
                            // Scrolled to the closest position
                            content.removeEventListener('scroll', handler);
                            window.location.hash = binding.value;
                        }
                    }
                }
                content.addEventListener('scroll', handler);

                element.scrollIntoView({
                    left: 0,
                    block: 'start',
                    behavior: 'smooth'
                });
            } else {
                console.warn('Element with ID "'+binding.value+'" was not found!');
            }
            if (binding.modifiers.prevent === true) {
                e.preventDefault();
            }
            if (binding.modifiers.stop === true) {
                e.stopPropagation();
            }
        });
    }
};