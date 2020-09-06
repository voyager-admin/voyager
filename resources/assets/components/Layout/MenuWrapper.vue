<template>
    <div>
        <div v-for="(item, i) in items" :key="i">
            <hr class="my-3 sidebar-border" v-if="item.divider" />
            <menu-item
                v-else
                :title="item.title"
                :icon="item.icon"
                :href="item.href"
                :active="isItemActive(item)"
                :hasChildren="item.children.length > 0"
            >
                <div v-if="item.children.length > 0">
                    <menu-wrapper
                        :items="item.children"
                        :current-url="currentUrl"
                    />
                </div>
            </menu-item>
        </div>
    </div>
</template>

<script>
import MenuItem from './MenuItem.vue';

export default {
    components: {
        'menu-item': MenuItem,
    },
    props: {
        items: {
            type: Array,
            required: true,
        },
        currentUrl: {
            type: String,
            default: '',
        },
    },
    methods: {
        isItemActive: function (item) {
            var url = item.href;
            if (!url.endsWith('/')) {
                url = url + '/';
            }

            if (item.exact === true) {
                return this.currentUrl == url;
            }

            return this.currentUrl.startsWith(url);
        }
    },
}
</script>