<template>
    <div>
        <div v-if="show == 'view-options'">
            <div v-if="relationship">
                <card title="BREAD" class="mt-3" v-if="relationship.has_bread">
                    <label for="browse_list">{{ __('voyager::formfields.relationship.browse_list') }}</label>
                    <select v-model="options.browse_list" class="input small w-full" id="browse_list">
                        <option :value="null">{{ __('voyager::generic.none') }}</option>
                        <option v-for="(list, i) in relationshipLayouts('list')" :key="'list-'+i">
                            {{ list.name }}
                        </option>
                    </select>

                    <label for="add_view">{{ __('voyager::formfields.relationship.add_view') }}</label>
                    <select v-model="options.add_view" class="input small w-full" id="add_view">
                        <option :value="null">{{ __('voyager::generic.none') }}</option>
                        <option v-for="(view, i) in relationshipLayouts('view')" :key="'view-'+i">
                            {{ view.name }}
                        </option>
                    </select>
                </card>
                <card title="Field">
                    <select v-model="options.column" class="input small w-full">
                        <option :value="null">{{ __('voyager::generic.none') }}</option>
                        <option v-for="(column, i) in relationship.columns" :key="'column-'+i">
                            {{ column }}
                        </option>
                    </select>
                </card>
            </div>
            <div v-else>
                {{ __('voyager::formfields.relationship.select_relationship') }}
            </div>
        </div>
        <div v-else-if="show == 'view'">
            ...
        </div>
    </div>
</template>

<script>
export default {
    props: ['options', 'column', 'show', 'relationships'],
    methods: {
        relationshipLayouts: function (type) {
            var vm = this;
            if (!vm.relationship || !vm.relationship.has_bread) {
                return [];
            }

            return vm.relationship.bread.layouts.where('type', type);
        },
    },
    computed: {
        relationship: function () {
            var vm = this;
            if (!vm.relationships) {
                return null;
            }
            return vm.relationships.where('method', vm.column.column)[0];
        },
    },
};
</script>