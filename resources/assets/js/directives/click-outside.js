export default {
    mounted(el, binding) {
        var first = false;

        document.documentElement.addEventListener('mousedown', (e) => {
            var target = e.target;
            var inside = false;

            do {
                if (target == el) {
                    inside = true;
                }
            } while (target = target.parentNode);

            if (!inside && first) {
                binding.value();
            }

            first = true;
        }, true);
    }
};