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
                >
                <modal ref="search_plugin_modal" :title="__('voyager::plugins.plugins')" icon="puzzle" v-on:closed="available.query = ''">
                    <input
                        type="text"
                        class="input w-full mb-3"
                        v-model="available.query"
                        :placeholder="__('voyager::generic.search')"
                        @dblclick="available.query = ''"
                        @keydown.esc="available.query = ''"
                    >
                    <div class="w-full my-3">
                        <badge
                            v-for="(type, i) in availableTypes"
                            :key="i"
                            :color="getPluginTypeColor(type)"
                            :icon="available.currentType == type ? 'x' : null"
                            @click="setAvailableTypeFilter(type)"
                        >
                            {{ __('voyager::plugins.types.'+type) }}
                        </badge>
                    </div>
                    <div v-if="filteredAvailablePlugins.length == 0" class="w-full text-center">
                        <h4>{{ __('voyager::plugins.no_plugins_match_search') }}</h4>
                    </div>
                    <div v-for="(plugin, i) in filteredAvailablePlugins.slice(availableStart, availableEnd)" :key="'plugin-'+i">
                        <div class="flex">
                            <div class="w-3/5">
                                <div class="inline-flex">
                                    <h5 class="mr-2">{{ plugin.name }}</h5>
                                    <badge :color="getPluginTypeColor(plugin.type)">{{ __('voyager::plugins.types.'+plugin.type) }}</badge>
                                </div>
                                <p>{{ plugin.description }}</p>
                                <a v-if="plugin.website" :href="plugin.website" target="_blank">
                                    {{ __('voyager::generic.website') }}
                                </a>
                                <span v-else>&nbsp;</span>
                            </div>
                            <div class="w-2/5 text-right" v-if="!pluginInstalled(plugin)">
                                <input class="input w-full select-none" :value="'composer require '+plugin.repository" @dblclick="copy(plugin)">
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
            <div class="voyager-table striped" :class="[loading ? 'loading' : '']" v-else>
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
                            <td class="ltr:text-right rtl:text-left">
                                <a class="button small" v-if="plugin.website" :href="plugin.website" target="_blank">
                                    <icon icon="globe"></icon>
                                    {{ __('voyager::generic.website') }}
                                </a>
                                <a v-if="plugin.has_settings && plugin.enabled" :href="route('voyager.plugins.settings', plugin.num)" class="button blue small">
                                    <icon icon="cog"></icon>
                                    <span>{{ __('voyager::generic.settings') }}</span>
                                </a>

                                <button v-if="plugin.instructions" class="button blue small" @click="$refs['instructions-modal-'+i][0].open()">
                                    <icon icon="information-circle"></icon>
                                    <span>{{ __('voyager::generic.instructions') }}</span>
                                </button>
                                <modal v-if="plugin.instructions" :ref="'instructions-modal-'+i" :title="__('voyager::generic.instructions')">
                                    <div v-html="plugin.instructions"></div>
                                </modal>
                                <button v-if="plugin.type == 'theme' && !plugin.enabled" class="button small" @click="previewTheme(plugin.name)">
                                    <icon icon="eye"></icon>
                                    <span>{{ __('voyager::generic.preview') }}</span>
                                </button>
                                <button v-if="!plugin.enabled" class="button green small" @click="enablePlugin(plugin, true)">
                                    <icon icon="play"></icon>
                                    <span>{{ __('voyager::generic.enable') }}</span>
                                </button>
                                <button v-else class="button red small" @click="enablePlugin(plugin, false)">
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
        <div v-else-if="!loading" class="w-full text-center">
            <h3>{{ __('voyager::plugins.no_plugins_installed_title') }}</h3>
            <h4>{{ __('voyager::plugins.no_plugins_installed_hint') }}</h4>
        </div>
    </card>
</template>

<script>
import fetch from '../js/fetch';

export default {
    props: ['availablePlugins'],
    data: function () {
        return {
            installed: {
                plugins: [],
                query: '',
                currentType: null,
                page: 0,
                resultsPerPage: 7,
                onlyEnabled: null,
            },
            available: {
                plugins: this.availablePlugins,
                query: '',
                currentType: null,
                page: 0,
                resultsPerPage: 3,
            },
            addPluginModalOpen: false,
            loading: true,
        };
    },
    methods: {
        closeAddPluginModal: function () {
            this.addPluginModalOpen = false;
        },
        copy: function (plugin) {
            this.copyToClipboard('composer require ' + plugin.repository);
            new this.$notification(this.__('voyager::plugins.copy_notice')).timeout().show();
        },
        loadPlugins: function () {
            var vm = this;
            vm.loading = true;
            fetch.post(vm.route('voyager.plugins.get'))
            .then(function (response) {
                vm.installed.plugins = response.data;
            })
            .catch(function (response) {
                vm.$store.handleAjaxError(response);
            })
            .then(function () {
                vm.loading = false;
            });
        },
        enablePlugin: function (plugin, enable) {
            var vm = this;
            var message = this.__('voyager::plugins.enable_plugin_confirm', {name: plugin.name});
            if (!enable) {
                message = this.__('voyager::plugins.disable_plugin_confirm', {name: plugin.name});
            }

            new vm.$notification(message).confirm().timeout().show().then(function (response) {
                if (response) {
                    fetch.post(vm.route('voyager.plugins.enable'), {
                        identifier: plugin.identifier,
                        enable: enable,
                    })
                    .then(function (response) {
                        new vm.$notification(vm.__('voyager::plugins.reload_page')).show();
                    })
                    .catch(function (response) {
                        vm.$store.handleAjaxError(response);
                    })
                    .then(function () {
                        vm.loadPlugins();
                    });
                }
            });
        },
        previewTheme: function (name) {
            var file = document.createElement('link');
            file.setAttribute('rel', 'stylesheet');
            file.setAttribute('type', 'text/css');
            file.setAttribute('href', this.asset('plugin/'+slugify(name, { lower: true })+'.css'));
            document.getElementsByTagName('head')[0].appendChild(file);

            new this.$notification(this.__('voyager::plugins.preview_theme', {name: name})).timeout().show();
        },
        getPluginTypeColor: function (type) {
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
        pluginInstalled: function (plugin) {
            return this.installed.plugins.where('repository', plugin.repository).length > 0;
        },
        setTypeFilter: function (type) {
            if (this.installed.currentType == type) {
                this.installed.currentType = null;
            } else {
                this.installed.currentType = type;
            }
            this.installed.page = 0;
        },
        setAvailableTypeFilter: function (type) {
            if (this.available.currentType == type) {
                this.available.currentType = null;
            } else {
                this.available.currentType = type;
            }
            this.available.page = 0;
        }
    },
    computed: {
        filteredAvailablePlugins: function () {
            var vm = this;
            var query = vm.available.query.toLowerCase();
            return vm.available.plugins.filter(function (plugin) {
                if (vm.available.currentType !== null) {
                    return plugin.type == vm.available.currentType;
                }

                return true;
            }).filter(function (plugin) {
                return plugin.keywords.filter(function (keyword) {
                    return keyword.toLowerCase().indexOf(query) >= 0;
                }).length > 0;
            });
        },
        filteredInstalledPlugins: function () {
            var vm = this;
            var query = vm.installed.query.toLowerCase();
            return vm.installed.plugins.filter(function (plugin) {
                if (vm.installed.onlyEnabled === true) {
                    return plugin.enabled;
                } else if (vm.installed.onlyEnabled === false) {
                    return !plugin.enabled;
                }

                return true;
            }).filter(function (plugin) {
                if (vm.installed.currentType !== null) {
                    return plugin.type == vm.installed.currentType;
                }

                return true;
            }).filter(function (plugin) {
                return plugin.description.toLowerCase().indexOf(query) >= 0 || plugin.name.toLowerCase().indexOf(query) >= 0;
            });
        },
        availableStart: function () {
            return this.available.page * this.available.resultsPerPage;
        },
        availableEnd: function () {
            return this.availableStart + this.available.resultsPerPage;
        },
        availablePages: function () {
            return Math.ceil(this.filteredAvailablePlugins.length / this.available.resultsPerPage);
        },
        installedStart: function () {
            return this.installed.page * this.installed.resultsPerPage;
        },
        installedEnd: function () {
            return this.installedStart + this.installed.resultsPerPage;
        },
        installedPages: function () {
            return Math.ceil(this.filteredInstalledPlugins.length / this.installed.resultsPerPage);
        },
        availableTypes: function () {
            return this.available.plugins.map(function (plugin) {
                return plugin.type;
            }).filter(function (value, index, self) {
                return self.indexOf(value) === index;
            });
        },
        installedTypes: function () {
            return this.installed.plugins.map(function (plugin) {
                return plugin.type;
            }).filter(function (value, index, self) {
                return self.indexOf(value) === index;
            });
        },
    },
    mounted: function () {
        var vm = this;

        vm.loadPlugins();

        var type = vm.getParameterFromUrl('type', null);
        if (type !== null) {
            vm.available.currentType = type;
            vm.$refs.search_plugin_modal.open();
        }
    },
    created: function () {
        var vm = this;

        vm.$watch(
            () => vm.available.query,
            function () {
                vm.available.page = 0;
            }
        );
        vm.$watch(
            () => vm.installed.query,
            function () {
                vm.installed.page = 0;
            }
        );
        vm.$watch(
            () => vm.installed.onlyEnabled,
            function () {
                vm.installed.page = 0;
            }
        );
    },
};
</script>