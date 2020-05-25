Vue.mixin({
    methods: {
        clamp: function (num, min, max) {
            if (num < min) {
                return min;
            } else if (num > max) {
                return max;
            }
        
            return num;
        }
    }
});