<template>
    <div>
        <listbox
            :options="selectable"
            v-model="selected"
            :close-on-select="!relationship.multiple"
            :loading="loading"
            :multiple="relationship.multiple"
            search
            @search="search"
            :pages="pages"
            @page="page = $event"
            :search-options-text="translate(options.search_text, true)"
            :select-option-text="translate(options.select_text, true)"
            @opened="!initial_loaded ? loadResults() : null"
            :disabled="!options.editable || false"
        >
            <div>
                <div v-if="relationship.multiple">
                    <badge
                        v-for="(option, i) in value"
                        :key="i"
                        :icon="options.editable ? 'x' : ''"
                        @click-icon.stop.prevent="remove(option.key)"
                    >
                        {{ option.value }}
                    </badge>
                    <span v-if="value.length == 0">
                        {{ translate(options.select_text, true) }}
                    </span>
                </div>
                <div v-else>
                    <span v-if="value.length !== 0">
                        {{ value[0].value }}
                    </span>
                    <span v-else>
                        {{ translate(options.select_text, true) }}
                    </span>
                </div>
            </div>
        </listbox>
    </div>
</template>

<script>
export default {
    props: ['options', 'value', 'column', 'relationships', 'bread', 'translatable'],
    data: function () {
        return {
            loading: false,
            initial_loaded: false,
            page: 1,
            pages: 1,
            selectable: []
        };
    },
    computed: {
        relationship: function () {
            return this.relationships.where('method', this.column.column).first() || null;
        },
        selected: {
            get: function () {
                return this.value.map(function (item) {
                    return item.key;
                });
            },
            set: function (value) {
                if (!this.relationship.multiple) {
                    this.$emit('input', [{
                        key: value,
                        value: this.selectable.where('key', value).first().value
                    }]);

                    return;
                }
                var keys = this.value.pluck('key');

                var diff = value.diff(keys).first();
                if (diff !== undefined) {
                    // Added an entry
                    this.$emit('input', [
                        ...this.value,
                        {
                            key: diff,
                            value: this.selectable.where('key', diff).first().value,
                        }
                    ]);
                } else {
                    diff = keys.diff(value).first();
                    this.$emit('input', this.value.whereNot('key', diff));
                }
            }
        }
    },
    methods: {
        loadResults: function () {
            var vm = this;
            vm.loading = true;

            axios.post(vm.route('voyager.'+vm.translate(this.bread.slug, true)+'.relationship'), {
                query: vm.query,
                method: vm.relationship.method,
                page: vm.page,
                column: vm.options.display_column,
                scope: vm.options.scope,
            })
            .then(function (response) {
                vm.selectable = response.data.data;
                vm.pages = response.data.pages;
                if (vm.relationship.type === 'BelongsTo' && (vm.options.allow_null)) {
                    vm.selectable.unshift({
                        key: null,
                        value: vm.__('voyager::generic.none'),
                    });
                }

                vm.loading = false;
                vm.initial_loaded = true;
            });
        },
        search: debounce(function (query) {
            this.query = query;
            this.loadResults();
        }, 250),
        remove: function (key) {
            if (!this.options.editable) {
                return;
            }
            this.$emit('input', this.value.whereNot('key', key));
        },
    },
    watch: {
        page: function () {
            this.loadResults();
        }
    },
    mounted: function () {
        //this.loadResults();
    }
};
</script>