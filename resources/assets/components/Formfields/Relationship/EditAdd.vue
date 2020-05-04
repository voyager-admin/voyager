<template>
    <div>
        <label class="label" v-if="translate(options.label, true) !== ''">{{ translate(options.label, true) }}</label>
        <div v-if="options.browse_list">
            
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
    props: ['options', 'value', 'relationships'],
};
</script>