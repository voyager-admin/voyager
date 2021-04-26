<template>
    <div v-if="locales.length > 1">
        <dropdown ref="locale_dropdown">
            <div>
                <a
                    v-for="l in locales"
                    v-bind:key="locale"
                    @click="locale = l"
                    class="link uppercase"
                    :class="locale == l ? 'active' : null"
                >
                    {{ l }}
                </a>
            </div>
            <template #opener>
                <button
                    class="button accent uppercase"
                    :class="[small ? 'small' : '']"
                    @click.prevent.stop="$refs.locale_dropdown.open()"
                >
                    <span>{{ locale }}</span>
                    <icon icon="chevron-down" :size="4" />
                </button>
            </template>
        </dropdown>
    </div>
</template>

<script>
import { usePage } from '@inertiajs/inertia-vue3';

export default {
    props: {
        small: {
            type: Boolean,
            default: true,
        },
    },
    mounted() {
        document.addEventListener('keydown', (e) => {
            if (this.$refs.locale_dropdown && this.$refs.locale_dropdown.isOpen) {
                if (e.key == 'ArrowDown') {
                    this.nextLocale();
                    e.preventDefault();
                } else if (e.key == 'ArrowUp') {
                    this.previousLocale();
                    e.preventDefault();
                }
            }
        });
    },
    computed: {
        locales() {
            return usePage().props.value.locales;
        },
        locale: {
            get() {
                return usePage().props.value.locale;
            },
            set(locale) {
                usePage().props.value.locale = locale;
            }
        },
    }
};
</script>