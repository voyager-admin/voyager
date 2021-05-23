<template>
    <card :title="__('voyager::generic.breads')" icon="bread">
        <template #actions>
            <div class="inline-flex space-x-1">
                <button class="button space-x-0" @click="loadBreads" :disabled="loading">
                    <icon icon="refresh" class="animate-spin-reverse" :size="loading ? 4 : 0" :transition-size="4" />
                    <span>{{ __('voyager::generic.reload') }}</span>
                </button>
                <locale-picker :small="false" />
            </div>
        </template>
        <div class="voyager-table striped" :class="[loading ? 'loading' : '']">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('voyager::generic.table') }}</th>
                        <th class="hidden md:table-cell">{{ __('voyager::generic.slug') }}</th>
                        <th class="hidden md:table-cell">{{ __('voyager::builder.name_singular') }}</th>
                        <th class="hidden md:table-cell">{{ __('voyager::builder.name_plural') }}</th>
                        <th class="hidden md:table-cell">{{ __('voyager::builder.model') }}</th>
                        <th class="hidden md:table-cell">{{ __('voyager::builder.lists') }}</th>
                        <th class="hidden md:table-cell">{{ __('voyager::builder.views') }}</th>
                        <th class="flex justify-end">{{ __('voyager::generic.actions') }}</th>
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
                            <span v-if="hasBread(table)">{{ getBread(table).model }}</span>
                        </td>
                        <td class="hidden md:table-cell">
                            <span v-if="hasBread(table)">{{ getBread(table).layouts.where('type', 'list').length }}</span>
                        </td>
                        <td class="hidden md:table-cell">
                            <span v-if="hasBread(table)">{{ getBread(table).layouts.where('type', 'view').length }}</span>
                        </td>
                        <td class="flex flex-no-wrap justify-end space-x-1">
                            <template v-if="hasBread(table)">
                                <a class="button blue" :href="route('voyager.'+translate(getBread(table).slug, true)+'.browse')">
                                    <icon icon="globe" :size="4" />
                                    <span>{{ __('voyager::generic.browse') }}</span>
                                </a>
                                <button class="button green" @click="backupBread(table)">
                                    <icon icon="clock" :class="[backingUp ? 'animate-spin-reverse' : '']" :size="4" />
                                    <span>{{ __('voyager::generic.backup') }}</span>
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
                                            <span>{{ __('voyager::builder.rollback') }} ({{ getBackupsForTable(table).length }})</span>
                                        </button>
                                    </template>
                                </dropdown>
                                <inertia-link as="button" class="button yellow" :href="route('voyager.bread.edit', table)">
                                    <icon icon="pencil" :size="4" />
                                    <span>{{ __('voyager::generic.edit') }}</span>
                                </inertia-link>
                                <button class="button red" @click="deleteBread(table)">
                                    <icon :icon="deleting ? 'refresh' : 'trash'" :class="[deleting ? 'animate-spin-reverse' : '']" :size="4" />
                                    <span>{{ __('voyager::generic.delete') }}</span>
                                </button>
                            </template>
                            <inertia-link as="button" v-else class="button green" :href="route('voyager.bread.create', table)">
                                <icon icon="plus" :size="4" />
                                <span class="hidden md:block">
                                    {{ __('voyager::generic.add_type', { type: __('voyager::generic.bread') }) }}
                                </span>
                            </inertia-link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </card>
</template>

<script>
import axios from 'axios';

export default {
    props: ['tables'],
    data() {
        return {
            breads: [],
            backups: [],
            loading: false,
            backingUp: false,
            deleting: false,
        };
    },
    methods: {
        hasBread(table) {
            return this.getBread(table) !== null;
        },
        getBread(table) {
            var bread = null;
            this.breads.forEach(b => {
                if (b.table == table) {
                    bread = b;
                }
            });

            return bread;
        },
        deleteBread(table) {
            new this
            .$notification(this.__('voyager::builder.delete_bread_confirm', {bread: table}))
            .color('yellow')
            .timeout()
            .confirm()
            .show()
            .then((result) => {
                if (result) {
                    this.deleting = true;
                    axios.delete(this.route('voyager.bread.delete', table))
                    .then(() => {
                        new this.$notification(this.__('voyager::builder.delete_bread_success', {bread: table})).color('green').timeout().show();
                    })
                    .catch((response) => {})
                    .then(() => {
                        this.loadBreads();
                        this.deleting = false;
                    });
                }
            });
        },
        backupBread(table) {
            this.backingUp = true;

            axios.post(this.route('voyager.bread.backup-bread'), {
                table: table
            })
            .then((response) => {
                new this.$notification(this.__('voyager::builder.bread_backed_up', { name: response.data })).timeout().show();
            })
            .catch((response) => {})
            .then(() => {
                this.backingUp = false;
                this.loadBreads();
            });
        },
        rollbackBread(table, backup) {
            axios.post(this.route('voyager.bread.rollback-bread'), {
                table: table,
                path: backup.path
            })
            .then(() => {
                new this.$notification(this.__('voyager::builder.bread_rolled_back', { date: backup.date })).timeout().show();
            })
            .catch((response) => {})
            .then(() => {
                this.loadBreads();
            });
        },
        getBackupsForTable(table) {
            return this.backups.where('table', table);
        },
        loadBreads() {
            if (this.loading) {
                return;
            }

            this.loading = true;
            axios.post(this.route('voyager.bread.get-breads'))
            .then((response) => {
                this.breads = response.data.breads;
                this.backups = response.data.backups;
            })
            .catch((response) => {})
            .then(() => {
                this.loading = false;
            });
        }
    },
    mounted() {
        this.loadBreads();
    }
};
</script>