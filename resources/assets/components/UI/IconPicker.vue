<template>
    <div>
        <input type="text" class="input w-full mb-3" :placeholder="__('voyager::generic.search_icons')" v-model="query" />
        <div class="grid grid-cols-12 gap-1">
            <button
                class="button blue icon-only justify-center my-1"
                v-for="(icon, i) in filteredIcons.slice(start, end)"
                :key="'icon-' + i"
                @dblclick="selectIcon(icon.usable)"
                v-tooltip="icon.readable">
                <icon :icon="icon.name" :type="icon.style" :size="6" />
            </button>
        </div>
        <div class="button-group mt-2">
            <button
                class="button blue"
                :class="page == (i - 1) ? 'active' : ''"
                v-for="i in pages"
                @click="page = (i - 1)"
                :key="'page-button-'+i">
                {{ i }}
            </button>
        </div>
    </div>
</template>
<script>
import { icons } from '@bytegem/vue-heroicons';

export default {
    data: function () {
        return {
            query: '',
            page: 0,
            resultsPerPage: 120,
        };
    },
    methods: {
        selectIcon: function (icon) {
            this.$emit('select', icon);
        },
        readableName: function (icon) {
            return this.title_case(icon.replace(/^uni/g,''));
        }
    },
    computed: {
        start: function () {
            return this.page * this.resultsPerPage;
        },
        end: function () {
            return this.start + this.resultsPerPage;
        },
        pages: function () {
            return Math.ceil(this.filteredIcons.length / this.resultsPerPage);
        },
        filteredIcons: function () {
            var vm = this;
            var q = vm.query.toLowerCase();
            return Object.keys(icons).filter(function (icon) {
                return icon.toLowerCase().includes(q);
            }).map(function (icon) {
                var name = icon.replace('Heroicons', '');
                var style = 'outline';
                if (name.endsWith('Solid')) {
                    style = 'solid';
                }
                name = name.replace('Solid', '').replace('Outline', '');

                return {
                    name: name,
                    usable: name + vm.ucfirst(style),
                    readable: vm.studly(name) + ' ' + style,
                    style: style,
                }
            });
        },
    },
    watch: {
        query: function (q) {
            this.page = 0;
        }
    }
};
</script>