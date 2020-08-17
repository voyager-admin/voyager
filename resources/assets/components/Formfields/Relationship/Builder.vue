<template>
    <div>
        <div v-if="show == 'view-options'">
            <label class="label mt-4">{{ __('voyager::generic.display_column') }}</label>
            <select class="input w-full" v-if="relationship !== null && relationship.hasOwnProperty('columns')" v-model="options.display_column">
                <optgroup :label="__('voyager::builder.columns')">
                    <option v-for="(column, i) in relationship.columns" :key="i">
                        {{ column }}
                    </option>
                </optgroup>
                <optgroup :label="__('voyager::builder.computed')">
                    <option v-for="(column, i) in relationship.computed" :key="i">
                        {{ column }}
                    </option>
                </optgroup>
            </select>
            <select class="input w-full" disabled v-else>
                <option>{{ __('voyager::formfields.relationship.select_relationship') }}</option>
            </select>

            <label class="label mt-4">{{ __('voyager::formfields.relationship.allow_null') }}</label>
            <input type="checkbox" class="input" v-model="options.allow_null">

            <label class="label mt-4">{{ __('voyager::generic.editable') }}</label>
            <input type="checkbox" class="input" v-model="options.editable">
            
            <label class="label mt-4">{{ __('voyager::formfields.relationship.select_text') }}</label>
            <language-input
                class="input w-full"
                type="text" :placeholder="__('voyager::formfields.relationship.select_text')"
                v-bind:value="options.select_text"
                v-on:input="options.select_text = $event" />

            <label class="label mt-4">{{ __('voyager::formfields.relationship.search_text') }}</label>
            <language-input
                class="input w-full"
                type="text" :placeholder="__('voyager::formfields.relationship.search_text')"
                v-bind:value="options.search_text"
                v-on:input="options.search_text = $event" />

            <label class="label mt-4" v-if="relationship && relationship.type == 'BelongsToMany'">
                {{ __('voyager::formfields.relationship.pivots') }}
            </label>
            <select multiple class="input w-full" v-if="relationship && relationship.type == 'BelongsToMany'" v-model="options.pivots">
                <option v-for="(pivot, i) in relationship.pivot" :key="i">
                    {{ pivot }}
                </option>
            </select>

            <label class="label" for="scope" v-if="relationship && relationship.scopes">{{ __('voyager::builder.scope') }}</label>
            <select class="input w-full" v-if="relationship && relationship.scopes" v-model="options.scope">
                <option :value="null">{{ __('voyager::generic.none') }}</option>
                <option v-for="(scope, i) in relationship.scopes" :key="i">{{ scope }}</option>
            </select>
        </div>
        <div v-else-if="show == 'view'">
            <select class="input w-full" disabled></select>
        </div>
    </div>
</template>

<script>
export default {
    props: ['options', 'column', 'show', 'relationships'],
    computed: {
        relationship: function () {
            var vm = this;
            if (!vm.relationships || !vm.column.column) {
                return null;
            }

            return vm.relationships.where('method', vm.column.column).first();
        },
        relatedBread: function () {

            return null;
        }
    },
};
</script>