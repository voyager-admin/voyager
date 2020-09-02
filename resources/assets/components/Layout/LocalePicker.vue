<template>
    <div v-if="$language.locales.length > 1">
        <button
            class="button accent uppercase"
            :class="[small ? 'small' : '']"
            @click.prevent.stop="$refs.locale_dropdown.open()"
        >
            <span>{{ $language.locale }}</span>
            <icon icon="chevron-down" :size="4" />
        </button>
        <dropdown ref="locale_dropdown" :width="24">
            <div>
                <a
                    v-for="locale in $language.locales"
                    v-bind:key="locale"
                    @click="$language.locale = locale"
                    class="link uppercase"
                >
                    {{ locale }}
                </a>
            </div>
        </dropdown>
    </div>
</template>

<script>
export default {
    props: {
        small: {
            type: Boolean,
            default: true,
        },
    },
};
</script>