<template>
    <div>
        <div v-if="show == 'query'">
            <select class="input small w-full" @change="$emit('input', $event.target.value)" v-bind:value="value">
                
            </select>
        </div>
        <div v-else>
            <div class="inline-flex" v-for="(option, i) in displayValues" :key="i">
                {{ option }}
                <span v-if="displayValues.length !== i+1">,&nbsp;</span>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['show', 'options', 'value', 'translatable'],
    computed: {
        displayValues: function () {
            if (this.isArray(this.value)) {
                return this.value;
            }

            return [];
        }
    },
};
</script>