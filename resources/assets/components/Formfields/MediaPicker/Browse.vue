<template>
    <div>
        <slot v-if="show == 'query'"></slot>
        <div v-else>
            <div class="inline-flex items-center">
                
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['show', 'options', 'value', 'translatable'],
    data: function () {
        return {
            
        };
    },
};
</script>