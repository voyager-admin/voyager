<template>
    <div>
        <card :title="__('voyager::settings.settings')" icon="cog">
            <div slot="actions">
                <div class="flex items-center">
                    <button class="button accent" @click="saveSettings">
                        <icon icon="refresh" class="mr-0 md:mr-1 rotating-ccw" :size="4" v-if="savingSettings" />
                        <span>{{ __('voyager::generic.save') }}</span>
                    </button>
                    <dropdown ref="formfield_dd">
                        <div>
                            <a v-for="formfield in filterFormfields"
                                :key="'formfield-'+formfield.type"
                                href="#"
                                @click.prevent="addFormfield(formfield); $refs.formfield_dd.close()"
                                class="link">
                                {{ formfield.name }}
                            </a>
                            <div class="divider"></div>
                            <a
                                :href="route('voyager.plugins.index')+'/?type=formfield'"
                                target="_blank"
                                class="italic link">
                                {{ __('voyager::builder.formfields_more') }}
                            </a>
                        </div>
                        <div slot="opener">
                            <button class="button green small">
                                <icon icon="plus" />
                                <span>
                                    {{ __('voyager::builder.add_formfield') }}
                                </span>
                            </button>
                        </div>
                    </dropdown>
                    <locale-picker :small="false" />
                </div>
            </div>
            <tabs v-on:select="currentGroupId = $event" :tabs="groups" ref="tabs">
                <div v-for="(group, i) in groups" :key="'group-'+i" :slot="group.name">
                    <sort-container v-model="groupedSettings" :useDragHandle="true">
                        <sort-element v-for="(setting, i) in settingsByGroup(group.name)" :key="'settings-'+i" :index="i">
                            <card :title="setting.name">
                                <div slot="title" v-if="editMode" class="flex space-x-1">
                                    <input
                                        type="text"
                                        class="input small w-full md:w-1/3"
                                        v-model="setting.name"
                                        v-on:input="setting.key = slugify($event.target.value, { lower: true, strict: true })"
                                        :placeholder="__('voyager::generic.name')"
                                    >
                                    <input type="text" class="input small hidden md:block md:w-1/4" v-bind:value="setting.key" disabled :placeholder="__('voyager::generic.key')">
                                    <input type="text" class="input small w-full md:w-1/4" v-bind:value="setting.group" v-on:input="setting.group = slugify($event.target.value, {strict:true,lower:true}); currentEnteredGroup = $event.target.value" :placeholder="__('voyager::generic.group')">
                                    <input type="text" class="input small w-full md:w-1/4" v-model="setting.info" :placeholder="__('voyager::generic.info')">
                                </div>
                                <div slot="title" v-else class="flex items-end">
                                    <h4>{{ setting.name }}</h4>
                                    <p class="mx-4">{{ setting.key }}</p>
                                </div>
                                <div slot="actions" v-if="editMode">
                                    <div class="flex items-center mt-1 md:mt-0">
                                        <button class="button green" v-sort-handle>
                                            <icon icon="selector" :size="4"></icon>
                                        </button>
                                        <button class="button blue" @click="optionsId = i">
                                            <icon icon="cog" :size="4"></icon>
                                            <span>{{ __('voyager::generic.options') }}</span>
                                        </button>
                                        <button class="button red" @click="deleteSetting(setting)">
                                            <icon icon="trash" :size="4"></icon>
                                            <span>{{ __('voyager::generic.delete') }}</span>
                                        </button>
                                        <slide-in :opened="optionsId == i" v-on:closed="optionsId = null" width="w-1/3" class="text-left" :title="__('voyager::generic.options')">
                                            <locale-picker v-if="$language.localePicker" slot="actions" />
                                            <div v-if="setting.canBeTranslated">
                                                <label class="label mt-4">Translatable</label>
                                                <input type="checkbox" class="input" v-model="setting.translatable">
                                            </div>

                                            <component
                                                :is="'formfield-'+kebab_case(setting.type)+'-builder'"
                                                v-bind:options="setting.options"
                                                :column="''"
                                                show="view-options" />
                                            <bread-builder-validation v-model="setting.validation" />
                                        </slide-in>
                                    </div>
                                </div>
                                <div>
                                    <alert v-if="getErrors(setting).length > 0" color="red" class="my-2" :closebutton="false">
                                        <ul class="list-disc ml-4">
                                            <li v-for="(error, i) in getErrors(setting)" :key="'error-'+i">
                                                {{ error }}
                                            </li>
                                        </ul>
                                    </alert>
                                    <component
                                        :is="'formfield-'+kebab_case(setting.type)+'-edit-add'"
                                        v-bind:value="data(setting, null)"
                                        v-on:input="data(setting, $event)"
                                        :options="setting.options"
                                        :show="'edit'" />
                                </div>
                            </card>
                        </sort-element>
                    </sort-container>
                    <div v-if="groupedSettings.length == 0" class="w-full text-center">
                        <h4>{{ __('voyager::settings.no_settings_in_group') }}</h4>
                    </div>
                </div>
            </tabs>
        </card>
        <collapsible v-if="$store.json_output" :title="__('voyager::builder.json_output')" :opened="false">
            <textarea class="input w-full" rows="10" v-model="jsonSettings"></textarea>
        </collapsible>
    </div>
</template>

<script>
export default {
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
    data: function () {
        return {
            settings: this.input,
            savingSettings: false,
            currentGroupId: 0,
            optionsId: null,
            currentEnteredGroup: null,
            errors: [],
        };
    },
    methods: {
        settingsByGroup: function (group) {
            return this.settings.filter(function (setting) {
                if (group == 'no-group') {
                    return setting.group == null;
                }
                return setting.group == group;
            });
        },
        saveSettings: function () {
            var vm = this;
            vm.savingSettings = true;
            vm.errors = [];

            axios.post(vm.route('voyager.settings.store'), {
                settings: vm.settings
            })
            .then(function (response) {
                new vm.$notification(vm.__('voyager::settings.settings_saved')).color('green').timeout().show();
            })
            .catch(function (response) {
                if (response.response.status == 422) {
                    // Validation failed
                    vm.errors = response.response.data;
                } else {
                    new vm.$notification(response.response.data.message).color('red').show();
                }
            })
            .then(function () {
                vm.savingSettings = false;
            });
        },
        addFormfield: function (formfield) {
            var group = this.groups[this.currentGroupId].name
            this.settings.push({
                type: formfield.type,
                group: (group == 'no-group' ? null : group),
                key: '',
                name: '',
                value: null,
                info: '',
                translatable: false,
                canBeTranslated: formfield.canBeTranslated,
                options: formfield.viewOptions,
                validation: [],
            });
        },
        deleteSetting: function (setting) {
            var vm = this;
            new vm
            .$notification(vm.trans_choice('voyager::bread.delete_type_confirm', 1, { type: vm.__('voyager::settings.setting') }))
            .color('red')
            .timeout()
            .confirm()
            .show()
            .then(function (response) {
                if (response) {
                    vm.settings.splice(vm.settings.indexOf(setting), 1);

                    if (!vm.groups[vm.currentGroupId]) {
                        vm.currentGroupId = 0;
                        vm.$refs.tabs.openByIndex(0);
                    }
                }
            });
        },
        data: function (setting, value = null) {
            if (setting.translatable || false && setting.value && this.isString(setting.value)) {
                Vue.set(setting, 'value', this.get_translatable_object(setting.value));
            }
            if (value !== null) {
                if (setting.translatable || false) {
                    Vue.set(setting.value, this.$language.locale, value);
                } else {
                    Vue.set(setting, 'value', value);
                }
            }
            if (setting.translatable || false) {
                return this.translate(this.get_translatable_object(setting.value));
            }
            return setting.value;
        },
        getErrors: function (setting) {
            var key = setting.key;
            if (setting.group !== null) {
                key = setting.group+'.'+setting.key;
            }

            return this.errors[key] || [];
        },
    },
    computed: {
        filterFormfields: function () {
            return this.$store.formfields.where('asSetting', true);
        },
        groups: function () {
            var groups = ['no-group'];
            this.settings.forEach(function (setting) {
                if (groups.indexOf(setting.group) == -1 && setting.group !== null) {
                    groups.push(setting.group);
                }
            });

            groups = groups.map(function (group) {
                return {
                    name: group,
                    title: (group == 'no-group' ? 'No group' : group),
                };
            });

            return groups;
        },
        groupedSettings: {
            get: function () {
                return this.settingsByGroup(this.groups[this.currentGroupId].name);
            },
            set: function (settings) {
                var vm = this;
                var current_group = vm.groups[vm.currentGroupId].name;
                vm.settings = vm.settings.filter(function (setting) {
                    if (current_group == 'no-group') {
                        return setting.group !== null;
                    }
                    return setting.group !== current_group;
                });
                vm.settings = vm.settings.concat(settings);
            }
        },
        jsonSettings: {
            get: function () {
                return JSON.stringify(this.settings, null, 2);
            },
            set: function (value) {
                
            }
        },
    },
    watch: {
        currentEnteredGroup: function (value) {
            if (value == '') {
                this.settings = this.settings.map(function (setting) {
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
        },
        currentGroupId: function (value) {
            var url = window.location.href.split('?')[0];
            if (value > 0) {
                url = this.addParameterToUrl('group', this.groups[value].name, url);
            } else {
                url = this.addParameterToUrl('group', '', url);
            }
            this.pushToUrlHistory(url);
        }
    },
    mounted: function () {
        var vm = this;
        var group = vm.getParameterFromUrl('group', 'no-group');

        if (group !== null && group !== 'null' && group !== 'no-group') {
            vm.currentEnteredGroup = group;
        }

        document.addEventListener('keydown', function (e) {
            if (event.ctrlKey && event.key === 's') {
                e.preventDefault();
                vm.saveSettings();
            }
        });
    }
};
</script>