<template>
    <div>
        <collapsible ref="bread_settings" :title="__('voyager::generic.'+(isNew ? 'add' : 'edit')+'_type', { type: __('voyager::generic.bread')})" icon="bread" :icon-size="8">
            <template #actions>
                <div class="flex items-center space-x-2">
                    <button class="button" @click.stop="toggleFocusMode">
                        <icon icon="arrows-expand" :size="4" />
                        <span>{{ __('voyager::generic.focus') }}</span>
                    </button>
                    <button class="button" @click="loadProperties" :disabled="!bread.model">
                        <icon icon="refresh" :size="4" :class="loadingProps ? 'animate-spin-reverse' : ''" />
                        <span>{{ __('voyager::builder.reload_properties') }}</span>
                    </button>
                    <locale-picker :small="false" />
                </div>
            </template>
            <div>
                <alert color="yellow" v-if="!propsLoaded && !loadingProps" class="mx-4">
                    <template #title>
                        <span>{{ __('voyager::generic.heads_up') }}</span>
                    </template>
                    {{ __('voyager::builder.new_breads_prop_warning') }}
                </alert>
                <div class="flex mb-4">
                    <div class="w-full m-1">
                        <label class="label" for="slug">{{ __('voyager::generic.slug') }}</label>
                        <language-input
                            class="input w-full"
                            id="slug"
                            type="text" :placeholder="__('voyager::generic.slug')"
                            v-model="bread.slug" />
                    </div>
                </div>
                
                <div class="flex-none md:flex mb-4">
                    <div class="w-full md:w-5/12 m-1">
                        <label class="label" for="name-singular">{{ __('voyager::builder.name_singular') }}</label>
                        <language-input
                            class="input w-full"
                            id="name-singular"
                            type="text" :placeholder="__('voyager::builder.name_singular')"
                            v-model="bread.name_singular" />
                    </div>
                    <div class="w-full md:w-5/12 m-1">
                        <label class="label" for="name-plural">{{ __('voyager::builder.name_plural') }}</label>
                        <language-input
                            class="input w-full"
                            id="name-plural"
                            type="text" :placeholder="__('voyager::builder.name_plural')"
                            :model-value="bread.name_plural"
                            @update:model-value="bread.name_plural = $event; setSlug($event)" />
                    </div>
                    <div class="w-full md:w-2/12 m-1">
                        <label class="label" for="icon">{{ __('voyager::generic.icon') }}</label>
                        <modal ref="icon_modal" :title="__('voyager::generic.select_icon')">
                            <icon-picker @select="bread.icon = $event; $refs.icon_modal.close()" />
                            <template #opener>
                                <div class="w-full">
                                    <button class="button">
                                        <icon class="my-1 content-center" :icon="bread.icon" :key="bread.icon" />
                                    </button>
                                </div>
                            </template>
                        </modal>
                    </div>
                </div>
                <div class="flex-none md:flex mb-4">
                    <div class="w-full md:w-1/5 m-1">
                        <label class="label" for="model">{{ __('voyager::builder.model') }}</label>
                        <input
                            class="input w-full"
                            id="model"
                            type="text" :placeholder="__('voyager::builder.model')"
                            v-model="bread.model">
                    </div>
                    <div class="w-full md:w-1/5 m-1">
                        <label class="label" for="controller">{{ __('voyager::builder.controller') }}</label>
                        <input
                            class="input w-full"
                            id="controller"
                            type="text" :placeholder="__('voyager::builder.controller')"
                            v-model="bread.controller">
                    </div>
                    <div class="w-full md:w-1/5 m-1">
                        <label class="label" for="policy">{{ __('voyager::builder.policy') }}</label>
                        <input
                            class="input w-full"
                            id="policy"
                            type="text" :placeholder="__('voyager::builder.policy')"
                            v-model="bread.policy">
                    </div>
                    <div class="w-full md:w-1/5 m-1">
                        <label class="label inline-flex" for="global_search">
                            <span class="mx-2">{{ __('voyager::builder.global_search_display_field') }}</span>
                            <icon icon="question-mark-circle" v-tooltip="__('voyager::builder.global_search_display_field_hint')" />
                        </label>
                        <select class="input w-full" v-model="bread.global_search_field">
                            <option :value="null">{{ __('voyager::generic.none') }}</option>
                            <option v-for="column in columns" :key="column">{{ column }}</option>
                        </select>
                    </div>
                    <div class="w-full md:w-1/5 m-1">
                        <label class="label inline-flex" for="order_field">
                            <span class="mx-2">{{ __('voyager::builder.order_field') }}</span>
                            <icon icon="question-mark-circle" v-tooltip="__('voyager::builder.order_field_hint')" />
                        </label>
                        <select class="input w-full" v-model="bread.order_field">
                            <option :value="null">{{ __('voyager::generic.none') }}</option>
                            <option v-for="column in columns" :key="column">{{ column }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="inline-flex space-x-1">
                <button class="button blue space-x-0" @click="save" :disabled="savingBread || backingUp">
                    <icon icon="refresh" class="animate-spin-reverse" :size="savingBread ? 4 : 0" :transition-size="4" />
                    <span>{{ __('voyager::generic.save') }}</span>
                </button>

                <button class="button space-x-0" @click="backupBread" :disabled="savingBread || backingUp">
                    <icon icon="refresh" class="animate-spin-reverse" :size="backingUp ? 4 : 0" :transition-size="4" />
                    <span>{{ __('voyager::generic.backup') }}</span>
                </button>
            </div>
        </collapsible>

        <card dont-show-header>
            <!-- Toolbar -->
            <div class="w-full mb-5 flex space-x-1">
                <select class="input small self-center" v-model="currentLayoutName" :disabled="bread.layouts.length == 0">
                    <option :value="null" v-if="bread.layouts.length == 0">
                        {{ __('voyager::builder.create_layout_first') }}
                    </option>
                    <optgroup label="Views" v-if="views.length > 0">
                        <option v-for="view in views" :key="'view-' + view.name">{{ view.name }}</option>
                    </optgroup>
                    <optgroup label="Lists" v-if="lists.length > 0">
                        <option v-for="list in lists" :key="'list-' + list.name">{{ list.name }}</option>
                    </optgroup>
                </select>
                <dropdown class="self-center" placement="bottom">
                    <div>
                        <div class="grid grid-cols-2">
                            <a v-for="formfield in filteredFormfields"
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
                            class="w-full italic link text-center">
                            {{ __('voyager::builder.formfields_more') }}
                        </a>
                    </div>
                    <template #opener>
                        <button class="button small"
                                :disabled="bread.layouts.length == 0">
                            <icon icon="plus" />
                            <span>
                                {{ __('voyager::builder.add_formfield') }}
                            </span>
                        </button>
                    </template>
                </dropdown>
                <dropdown class="self-center" placement="bottom">
                    <div>
                        <a href="#" @click.prevent="addLayout(false)" class="link">
                            {{ __('voyager::builder.list') }}
                        </a>
                        <a href="#" @click.prevent="addLayout(true)" class="link">
                            {{ __('voyager::builder.view') }}
                        </a>
                    </div>
                    <template #opener>
                        <button class="button small">
                            <icon icon="plus" />
                            <span>
                                {{ __('voyager::builder.add_layout') }}
                            </span>
                        </button>
                    </template>
                </dropdown>
                <dropdown class="self-center" placement="bottom">
                    <div>
                        <a href="#" @click.prevent="renameLayout" class="link">
                            {{ __('voyager::builder.rename_layout') }}
                        </a>
                        <a href="#" @click.prevent="deleteLayout" class="link">
                            {{ __('voyager::builder.delete_layout') }}
                        </a>
                        <a href="#" @click.prevent="cloneLayout" class="link">
                            {{ __('voyager::builder.clone_layout') }}
                        </a>
                    </div>
                    <template #opener>
                        <button class="button small">
                            <icon icon="fire" />
                            <span>
                                {{ __('voyager::generic.actions') }}
                            </span>
                        </button>
                    </template>
                </dropdown>
                <slide-in v-if="currentLayout" :title="__('voyager::generic.options')">
                    <template #actions>
                        <locale-picker />
                    </template>
                    <div>
                        <div v-if="currentLayout.type == 'list'">
                            <label class="label mt-4">{{ __('voyager::builder.show_soft_deleted') }}</label>
                            <input type="checkbox" class="input" v-model="currentLayout.options.soft_deletes">
                        </div>

                        <label class="label" for="scope">{{ __('voyager::builder.scope') }}</label>
                        <select class="input w-full" v-model="currentLayout.options.scope">
                            <option :value="null">{{ __('voyager::generic.none') }}</option>
                            <option v-for="(scope, i) in scopes" :key="i">{{ scope }}</option>
                        </select>
                        <div v-if="currentLayout.type == 'view'">
                            <label class="label" for="validate_locales">{{ __('voyager::builder.validate_locales') }}</label>
                            <select class="input w-full" v-model="currentLayout.options.validate_locales">
                                <option value="all">{{ __('voyager::builder.validate_all_locales') }}</option>
                                <option value="current">{{ __('voyager::builder.validate_current_locale') }}</option>
                            </select>
                        </div>
                    </div>
                    <template #opener>
                        <button class="button small">
                            <icon icon="cog" />
                            <span>{{ __('voyager::generic.options') }}</span>
                        </button>
                    </template>
                </slide-in>
            </div>

            <card class="text-center text-xl py-4" v-if="!currentLayout" dont-show-header>
                {{ __('voyager::builder.create_select_layout') }}
            </card>
            <card class="text-center text-xl py-4" v-else-if="currentLayout && currentLayout.formfields.length == 0" dont-show-header>
                {{ __('voyager::builder.add_formfield_to_layout') }}
            </card>
            <component
                v-else-if="currentLayout"
                :is="'bread-builder-' + currentLayout.type"
                :computed="computed"
                :columns="columns"
                :relationships="relationships"
                v-model:formfields="currentLayout.formfields"
                v-model:options="currentLayout.options"
                v-on:delete="deleteFormfield($event)" />
        </card>

        <collapsible :title="__('voyager::builder.layout_mapping')" ref="layout_mapping">
            <div class="flex">
                <div class="w-1/4">
                    <h6>{{ __('voyager::generic.browse') }}</h6>
                    <select class="input w-full mt-2" v-model="bread.layout_map.browse" multiple>
                        <option v-for="(list, i) in lists" :key="'browse-layout'+i">
                            {{ list.name }}
                        </option>
                    </select>
                </div>
                <div class="w-1/4 ml-2">
                    <h6>{{ __('voyager::generic.read') }}</h6>
                    <select class="input w-full mt-2" v-model="bread.layout_map.read" multiple>
                        <option v-for="(view, i) in views" :key="'read-layout'+i">
                            {{ view.name }}
                        </option>
                    </select>
                </div>
                <div class="w-1/4 ml-2">
                    <h6>{{ __('voyager::generic.edit') }}</h6>
                    <select class="input w-full mt-2" v-model="bread.layout_map.edit" multiple>
                        <option v-for="(view, i) in views" :key="'edit-layout'+i">
                            {{ view.name }}
                        </option>
                    </select>
                </div>
                <div class="w-1/4 ml-2">
                    <h6>{{ __('voyager::generic.add') }}</h6>
                    <select class="input w-full mt-2" v-model="bread.layout_map.add" multiple>
                        <option v-for="(view, i) in views" :key="'add-layout'+i">
                            {{ view.name }}
                        </option>
                    </select>
                </div>
            </div>
        </collapsible>

        <collapsible ref="bread_json" v-if="jsonOutput" :title="__('voyager::generic.json_output')" closed>
            <json-editor v-model="bread" />
        </collapsible>
    </div>
</template>

<script>
import axios from 'axios';

import BreadBuilderList from './List';
import BreadBuilderView from './View';

export default {
    props: ['data', 'isNew'],
    components: {
        'bread-builder-list': BreadBuilderList,
        'bread-builder-view': BreadBuilderView
    },
    data() {
        return {
            bread: this.data,
            computed: [],
            columns: [],
            scopes: [],
            relationships: [],
            softdeletes: false,
            loadingProps: false,
            savingBread: false,
            backingUp: false,
            currentLayoutName: null,
            focusMode: false,
            propsLoaded: false,
        };
    },
    methods: {
        save(e = null) {
            if (typeof e === 'object' && e instanceof KeyboardEvent) {
                if (e.ctrlKey && e.key === 's') {
                    e.preventDefault();
                } else {
                    return;
                }
            }

            if (this.validateLayouts()) {
                new this.$notification(this.nl2br(this.__('voyager::builder.layout_field_warning')))
                        .confirm()
                        .color('red')
                        .timeout()
                        .show()
                        .then((response) => {
                    if (response) {
                        this.storeBread();
                    }
                });
            } else if (this.validateLayoutMapping()) {
                new this.$notification(this.nl2br(this.__('voyager::builder.layout_mapping_warning')))
                        .confirm()
                        .color('red')
                        .timeout()
                        .show()
                        .then((response) => {
                    if (response) {
                        this.storeBread();
                    }
                });
            } else {
                this.storeBread();
            }
        },
        storeBread() {
            this.savingBread = true;

            axios.put(this.route('voyager.bread.update', this.bread.table), {
                bread: this.bread
            })
            .then(() => {
                new this.$notification(this.__('voyager::builder.bread_saved_successfully')).color('green').timeout().show();
            })
            .catch((response) => {})
            .then(() => {
                this.savingBread = false;
            });
        },
        backupBread() {
            this.backingUp = true;

            axios.post(this.route('voyager.bread.backup-bread'), {
                table: this.bread.table
            })
            .then((response) => {
                new this.$notification(this.__('voyager::builder.bread_backed_up', { name: response.data })).timeout().show();
            })
            .catch((response) => {})
            .then(() => {
                this.backingUp = false;
            });
        },
        loadProperties() {
            if (this.loadingProps) {
                return;
            }

            this.loadingProps = true;

            axios.post(this.route('voyager.bread.get-properties'), {
                model: this.bread.model,
                resolve_relationships: true,
            })
            .then((response) => {
                Object.keys(response.data).map((key) => {
                    this[key] = response.data[key];
                });
                this.propsLoaded = true;
            })
            .catch((response) => {})
            .then(() => {
                this.loadingProps = false;
            });
        },
        addLayout(view) {
            new this
            .$notification(this.__('voyager::builder.enter_name'))
            .prompt()
            .timeout()
            .show()
            .then((value) => {
                if (value && value !== '') {
                    var filtered = this.bread.layouts.where('name', value);

                    if (filtered.length > 0) {
                        new this.$notification(this.__('voyager::builder.name_already_exists')).color('red').timeout().show();

                        return;
                    }

                    var view_options = {
                        scope: null,
                        validate_locales: 'current',
                    };
                    var list_options = {
                        default_order_column: {
                            column: null,
                            type: null,
                        },
                        soft_deletes: true,
                        scope: null,
                        filters: [],
                    };

                    this.bread.layouts.push({
                        name: value,
                        type: (view ? 'view' : 'list'),
                        options: (view ? view_options : list_options),
                        formfields: []
                    });

                    this.currentLayoutName = value;
                }
            });
        },
        renameLayout() {
            new this
            .$notification(this.__('voyager::builder.enter_new_name'))
            .timeout()
            .prompt(this.currentLayoutName)
            .show()
            .then((value) => {
                if (value && value !== '') {
                    if (value == this.currentLayoutName) {
                        return;
                    }
                    var filtered = this.bread.layouts.where('name', value);

                    if (filtered.length > 0) {
                        new this.$notification(this.__('voyager::builder.name_already_exists')).color('red').timeout().show();

                        return;
                    }

                    this.currentLayout.name = value;
                    this.currentLayoutName = value;
                }
            });
        },
        deleteLayout() {
            new this
            .$notification(this.__('voyager::builder.delete_layout_confirm'))
            .color('yellow')
            .timeout()
            .confirm()
            .show()
            .then((result) => {
                if (result) {
                    var name = this.currentLayoutName;
                    this.currentLayoutName = null;
                    this.bread.layouts = this.bread.layouts.whereNot('name', name);

                    if (this.bread.layouts.length > 0) {
                        this.currentLayoutName = this.bread.layouts[0].name;
                    }
                }
            });
        },
        cloneLayout() {
            var layout = JSON.parse(JSON.stringify(this.currentLayout));
            layout.name = layout.name + ' 2';
            this.bread.layouts.push(layout);
            this.currentLayoutName = layout.name;
        },
        addFormfield(formfield) {
            var options = {
                width: 'w-3/6',
            };

            var formfield = {
                type: formfield.type,
                column: {
                    column: null,
                    type: null,
                },
                translatable: false,
                options: this.currentLayout.type == 'list' ? {} : options,
                validation: [],
            };

            if (this.currentLayout.type == 'list') {
                formfield.title = null;
            }

            this.currentLayout.formfields.push(formfield);
        },
        deleteFormfield(key) {
            new this
            .$notification(this.__('voyager::builder.delete_formfield_confirm'))
            .color('yellow')
            .timeout()
            .confirm()
            .show()
            .then((result) => {
                if (result) {
                    this.currentLayout.formfields.splice(key, 1);
                }
            });
        },
        setSlug(value) {
            var l = this.$store.locale;
            this.bread.slug = this.get_translatable_object(this.bread.slug);
            this.bread.slug[l] = this.slugify(value[l], { strict: true, lower: true });
        },
        toggleFocusMode() {
            this.focusMode = !this.focusMode;

            if (this.focusMode) {
                this.$refs.bread_settings.close();
                this.$refs.layout_mapping.close();
                if (this.jsonOutput) {
                    this.$refs.bread_json.close();
                }
                this.closeSidebar();
            } else {
                this.$refs.bread_settings.open();
                this.$refs.layout_mapping.open();
                this.openSidebar();
            }
        },
        validateLayouts() {
            var failed = false;

            this.bread.layouts.forEach((layout) => {
                layout.formfields.forEach((formfield) => {
                    if (formfield.column == '' || formfield.column === null || (this.isObject(formfield.column) && (formfield.column.column == '' || formfield.column.column === null))) {
                        failed = true;
                    }
                });
            });

            return failed;
        },
        validateLayoutMapping() {
            var failed = false;

            Object.keys(this.bread.layout_map).forEach((action) => {
                if (this.bread.layout_map[action].length == 0) {
                    failed = true;
                }
            });

            return failed;
        },
    },
    computed: {
        views() {
            return this.bread.layouts.where('type', 'view');
        },
        lists() {
            return this.bread.layouts.where('type', 'list');
        },
        filteredFormfields() {
            return this.$store.formfields.filter((formfield) => {
                if (this.currentLayout && this.currentLayout.type == 'list') {
                    return formfield.in_lists;
                }
                return formfield.in_views;
            });
        },
        currentLayout() {
            return this.bread.layouts.filter((layout, key) => {
                if (layout.name == this.currentLayoutName) {
                    this.pushToUrlHistory(this.addParameterToUrl('layout', key));
                    return true;
                }
                return false;
            })[0];
        },
        jsonOutput() {
            return this.$store.jsonOutput;
        }
    },
    mounted() {
        // Load model-properties (only when we already know the model-name)
        if (this.bread.model) {
            this.loadProperties();
        }

        document.addEventListener('keydown', this.save);
    },
    unmounted() {
        document.removeEventListener('keydown', this.save);
    },
    created() {
        var layout = parseInt(this.getParameterFromUrl('layout', 0));
        if (this.bread.layouts.length >= (layout+1)) {
            this.currentLayoutName = this.bread.layouts[layout].name;
        }
    },
};
</script>