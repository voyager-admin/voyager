<template>
    <card :title="__('voyager::plugins.plugins')" icon="puzzle">
        <template #actions>
            <div class="flex items-center">
                <input
                    type="text"
                    v-model="installed.query"
                    class="input w-full small ltr:mr-2 rtl:ml-2"
                    :placeholder="__('voyager::plugins.search_installed_plugins')"
                    @dblclick="installed.query = ''"
                    @keydown.esc="installed.query = ''"
                >
                <button class="button" @click="reload">{{ __('voyager::generic.reload') }}</button>
                <button class="button" @click="checkUpdates">{{ __('voyager::plugins.check_for_updates') }}</button>
                <modal ref="search_plugin_modal" :title="__('voyager::plugins.plugins')" icon="puzzle" v-on:closed="available.query = ''">
                    <input
                        type="text"
                        class="input w-full mb-3"
                        v-model="available.query"
                        :placeholder="__('voyager::generic.search')"
                        @dblclick="available.query = ''"
                    >
                    <div v-if="filteredAvailablePlugins.length == 0" class="w-full text-center">
                        <h4>{{ __('voyager::plugins.no_plugins_match_search') }}</h4>
                    </div>
                    <div v-for="(plugin, i) in filteredAvailablePlugins.slice(availableStart, availableEnd)" :key="'plugin-'+i">
                        <div class="flex">
                            <div class="w-3/5">
                                <div class="w-full inline-flex">
                                    <h5 class="mr-2">{{ translate(plugin.name) }}</h5>
                                    <badge icon="download">{{ plugin.downloads }}</badge>
                                    <badge icon="thumb-up">{{ plugin.favers }}</badge>
                                </div>
                                <p>{{ translate(plugin.description) }}</p>
                                <div class="w-full space-x-1.5">
                                    <a v-if="plugin.url" :href="plugin.url" target="_blank">
                                        {{ __('voyager::generic.website') }}
                                    </a>
                                    <a v-if="plugin.repository" :href="plugin.repository" target="_blank">
                                        {{ __('voyager::generic.repository') }}
                                    </a>
                                </div>
                            </div>
                            <div class="w-2/5 text-right" v-if="!pluginInstalled(plugin)">
                                <input class="input w-full select-none" :value="'composer require '+plugin.name" @dblclick="copy(plugin)">
                            </div>
                            <div class="w-2/5 text-right" v-else>
                                <badge color="orange">{{ __('voyager::plugins.plugin_installed') }}</badge>
                            </div>
                        </div>
                        <hr class="w-full bg-gray-300 my-4">
                    </div>
                    <div class="w-full">
                        <pagination
                            :page-count="availablePages"
                            @update:model-value="available.page = $event - 1"
                            :model-value="available.page + 1"
                            :first-last-buttons="false"
                        />
                    </div>
                    <template #opener>
                        <button class="button">
                            <icon icon="search"></icon>
                            <span>{{ __('voyager::plugins.search_plugins') }}</span>
                        </button>
                    </template>
                </modal>
            </div>
        </template>
        <slide-down-transition>
            <alert color="blue" v-if="update.installed > 0" class="mb-4">
                <template #title>
                    {{ __('voyager::plugins.checking_for_updates', { x: update.checked, y: update.installed }) }}
                </template>
                <div v-if="update.updates.length > 0">
                    <span v-html="__('voyager::plugins.updates_available')"></span>
                    <ul class="my-2">
                        <li v-for="(plugin, i) in update.updates" :key="`update-${i}`">
                            {{ plugin.repo }} ({{ plugin.current }} => {{ plugin.newest }})
                        </li>
                    </ul>
                    <span v-html="__('voyager::plugins.updates_available_install')"></span>
                </div>
            </alert>
        </slide-down-transition>
        <div class="w-full flex">
            <div class="flex-grow">
                <badge
                    v-for="(type, i) in installedTypes"
                    :key="i" :color="getPluginTypeColor(type)"
                    :icon="installed.currentType == type ? 'x' : null"
                    @click="setTypeFilter(type)"
                >
                    {{ __('voyager::plugins.types.'+type) }}
                </badge>
            </div>
            <div class="flex-grow-0">
                <badge
                    color="green"
                    @click="installed.onlyEnabled === true ? installed.onlyEnabled = null : installed.onlyEnabled = true"
                    :icon="installed.onlyEnabled === true ? 'x' : null"
                >
                    {{ __('voyager::plugins.only_enabled') }}
                </badge>
                <badge
                    color="red"
                    @click="installed.onlyEnabled === false ? installed.onlyEnabled = null : installed.onlyEnabled = false"
                    :icon="installed.onlyEnabled === false ? 'x' : null"
                >
                    {{ __('voyager::plugins.only_disabled') }}
                </badge>
            </div>
        </div>
        <div v-if="installed.plugins.length > 0">
            <div v-if="filteredInstalledPlugins.length == 0" class="w-full text-center">
                <h3>{{ __('voyager::plugins.no_plugins_match_search') }}</h3>
            </div>
            <div class="voyager-table striped" :class="$store.pageLoading ? 'loading' : null" v-else>
                <table id="bread-builder-browse">
                    <thead>
                        <tr>
                            <th>
                                {{ __('voyager::generic.name') }}
                            </th>
                            <th>
                                {{ __('voyager::generic.description') }}
                            </th>
                            <th>
                                {{ __('voyager::generic.type') }}
                            </th>
                            <th>
                                {{ __('voyager::generic.version') }}
                            </th>
                            <th class="ltr:text-right rtl:text-left">
                                {{ __('voyager::generic.actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(plugin, i) in filteredInstalledPlugins.slice(installedStart, installedEnd)" :key="'installed-plugin-'+i">
                            <td>{{ translate(plugin.name) }}</td>
                            <td>{{ translate(plugin.description) }}</td>
                            <td>
                                <badge :color="getPluginTypeColor(plugin.type)">
                                    {{ __('voyager::plugins.types.'+plugin.type) }}
                                </badge>
                            </td>
                            <td>
                                {{ plugin.version || '-' }}
                            </td>
                            <td class="w-full inline-flex justify-end">
                                <a class="button small" v-if="plugin.website" :href="translate(plugin.website)" target="_blank">
                                    <icon icon="globe"></icon>
                                    {{ __('voyager::generic.website') }}
                                </a>
                                
                                <modal v-if="plugin.settings_component && plugin.enabled" :title="__('voyager::generic.settings')">
                                    <component :is="plugin.settings_component"></component>
                                    <template #opener>
                                        <button class="button small">
                                        <icon icon="cog"></icon>
                                        <span>{{ __('voyager::generic.settings') }}</span>
                                    </button>
                                    </template>
                                </modal>

                                <modal v-if="plugin.instructions_component" :title="__('voyager::generic.instructions')">
                                    <component :is="plugin.instructions_component"></component>
                                    <template #opener>
                                        <button class="button small">
                                        <icon icon="eye"></icon>
                                        <span>{{ __('voyager::generic.instructions') }}</span>
                                    </button>
                                    </template>
                                </modal>

                                <button v-if="plugin.type == 'theme' && !plugin.enabled" class="button small" @click="previewTheme(plugin.name)">
                                    <icon icon="eye"></icon>
                                    <span>{{ __('voyager::generic.preview') }}</span>
                                </button>
                                <button v-if="!plugin.enabled" class="button small green" @click="enablePlugin(plugin, true)">
                                    <icon icon="play"></icon>
                                    <span>{{ __('voyager::generic.enable') }}</span>
                                </button>
                                <button v-else class="button small red" @click="enablePlugin(plugin, false)">
                                    <icon icon="stop"></icon>
                                    <span>{{ __('voyager::generic.disable') }}</span>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="w-full mt-2">
                <pagination
                    :page-count="installedPages"
                    @update:modelValue="installed.page = $event - 1"
                    :modelValue="installed.page + 1"
                    :first-last-buttons="false"
                ></pagination>
            </div>
        </div>
        <div v-else class="w-full text-center">
            <h3>{{ __('voyager::plugins.no_plugins_installed_title') }}</h3>
            <h4>{{ __('voyager::plugins.no_plugins_installed_hint') }}</h4>
        </div>
    </card>
</template>

<script>
import axios from 'axios';
import { Inertia } from '@inertiajs/inertia';

const compare = require('semver-compare');

export default {
    props: ['installedPlugins'],
    data() {
        return {
            installed: {
                plugins: this.installedPlugins,
                query: '',
                currentType: null,
                page: 0,
                resultsPerPage: 7,
                onlyEnabled: null,
            },
            available: {
                plugins: [],
                query: '',
                currentType: null,
                page: 0,
                resultsPerPage: 3,
            },
            update: {
                updates: [],
                checked: 0,
                installed: 0,
            },
            addPluginModalOpen: false,
            pp: [],
        };
    },
    methods: {
        reload() {
            this.$inertia.get(route('voyager.plugins.index'));
        },
        closeAddPluginModal() {
            this.addPluginModalOpen = false;
        },
        copy(plugin) {
            this.copyToClipboard('composer require ' + plugin.repository);
            new this.$notification(this.__('voyager::plugins.copy_notice')).timeout().show();
        },
        enablePlugin(plugin, enable) {
            var message = this.__('voyager::plugins.enable_plugin_confirm', {name: plugin.name});
            if (!enable) {
                message = this.__('voyager::plugins.disable_plugin_confirm', {name: plugin.name});
            }

            new this.$notification(message).confirm().timeout().show().then((response) => {
                if (response) {
                    axios.post(this.route('voyager.plugins.enable'), {
                        identifier: plugin.identifier,
                        enable: enable,
                    })
                    .then(() => {
                        new this.$notification(this.__('voyager::plugins.reload_page')).show();
                    })
                    .catch(response => {
                        this.handleAjaxError(response);
                    })
                    .then(() => {
                        this.reload();
                    });
                }
            });
        },
        previewTheme(name) {
            var file = document.createElement('link');
            file.setAttribute('rel', 'stylesheet');
            file.setAttribute('type', 'text/css');
            file.setAttribute('href', this.asset('plugin/'+slugify(name, { lower: true })+'.css'));
            document.getElementsByTagName('head')[0].appendChild(file);

            new this.$notification(this.__('voyager::plugins.preview_theme', {name: name})).timeout().show();
        },
        getPluginTypeColor(type) {
            if (type == 'authentication') {
                return 'green';
            } else if (type == 'authorization') {
                return 'blue';
            } else if (type == 'menu') {
                return 'yellow';
            } else if (type == 'theme') {
                return 'purple';
            } else if (type == 'widget') {
                return 'orange';
            } else if (type == 'formfield') {
                return 'teal';
            }

            return 'red';
        },
        pluginInstalled(plugin) {
            return this.installed.plugins.where('repository', plugin.repository).length > 0;
        },
        setTypeFilter(type) {
            if (this.installed.currentType == type) {
                this.installed.currentType = null;
            } else {
                this.installed.currentType = type;
            }
            this.installed.page = 0;
        },
        setAvailableTypeFilter(type) {
            if (this.available.currentType == type) {
                this.available.currentType = null;
            } else {
                this.available.currentType = type;
            }
            this.available.page = 0;
        },
        checkUpdates() {
            this.$store.pageLoading = true;
            this.update.updates = [];
            this.update.checked = 0;

            let repos = [];
            this.installed.plugins.forEach((plugin) => {
                if (repos.indexOf(plugin.repository) === -1) {
                    repos.push(plugin.repository);
                }
            });

            this.update.installed = repos.length;

            repos.forEach((repo) => {
                axios.get(`https://repo.packagist.org/p2/${repo}.json`)
                .then((response) => {
                    let newest = response.data.packages[repo][0].version_normalized;
                    let current = this.installed.plugins.where('repository', repo).first().version_normalized;
                    if (compare(newest, current) === 1) {
                        this.update.updates.push({
                            repo,
                            current: this.installed.plugins.where('repository', repo).first().version,
                            newest: response.data.packages[repo][0].version
                        });
                    }
                })
                .catch()
                .then(() => {
                    this.update.checked++;

                    if (this.update.checked >= repos.length) {
                        if (this.update.updates.length == 0) {
                            this.update.updates = [];
                            this.update.checked = 0;
                            this.update.installed = 0;

                            new this.$notification(this.__('voyager::plugins.no_updates')).timeout().show();
                        }
                        this.$store.pageLoading = false;
                    }
                });
            });
        },
        getAvailablePlugins(url = null) {
            this.$store.pageLoading = true;
            if (url === null) {
                url = 'https://packagist.org/search.json?tags=voyager2-plugin';
                this.available.plugins = [];
            }
            axios.get(url)
                .then((response) => {
                    // TODO: Filter out "unwanted" plugins here
                    this.available.plugins = [...this.available.plugins, ...response.data.results];
                    if (response.data.hasOwnProperty('next')) {
                        this.getAvailablePlugins(response.data.next);
                    } else {
                        this.$store.pageLoading = false;
                    }
                })
                .catch((response) => {
                    new this.$notification(this.__('voyager::plugins.error_loading_plugins')).color('red').timeout().show();
                });
        }
    },
    computed: {
        filteredAvailablePlugins() {
            var query = this.available.query.toLowerCase();
            return this.available.plugins.filter((plugin) => {
                if (this.available.currentType !== null) {
                    return plugin.type == this.available.currentType;
                }

                return true;
            }).filter((plugin) => {
                return plugin.name.toLowerCase().includes(query) || plugin.description.toLowerCase().includes(query);
            });
        },
        filteredInstalledPlugins() {
            var query = this.installed.query.toLowerCase();
            return this.installed.plugins.filter((plugin) => {
                if (this.installed.onlyEnabled === true) {
                    return plugin.enabled;
                } else if (this.installed.onlyEnabled === false) {
                    return !plugin.enabled;
                }

                return true;
            }).filter((plugin) => {
                if (this.installed.currentType !== null) {
                    return plugin.type == this.installed.currentType;
                }

                return true;
            }).filter((plugin) => {
                return plugin.description.toLowerCase().indexOf(query) >= 0 || plugin.name.toLowerCase().indexOf(query) >= 0;
            });
        },
        availableStart() {
            return this.available.page * this.available.resultsPerPage;
        },
        availableEnd() {
            return this.availableStart + this.available.resultsPerPage;
        },
        availablePages() {
            return Math.ceil(this.filteredAvailablePlugins.length / this.available.resultsPerPage);
        },
        installedStart() {
            return this.installed.page * this.installed.resultsPerPage;
        },
        installedEnd() {
            return this.installedStart + this.installed.resultsPerPage;
        },
        installedPages() {
            return Math.ceil(this.filteredInstalledPlugins.length / this.installed.resultsPerPage);
        },
        installedTypes() {
            return this.installed.plugins.map((plugin) => {
                return plugin.type;
            }).filter((value, index, self) => {
                return self.indexOf(value) === index;
            });
        },
    },
    mounted() {
        var type = this.getParameterFromUrl('type', null);
        if (type !== null) {
            this.available.currentType = type;
            this.$refs.search_plugin_modal.open();
        }
        this.getAvailablePlugins();
    },
    created() {
        this.$watch(() => this.available.query, () => {
            this.available.page = 0;
        });
        this.$watch(() => this.installed.query, () => {
            this.installed.page = 0;
        });
        this.$watch(() => this.installed.onlyEnabled, () => {
            this.installed.page = 0;
        });
    },
};
</script>