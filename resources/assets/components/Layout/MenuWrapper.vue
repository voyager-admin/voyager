<template>
    <div v-for="(item, i) in items" :key="i">
        <hr class="my-3 sidebar-border" v-if="item.divider" />
        <menuItem
            v-else
            :title="item.title"
            :icon="item.icon"
            :href="item.href"
            :active="isItemActive(item)"
            :hasChildren="item.children.length > 0"
            :iconSize="iconSize"
        >
            <div v-if="item.children.length > 0">
                <menuWrapper
                    :items="item.children"
                    :current-url="currentUrl"
                />
            </div>
        </menuItem>
    </div>
</template>

<script>
import MenuItem from './MenuItem.vue';

export default {
    components: {
        MenuItem,
        'menuWrapper': this,
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
        iconSize: {
            type: Number,
            default: 6,
        }
    },
    methods: {
        isItemActive(item) {
            var url = item.href;
            if (!url.endsWith('/')) {
                url = url + '/';
            }

            if (item.exact === true) {
                return this.currentUrl == url;
            }

            return this.currentUrl.startsWith(url);
        }
    }
}
</script>