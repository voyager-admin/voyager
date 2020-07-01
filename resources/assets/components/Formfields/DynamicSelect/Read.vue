<template>
    <div>
        <div class="inline-flex" v-for="(option, i) in displayValues" :key="i">
            {{ option }}
            <span v-if="displayValues.length !== i+1">,&nbsp;</span>
        </div>
    </div>
</template>

<script>
export default {
    props: ['options', 'data', 'translatable'],
    computed: {
        displayValues: function () {
            if (this.data !== null && this.data !== '') {
                try {
                    var json = JSON.parse(this.data);
                    if (this.isArray(json)) {
                        return json;
                    }
                } catch { }
            }

            return [];
        }
    },
};
</script>