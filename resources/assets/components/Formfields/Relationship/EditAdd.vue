<template>
    <div>
        <div v-if="addView">
            <modal
                :title="__('voyager::generic.add_type', { type: translate(relatedBread.name_singular, true) })"
                :icon="relatedBread.icon"
                ref="add_modal"
            >
                <bread-edit-add
                    :bread="add_data.bread"
                    action="add"
                    :input="add_data.data"
                    :layout="addView"
                    :new="true"
                    :prevUrl="''"
                    :fromRelationship="true"
                    @saved="added($event)"
                />
                <div slot="actions">
                    <locale-picker :small="false"></locale-picker>
                </div>
            </modal>
            <div class="w-full mb-2 flex justify-end">
                <button class="button green" @click="fetchRelationshipData">
                    <icon icon="refresh" class="animate-spin-reverse ltr:mr-1 rtl:ml-1" :size="4" v-if="fetching_add_data" />
                    {{ __('voyager::generic.add_type', { type: translate(relatedBread.name_singular, true) }) }}
                </button>
            </div>
        </div>
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
            :select-option-text="selectText"
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
                        {{ translate(option.value) }}
                    </badge>
                    <span v-if="value.length == 0">
                        {{ selectText }}
                    </span>
                </div>
                <div v-else>
                    <span v-if="value.length !== 0">
                        {{ translate(value[0].value) }}
                    </span>
                    <span v-else>
                        {{ selectText }}
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
            selectable: [],
            fetching_add_data: false,
            add_data: {}
        };
    },
    computed: {
        relationship: function () {
            return this.relationships.where('method', this.column.column).first() || null;
        },
        relatedBread: function () {
            if (this.relationship) {
                return this.$store.getBreadByTable(this.relationship.table);
            }

            return null;
        },
        addView: function () {
            if (this.options.add_view && this.relationship && this.relatedBread) {
                return this.relatedBread.layouts.where('type', 'view').where('name', this.options.add_view).first();
            }

            return null;
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
        },
        selectText: function () {
            var text = this.translate(this.options.select_text, true);
            if (text == '') {
                text = 'Select';
            }

            return text;
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
        fetchRelationshipData: function () {
            var vm = this;
            vm.fetching_add_data = true;
            axios.get(vm.route('voyager.'+vm.translate(vm.relatedBread.slug, true)+'.add'), {
                params: {
                    from_relationship: true
                }
            })
            .then(function (response) {
                vm.add_data = response.data;
                vm.$refs.add_modal.open();
            })
            .catch(function () {

            })
            .then(function () {
                vm.fetching_add_data = false;
            });
        },
        added: function (key) {
            // TODO: We could automatically add key here
            this.loadResults();
            this.$refs.add_modal.close();
        }
    },
    watch: {
        page: function () {
            this.loadResults();
        }
    },
};
</script>