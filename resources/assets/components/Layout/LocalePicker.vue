<template>
    <div v-if="locales.length > 1">
        <dropdown>
            <div>
                <a
                    v-for="l in locales"
                    v-bind:key="locale"
                    @click="locale = l"
                    class="link"
                    :class="locale == l ? 'active' : null"
                >
                    {{ languageForLocale(l) }}
                </a>
            </div>
            <template #opener>
                <button
                    class="button accent"
                    :class="[small ? 'small' : '']"
                >
                    <span>{{ languageForLocale(locale) }}</span>
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
    mounted() {
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey && (e.key == 'ArrowDown' || e.key == 'ArrowRight')) {
                this.nextLocale();
                e.preventDefault();
            } else if (e.ctrlKey && (e.key == 'ArrowUp' || e.key == 'ArrowLeft')) {
                this.previousLocale();
                e.preventDefault();
            }
        });
    },
    methods: {
        languageForLocale(locale) {
            let key = `voyager::generic.languages.${locale}`;
            if (key !== this.__(key)) {
                return this.__(key);
            }

            return locale.toUpperCase();
        }
    },
    computed: {
        locales() {
            return this.$store.locales;
        },
        locale: {
            get() {
                return this.$store.locale;
            },
            set(locale) {
                this.$store.locale = locale;
            }
        },
    }
};
</script>