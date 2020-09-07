<template>
    <div class="flex flex-no-wrap space-x-0.5 justify-end">
        <div v-for="(action, i) in filteredActions" :key="i">
            <component
                :is="action.method == 'get' ? 'a' : 'button'"
                class="button small"
                :class="action.color"
                :href="getUrl(action)"
                @click="click(action, $event)"
                v-show="amount(action) > 0"
            >
                <icon v-if="action.icon !== null" :icon="action.icon" />
                <span>{{ trans_choice(action.title, amount(action), replace) }}</span>
            </component>
        </div>
    </div>
</template>

<script>
import fetch from '../../js/fetch';

export default {
    emits: ['reload'],
    props: {
        bulk: {
            type: Boolean,
            default: false,
        },
        actions: {
            type: Array,
            required: true,
        },
        bread: {
            type: Object,
            required: true,
        },
        selected: {
            type: Array,
            required: true,
        },
    },
    computed: {
        filteredActions: function () {
            return this.actions.where('bulk', this.bulk);
        },
        replace: function () {
            return {
                type: this.translate(this.bread.name_singular, true),
                types: this.translate(this.bread.name_plural, true)
            };
        },
        deletable: function () {
            return this.selected.where('is_soft_deleted', false).length;
        },
        restorable: function () {
            return this.selected.where('is_soft_deleted', true).length;
        }
    },
    methods: {
        getUrl: function (action) {
            if (action.method !== 'get') {
                return;
            } else if (this.selected.length == 1) {
                return this.route(action.route_name, this.selected[0].primary_key);
            } else if (action.display_deletable === null) {
                return this.route(action.route_name);
            }

            return '#';
        },
        click: function (action, e) {
            var vm = this;
            if (action.method !== 'get') {
                e.preventDefault();
                e.stopPropagation();

                if (vm.isObject(action.confirm)) {
                    new vm.$notification(vm.trans_choice(action.confirm.message || null, vm.amount(action), vm.replace))
                        .title(vm.trans_choice(action.confirm.title || null, vm.amount(action), vm.replace))
                        .color(action.confirm.color)
                        .confirm()
                        .timeout()
                        .show()
                        .then(function (response) {
                            if (response === true) {
                                vm.executeAction(action);
                            }
                        });
                } else {
                    vm.executeAction(action);
                }
            }
        },
        executeAction: function (action) {
            var vm = this;
            fetch.createRequest(vm.route(action.route_name), (action.method.toLowerCase() || 'post'), {
                primary: vm.selectedPrimarys(action)
            }, action.download === true ? 'blob' : 'json')
            .then(function (response) {
                if (vm.isObject(action.success)) {
                    var amount = response.data.amount || 1;
                    var replace = response.data;
                    replace.type = vm.translate(vm.bread.name_singular, true);
                    replace.types = vm.translate(vm.bread.name_plural, true);

                    new vm.$notification(vm.trans_choice(action.success.message || null, amount, replace))
                        .title(vm.trans_choice(action.success.title || null, amount, replace))
                        .color(action.success.color)
                        .timeout()
                        .show();
                }

                if (action.download === true) {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', action.file_name);
                    link.click();
                    window.URL.revokeObjectURL(url);
                }

                if (action.reload_after) {
                    vm.$emit('reload');
                }
            })
            .catch(function (response) {
                vm.$store.handleAjaxError(response);
            });
        },
        selectedPrimarys: function (action) {
            if (action.bulk) {
                return this.selected.pluck('primary_key');
            }

            return this.selected[0].primary_key;
        },
        amount: function (action) {
            if (action.display_deletable === true) {
                return action.bulk ? this.deletable : (this.selected[0].is_soft_deleted ? 0 : 1);
            } else if (action.display_deletable === false) {
                return action.bulk ? this.restorable : (this.selected[0].is_soft_deleted ? 1 : 0);
            } else if (action.display_deletable === null) {
                return 1;
            }

            return this.selected.length;
        }
    }
}
</script>