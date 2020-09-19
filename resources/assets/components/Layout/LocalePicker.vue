<template>
    <div v-if="$store.locales.length > 1">
        <dropdown ref="locale_dropdown">
            <div>
                <a
                    v-for="locale in $store.locales"
                    v-bind:key="locale"
                    @click="$store.locale = locale"
                    class="link uppercase"
                    :class="$store.locale == locale ? 'active' : null"
                >
                    {{ locale }}
                </a>
            </div>
            <template #opener>
                <button
                    class="button accent uppercase"
                    :class="[small ? 'small' : '']"
                    @click.prevent.stop="$refs.locale_dropdown.open()"
                >
                    <span>{{ $store.locale }}</span>
                    <icon icon="chevron-down" :size="4" />
                </button>
            </template>
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
    mounted: function () {
        var vm = this;
        document.addEventListener('keydown', function (e) {
            if (vm.$refs.locale_dropdown.isOpen) {
                if (e.key == 'ArrowDown') {
                    vm.$store.nextLocale();
                    e.preventDefault();
                } else if (e.key == 'ArrowUp') {
                    vm.$store.previousLocale();
                    e.preventDefault();
                }
            }
        });
    }
};
</script>