<template>
    <card :title="__('voyager::generic.breads')" icon="bread">
        <template #actions>
            <button class="button" @click.stop="loadBreads">
                <icon icon="refresh" class="animate-spin-reverse" :size="4" v-if="loading" />
                <span>{{ __('voyager::builder.reload_breads') }}</span>
            </button>
            <locale-picker :small="false" />
        </template>
        <div class="voyager-table striped" :class="[loading ? 'loading' : '']">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('voyager::generic.table') }}</th>
                        <th class="hidden md:table-cell">{{ __('voyager::generic.slug') }}</th>
                        <th class="hidden md:table-cell">{{ __('voyager::builder.name_singular') }}</th>
                        <th class="hidden md:table-cell">{{ __('voyager::builder.name_plural') }}</th>
                        <th class="hidden md:table-cell">{{ __('voyager::builder.lists') }}</th>
                        <th class="hidden md:table-cell">{{ __('voyager::builder.views') }}</th>
                        <th style="text-align:right !important">{{ __('voyager::generic.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="table in tables" v-bind:key="table">
                        <td>{{ table }}</td>
                        <td class="hidden md:table-cell">
                            <span v-if="hasBread(table)">{{ translate(getBread(table).slug) }}</span>
                        </td>
                        <td class="hidden md:table-cell">
                            <span v-if="hasBread(table)">{{ translate(getBread(table).name_singular) }}</span>
                        </td>
                        <td class="hidden md:table-cell">
                            <span v-if="hasBread(table)">{{ translate(getBread(table).name_plural) }}</span>
                        </td>
                        <td class="hidden md:table-cell">
                            <span v-if="hasBread(table)">{{ getBread(table).layouts.where('type', 'list').length }}</span>
                        </td>
                        <td class="hidden md:table-cell">
                            <span v-if="hasBread(table)">{{ getBread(table).layouts.where('type', 'view').length }}</span>
                        </td>
                        <td class="flex flex-no-wrap justify-end">
                            <template v-if="hasBread(table)">
                                <a class="button blue" :href="route('voyager.'+translate(getBread(table).slug, true)+'.browse')">
                                    <icon icon="globe" :size="4" />
                                    <span>
                                        {{ __('voyager::generic.browse') }}
                                    </span>
                                </a>
                                <button class="button green" @click="backupBread(table)">
                                    <icon icon="clock" :class="[backingUp ? 'animate-spin-reverse' : '']" :size="4" />
                                    <span>
                                        {{ __('voyager::generic.backup') }}
                                    </span>
                                </button>
                                <dropdown v-if="getBackupsForTable(table).length > 0" placement="bottom">
                                    <div>
                                        <a v-for="(bu, i) in getBackupsForTable(table)"
                                            :key="'rollback-'+i"
                                            href="#"
                                            @click.prevent="rollbackBread(table, bu)"
                                            class="link">
                                            {{ bu.date }}
                                        </a>
                                    </div>
                                    <template #opener>
                                        <button class="button green">
                                            <icon icon="clock" :size="4" />
                                            <span>
                                                {{ __('voyager::builder.rollback') }} ({{ getBackupsForTable(table).length }})
                                            </span>
                                        </button>
                                    </template>
                                </dropdown>
                                <a class="button yellow" :href="route('voyager.bread.edit', table)">
                                    <icon icon="pencil" :size="4" />
                                    <span>
                                        {{ __('voyager::generic.edit') }}
                                    </span>
                                </a>
                                <button class="button red" @click="deleteBread(table)">
                                    <icon :icon="deleting ? 'refresh' : 'trash'" :class="[deleting ? 'animate-spin-reverse' : '']" :size="4" />
                                    <span>
                                        {{ __('voyager::generic.delete') }}
                                    </span>
                                </button>
                            </template>
                            <a v-else class="button green" :href="route('voyager.bread.create', table)">
                                <icon icon="plus" :size="4" />
                                <span class="hidden md:block">
                                    {{ __('voyager::generic.add_type', { type: __('voyager::generic.bread') }) }}
                                </span>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </card>
</template>

<script>
import fetch from '../../js/fetch';

export default {
    props: ['tables'],
    data: function () {
        return {
            breads: [],
            backups: [],
            loading: false,
            backingUp: false,
            deleting: false,
        };
    },
    methods: {
        hasBread: function (table) {
            return this.getBread(table) !== null;
        },
        getBread: function (table) {
            var bread = null;
            this.breads.forEach(b => {
                if (b.table == table) {
                    bread = b;
                }
            });

            return bread;
        },
        deleteBread: function (table) {
            var vm = this;
            new vm
            .$notification(vm.__('voyager::builder.delete_bread_confirm', {bread: table}))
            .color('yellow')
            .timeout()
            .confirm()
            .show()
            .then(function (result) {
                if (result) {
                    vm.deleting = true;
                    fetch.delete(vm.route('voyager.bread.delete', table))
                    .then(function (response) {
                        new vm.$notification(vm.__('voyager::builder.delete_bread_success', {bread: table})).color('green').timeout().show();
                    })
                    .catch(function (response) {
                        vm.$store.handleAjaxError(response);
                    })
                    .then(function () {
                        vm.loadBreads();
                        vm.deleting = false;
                    });
                }
            });
        },
        backupBread: function (table) {
            var vm = this;
            vm.backingUp = true;

            fetch.post(vm.route('voyager.bread.backup-bread'), {
                table: table
            })
            .then(function (response) {
                new vm.$notification(vm.__('voyager::builder.bread_backed_up', { name: response.data })).timeout().show();
            })
            .catch(function (response) {
                vm.$store.handleAjaxError(response);
            })
            .then(function () {
                vm.backingUp = false;
                vm.loadBreads();
            });
        },
        rollbackBread: function (table, backup) {
            var vm = this;

            fetch.post(vm.route('voyager.bread.rollback-bread'), {
                table: table,
                path: backup.path
            })
            .then(function (response) {
                new vm.$notification(vm.__('voyager::builder.bread_rolled_back', { date: backup.date })).timeout().show();
            })
            .catch(function (response) {
                vm.$store.handleAjaxError(response);
            })
            .then(function () {
                vm.loadBreads();
            });
        },
        getBackupsForTable: function (table) {
            return this.backups.where('table', table);
        },
        loadBreads: function () {
            var vm = this;

            if (vm.loading) {
                return;
            }

            vm.loading = true;
            fetch.post(vm.route('voyager.bread.get-breads'))
            .then(function (response) {
                vm.breads = response.data.breads;
                vm.backups = response.data.backups;
            })
            .catch(function (response) {
                vm.$store.handleAjaxError(response);
            })
            .then(function () {
                vm.loading = false;
            });
        }
    },
    mounted: function () {
        this.loadBreads();
    }
};
</script>