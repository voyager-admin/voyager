<template>
    <div>
        <div v-if="options.browse_list">
            <div v-if="relationship">
                <div class="w-full text-right" v-if="addLayout">
                    <modal :ref="'add-'+_uid">
                        <bread-edit-add
                            :bread="relationship.bread"
                            action="add"
                            :layout="addLayout"
                            :translatable="true"
                            :relationships="[]"
                            :prev-url="''"
                            :input="{}"
                            v-on:saved="addedNewEntry"
                            :from-relationship="true"
                        ></bread-edit-add>
                        <div slot="opener" class="w-full">
                            <button class="button green">
                                <icon icon="plus"></icon>
                                <span>{{ __('voyager::generic.add_type', { type: translate(relationship.bread.name_singular, true)}) }}</span>
                            </button>
                        </div>
                    </modal>
                </div>
                <bread-browse
                    class="border-none shadow-none"
                    style="padding: 0 !important; box-shadow: none !important"
                    :bread="relationship.bread"
                    :relationship-layout="relationshipLayout"
                    :relationship-selected="selected"
                    :relationship-multiple="relationship.multiple"
                    :primary-key="relationship.key_name"
                    :per-page="5"
                    :ref="'browse-'+_uid"
                    v-on:select="$emit('input', $event)"
                    from-relationship
                ></bread-browse>
            </div>
        </div>
        <div v-else-if="options.column">

        </div>
        <div v-else>
            Please provider a BREAD browse-list or a column for this relationship!
        </div>
    </div>
</template>

<script>
export default {
    props: ['options', 'value', 'column', 'relationships'],
    data: function () {
        return {
            reactiveValue: this.isArray(this.value) ? this.value : [this.value],
        };
    },
    computed: {
        relationship: function () {
            var method = this.column.column;

            return this.relationships.where('method', method)[0];
        },
        relationshipLayout: function () {
            var layout_name = this.options.browse_list;
            return this.relationship.bread.layouts.where('name', layout_name).where('type', 'list')[0];
        },
        addLayout: function () {
            var layout_name = this.options.add_view;
            if (!layout_name) {
                return null;
            }
            return this.relationship.bread.layouts.where('name', layout_name).where('type', 'view')[0];
        },
        selected: function () {
            var vm = this;
            var values = vm.reactiveValue;
            var primary = vm.relationship.key_name;
            if (vm.isObject(vm.reactiveValue)) {
                values = [vm.reactiveValue];
            }
            var selected = [];
            values.forEach(function (val) {
                selected.push(val[primary]);
            });

            return selected;
        }
    },
    methods: {
        addedNewEntry: function (key) {
            this.$refs['add-'+this._uid].close();
            this.$refs['browse-'+this._uid].load();
        },
    },
};
</script>