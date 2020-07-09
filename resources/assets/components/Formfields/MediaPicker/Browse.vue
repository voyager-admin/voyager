<template>
    <div>
        <slot v-if="show == 'query'"></slot>
        <div v-else>
            <div class="inline-flex items-center space-x-1 space-y-1">
                <div v-for="(file, i) in value" :key="i">
                    <div v-if="options.icons" v-tooltip="file.relative_path + file.name">
                        <img :src="file.url" class="rounded object-contain h-16 max-w-full" v-if="mimeMatch(file.type, 'image/*')" />
                        <div v-else class="h-16">
                            <icon icon="document" size="16"></icon>
                        </div>
                    </div>
                    <span v-else v-tooltip="file.relative_path + file.name">
                        {{ file.name }}
                    </span>
                    
                </div>
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