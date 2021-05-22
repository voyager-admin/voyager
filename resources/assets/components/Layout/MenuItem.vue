<template>
<div class="menuitem">
    <div class="item" :class="[active ? 'active' : '']">
        <div class="inline-flex items-center w-full">
            <inertia-link :href="href" class="text-sm leading-5 link inline-flex items-center space-x-2 w-full" @click="clickItem">
                <icon v-if="icon !== '' && icon !== null" :icon="icon" class="icon" :size="iconSize" />
                <span>{{ title }}</span>
            </inertia-link>
        </div>
        <div class="flex-shrink-0 cursor-pointer inline-flex items-center" @click="open = !open" v-if="hasChildren">
            <icon icon="chevron-up" v-if="open" :size="4" class="icon" />
            <icon icon="chevron-down" v-else :size="4" class="icon" />
        </div>
    </div>
    
    <div v-if="hasChildren" class="ltr:ml-5 rtl:mr-5">
        <collapse-transition :duration="200">
            <slot v-if="open" />
        </collapse-transition>
    </div>
</div>
</template>

<script>
export default {
    props: {
        icon: {
            type: String,
            required: true
        },
        title: {
            type: String,
            required: true
        },
        href: {
            type: String,
            required: false,
            default: '#'
        },
        active: {
            type: Boolean,
            default: false,
        },
        isOpen: {
            type: Boolean,
            default: false,
        },
        hasChildren: {
            type: Boolean,
            default: false,
        },
        iconSize: {
            type: Number,
            default: 6,
        }
    },
    data() {
        return {
            open: this.isOpen,
        }
    },
    methods: {
        clickItem(e) {
            if (this.href == '' || this.href == '#') {
                e.preventDefault();
                this.open = !this.open;
            }
        }
    },
    created() {
        if (this.active) {
            this.open = true;
        }
    }
};
</script>

<style lang="scss" scoped>
.menuitem {
    .item {
        @apply flex items-center justify-between rounded-md font-medium mt-1 px-2 py-2;
    }
}
</style>