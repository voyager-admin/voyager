<template>
    <div>
        <card :title="__('voyager::settings.settings')" icon="cog">
            <alert v-if="settings.length === 0" color="red" class="mb-2">
                <template #title>{{ __('voyager::settings.no_settings_title') }}</template>
                <p>{{ __('voyager::settings.no_settings') }}</p>
            </alert>
            <template #actions>
                <div class="flex space-x-1 items-center">
                    <input type="text" class="input small" @dblclick="query = ''" @keydown.esc="query = ''" v-model="query" :placeholder="__('voyager::settings.search_settings')">
                    <button class="button accent" @click="save">
                        <icon icon="refresh" class="mr-0 md:mr-1 animate-spin-reverse" :size="4" v-if="savingSettings" />
                        <span>{{ __('voyager::generic.save') }}</span>
                    </button>
                    <dropdown>
                        <div>
                            <div class="grid grid-cols-2">
                                <a v-for="formfield in filterFormfields"
                                    :key="'formfield-'+formfield.type"
                                    href="#"
                                    @click.prevent="addFormfield(formfield)"
                                    class="link rounded">
                                    {{ formfield.name }}
                                </a>
                            </div>
                            <div class="divider"></div>
                            <a
                                :href="route('voyager.plugins.index')+'/?type=formfield'"
                                target="_blank"
                                class="w-full text-center italic link">
                                {{ __('voyager::builder.formfields_more') }}
                            </a>
                        </div>
                        <template #opener>
                            <button class="button green">
                                <icon icon="plus" :size="4" />
                                <span>
                                    {{ __('voyager::builder.add_formfield') }}
                                </span>
                            </button>
                        </template>
                    </dropdown>
                    <locale-picker :small="false" />
                </div>
            </template>
            <tabs v-on:select="currentGroupId = $event" :tabs="groups" ref="tabs">
                <template v-for="(group, i) in groups" :key="'group-'+i" #[group.name]>
                    <div>
                        <div v-for="(setting, i) in settingsByGroup(group.name)" :key="'settings-'+i">
                            <card :title="setting.name">
                                <div class="flex space-x-1" v-if="editMode">
                                    <input
                                        type="text"
                                        class="input small w-full md:w-1/4"
                                        v-model="setting.name"
                                        v-on:input="setting.key = slugify($event.target.value, { lower: true, strict: true })"
                                        :placeholder="__('voyager::generic.name')"
                                    >
                                    <input type="text" class="input small hidden md:block md:w-1/4" v-bind:value="setting.key" disabled :placeholder="__('voyager::generic.key')">
                                    <input type="text" class="input small w-full md:w-1/4" v-bind:value="setting.group" v-on:input="setting.group = slugify($event.target.value, {strict:true,lower:true}); currentEnteredGroup = $event.target.value" :placeholder="__('voyager::generic.group')">
                                    <input type="text" class="input small w-full" v-model="setting.info" v-tooltip="setting.info" :placeholder="__('voyager::generic.info')">
                                </div>
                                <div v-else>
                                    <h4>{{ setting.name }}</h4>
                                    <p class="mx-4">{{ setting.key }}</p>
                                </div>
                                <template #actions v-if="editMode">
                                    <div class="flex items-center mt-1 md:mt-0 space-x-1">
                                        <button class="button small" @click="moveSettingUp(setting)">
                                            <icon icon="chevron-up"></icon>
                                        </button>
                                        <button class="button small" @click="moveSettingDown(setting)">
                                            <icon icon="chevron-down"></icon>
                                        </button>
                                        <slide-in :title="__('voyager::generic.options')">
                                            <template #actions>
                                                <locale-picker />
                                            </template>
                                            <div v-if="getFormfieldByType(setting.type).can_be_translated">
                                                <label class="label mt-4">{{ __('voyager::generic.translatable') }}</label>
                                                <input type="checkbox" class="input" v-model="setting.translatable">
                                            </div>

                                            <component
                                                :is="getFormfieldByType(setting.type).builder_component"
                                                v-model:options="setting.options"
                                                :column="{}"
                                                action="view-options" />
                                            <breadBuilderValidation v-model="setting.validation" />

                                            <template #opener>
                                                <button class="button">
                                                    <icon icon="cog" :size="4"></icon>
                                                    <span>{{ __('voyager::generic.options') }}</span>
                                                </button>
                                            </template>
                                        </slide-in>
                                        <button class="button red" @click="deleteSetting(setting)">
                                            <icon icon="trash" :size="4"></icon>
                                            <span>{{ __('voyager::generic.delete') }}</span>
                                        </button>
                                    </div>
                                </template>
                                <div class="mt-2">
                                    <alert v-if="getErrors(setting).length > 0" color="red" class="my-2">
                                        <ul class="list-disc ml-4">
                                            <li v-for="(error, i) in getErrors(setting)" :key="'error-'+i">
                                                {{ error }}
                                            </li>
                                        </ul>
                                    </alert>
                                    <component
                                        :is="getFormfieldByType(setting.type).component"
                                        :model-value="data(setting, null)"
                                        @update:model-value="data(setting, $event)"
                                        :options="setting.options"
                                        :column="{}"
                                        action="edit" />
                                </div>
                            </card>
                        </div>
                    </div>
                    <div v-if="groupedSettings.length == 0" class="w-full text-center">
                        <h4>{{ __('voyager::settings.no_settings_in_group') }}</h4>
                        <h6 v-if="query !== ''">{{ __('voyager::settings.search_warning') }}</h6>
                    </div>
                </template>
            </tabs>
        </card>
        <collapsible v-if="jsonOutput" :title="__('voyager::builder.json_output')" closed>
            <textarea class="input w-full" rows="10" v-model="jsonSettings"></textarea>
        </collapsible>
    </div>
</template>

<script>
import { usePage } from '@inertiajs/inertia-vue3';
import axios from 'axios';

import BreadBuilderValidation from './Builder/ValidationForm';

export default {
    components: {
        BreadBuilderValidation,
    },
    props: {
        input: {
            type: Array,
            required: true,
        },
        editMode: {
            type: Boolean,
            default: true,
        }
    },
    data() {
        return {
            settings: this.input,
            savingSettings: false,
            currentGroupId: 0,
            currentEnteredGroup: null,
            errors: [],
            query: '',
        };
    },
    methods: {
        settingsByGroup(group) {
            return this.settings.filter((setting) => {
                var in_group = setting.group == group;
                if (group == 'no-group') {
                    in_group = setting.group == null;
                }
                if (this.query !== '') {
                    return in_group && setting.key.indexOf(this.query.toLowerCase()) >= 0;
                }

                return in_group;
            });
        },
        save() {
            this.savingSettings = true;
            this.errors = [];

            axios.post(this.route('voyager.settings.store'), { settings: this.settings })
            .then((response) => {
                new this.$notification(this.__('voyager::settings.settings_saved')).color('green').timeout().show();
            })
            .catch((response) => {
                if (response.status == 422) {
                    this.errors = response.data;
                }
            })
            .then(() => {
                this.savingSettings = false;
            });
        },
        addFormfield(formfield) {
            var group = this.groups[this.currentGroupId].name
            this.settings.push({
                type: JSON.parse(JSON.stringify(formfield.type)),
                group: (group == 'no-group' ? null : group),
                key: '',
                name: '',
                value: null,
                info: '',
                translatable: false,
                options: {},
                validation: [],
            });
        },
        deleteSetting(setting) {
            new this
            .$notification(this.trans_choice('voyager::bread.delete_type_confirm', 1, { type: this.__('voyager::settings.setting') }))
            .color('red')
            .timeout()
            .confirm()
            .show()
            .then((response) => {
                if (response) {
                    this.settings.splice(this.settings.indexOf(setting), 1);

                    if (!this.groups[this.currentGroupId]) {
                        this.currentGroupId = 0;
                        this.$refs.tabs.openByIndex(0);
                    }
                }
            });
        },
        moveSettingUp(setting) {
            if (this.settingsByGroup(setting.group).indexOf(setting) > 0) {
                this.settings = this.settings.moveElementUp(setting);
            }
        },
        moveSettingDown(setting) {
            var group = this.settingsByGroup(setting.group);
            if (group.length - 1 > group.indexOf(setting)) {
                this.settings = this.settings.moveElementDown(setting);
            }
        },
        data(setting, value = null) {
            if (setting.translatable || false && setting.value && this.isString(setting.value)) {
                setting.value = this.get_translatable_object(setting.value);
            }
            if (value !== null) {
                if (setting.translatable || false) {
                    setting.value[usePage().props.value.locale] = value;
                } else {
                    setting.value = value;
                }

                $eventbus.emit('setting-updated', setting);
            }
            if (setting.translatable || false) {
                return this.translate(this.get_translatable_object(setting.value));
            }
            return setting.value;
        },
        getErrors(setting) {
            var key = setting.key;
            if (setting.group !== null) {
                key = setting.group+'.'+setting.key;
            }

            return this.errors[key] || [];
        },
    },
    computed: {
        filterFormfields() {
            return this.$store.formfields.where('in_settings', true);
        },
        groups() {
            var groups = ['no-group'];
            this.settings.forEach((setting) => {
                if (groups.indexOf(setting.group) == -1 && setting.group !== null) {
                    groups.push(setting.group);
                }
            });

            groups = groups.map((group) => {
                return {
                    name: group,
                    title: (group == 'no-group' ? 'No group' : group),
                };
            });

            return groups;
        },
        groupedSettings: {
            get() {
                return this.settingsByGroup(this.groups[this.currentGroupId].name);
            },
            set(settings) {
                var current_group = this.groups[this.currentGroupId].name;
                this.settings = this.settings.filter((setting) => {
                    if (current_group == 'no-group') {
                        return setting.group !== null;
                    }
                    return setting.group !== current_group;
                });
                this.settings = this.settings.concat(settings);
            }
        },
        jsonSettings: {
            get() {
                return JSON.stringify(this.settings, null, 2);
            },
            set(value) {
                
            }
        },
        jsonOutput() {
            return usePage().props.value.json_output;
        }
    },
    created() {
        this.$watch(
            () => this.currentEnteredGroup,
            (value) => {
                if (value == '') {
                    this.settings = this.settings.map((setting) => {
                        if (setting.group == '') {
                            setting.group = null;
                        }

                        return setting;
                    });

                    value = 'no-group';
                }
                for (var group in this.groups) {
                    if (this.groups.hasOwnProperty(group)) {
                        if (this.groups[group].name == value) {
                            this.$refs.tabs.openByIndex(group);
                        }
                    }
                }
            }
        );
        this.$watch(
            () => this.currentGroupId,
            (value) => {
                var url = window.location.href.split('?')[0];
                if (value > 0) {
                    url = this.addParameterToUrl('group', this.groups[value].name, url);
                } else {
                    url = this.addParameterToUrl('group', '', url);
                }
                this.pushToUrlHistory(url);
            }
        );
    },
    mounted() {
        var group = this.getParameterFromUrl('group', 'no-group');

        if (group !== null && group !== 'null' && group !== 'no-group') {
            this.currentEnteredGroup = group;
        }

        $eventbus.on('ctrl-s-combo', (e) => {
            this.save();
        });
    }
};
</script>