<template>
    <div class="inline-flex" v-for="(option, i) in displayValues" :key="i">
        {{ option }}
        <span v-if="displayValues.length !== i+1">,&nbsp;</span>
    </div>
</template>

<script>
import fetch from '../../../js/fetch';

export default {
    props: ['options', 'data', 'translatable'],
    data: function () {
        return {
            selects: [],
        }
    },
    computed: {
        parsedValues: function () {
            try {
                var json = JSON.parse(this.data);
                if (this.isArray(json)) {
                    return json;
                }
            } catch { }

            return [];
        },
        displayValues: function () {
            var values = [];
            var vm = this;
            vm.selects.forEach(function (select, i) {
                if (vm.parsedValues.length >= i) {
                    var value = select[vm.parsedValues[i]] || null;
                    if (value !== null) {
                        values.push(value);
                    }
                }
            });

            return values;
        }
    },
    mounted: function () {
        if (this.data == '' || this.data === null) {
            return;
        }

        var vm = this;

        if (vm.options.route_name == '') {
            return;
        }

        fetch.post(vm.route(vm.options.route_name), {
            selected: vm.parsedValues,
        })
        .then(function (response) {
            vm.selects = response.data;
        })
        .catch(function (response) {
            vm.$store.handleAjaxError(response);
        });
    }
};
</script>