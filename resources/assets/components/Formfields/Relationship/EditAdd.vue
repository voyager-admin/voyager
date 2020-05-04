<template>
    <div>
        <label class="label" v-if="translate(options.label, true) !== ''">{{ translate(options.label, true) }}</label>
        <div v-if="options.browse_list">
            <bread-browse
                class="border-none shadow-none"
                style="padding: 0 !important; box-shadow: none !important"
                v-if="relationship"
                :bread="relationship.bread"
                :relationship-layout="relationshipLayout"
                :relationship-selected="value"
                :relationship-multiple="relationship.multiple"
                v-on:select="$emit('input', $event)"
                from-relationship
            ></bread-browse>
        </div>
        <div v-else-if="options.column">

        </div>
        <div v-else>
            Please provider a BREAD browse-list or a column for this relationship!
        </div>
        <p class="description" v-if="translate(options.description, true) !== ''">
            {{ translate(options.description, true) }}
        </p>
    </div>
</template>

<script>
export default {
    props: ['options', 'value', 'column', 'relationships'],
    computed: {
        relationship: function () {
            var method = this.column.column;

            return this.relationships.filter(function (relationship) {
                return relationship.method == method;
            })[0];
        },
        relationshipLayout: function () {
            var layout_name = this.options.browse_list;
            return this.relationship.bread.layouts.filter(function (layout) {
                return layout.name == layout_name;
            })[0];
        },
    },
};
</script>