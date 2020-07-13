<template>
    <div>
        <collapsible ref="bread_settings" :title="__('voyager::generic.'+(isNew ? 'add' : 'edit')+'_type', { type: __('voyager::generic.bread')})" icon="bread" :icon-size="8">
            <div slot="actions">
                <div class="flex items-center">
                    <button class="button blue" @click.stop="toggleFocusMode">
                        <icon icon="arrows-expand" :size="4" />
                        <span>{{ __('voyager::generic.focus') }}</span>
                    </button>
                    <button class="button green" @click="loadProperties">
                        <icon icon="refresh" class="rotating-ccw" :size="4" v-if="loadingProps" />
                        <span>{{ __('voyager::builder.reload_properties') }}</span>
                    </button>
                    <locale-picker :small="false" class="ltr:ml-2 rtl:mr-2" />
                </div>
            </div>
            <div>
                <alert color="yellow" v-if="!bread.model || bread.model == ''" class="mx-4">
                    <span slot="title">
                        {{ __('voyager::generic.heads_up') }}
                    </span>
                    {{ __('voyager::builder.new_breads_prop_warning') }}
                </alert>
                <div class="flex mb-4">
                    <div class="w-full m-1">
                        <label class="label" for="slug">{{ __('voyager::generic.slug') }}</label>
                        <language-input
                            class="input w-full"
                            id="slug"
                            type="text" :placeholder="__('voyager::generic.slug')"
                            v-bind:value="bread.slug"
                            v-on:input="bread.slug = $event" />
                    </div>
                </div>
                
                <div class="flex-none md:flex mb-4">
                    <div class="w-full md:w-5/12 m-1">
                        <label class="label" for="name-singular">{{ __('voyager::builder.name_singular') }}</label>
                        <language-input
                            class="input w-full"
                            id="name-singular"
                            type="text" :placeholder="__('voyager::builder.name_singular')"
                            v-bind:value="bread.name_singular"
                            v-on:input="bread.name_singular = $event" />
                    </div>
                    <div class="w-full md:w-5/12 m-1">
                        <label class="label" for="name-plural">{{ __('voyager::builder.name_plural') }}</label>
                        <language-input
                            class="input w-full"
                            id="name-plural"
                            type="text" :placeholder="__('voyager::builder.name_plural')"
                            v-bind:value="bread.name_plural"
                            v-on:input="bread.name_plural = $event; setSlug($event)" />
                    </div>
                    <div class="w-full md:w-2/12 m-1">
                        <label class="label" for="icon">{{ __('voyager::generic.icon') }}</label>
                        <modal ref="icon_modal" :title="__('voyager::generic.select_icon')">
                            <icon-picker v-on:select="$refs.icon_modal.close(); bread.icon = $event" />
                            <div slot="opener" class="w-full">
                                <button class="button green">
                                    <icon class="cursor-pointer text-white my-1 content-center" :size="6" :icon="bread.icon" />
                                </button>
                            </div>
                        </modal>
                    </div>
                </div>
                <div class="flex-none md:flex mb-4">
                    <div class="w-full md:w-1/4 m-1">
                        <label class="label" for="model">{{ __('voyager::builder.model') }}</label>
                        <input
                            class="input w-full"
                            id="model"
                            type="text" :placeholder="__('voyager::builder.model')"
                            v-model="bread.model">
                    </div>
                    <div class="w-full md:w-1/4 m-1">
                        <label class="label" for="controller">{{ __('voyager::builder.controller') }}</label>
                        <input
                            class="input w-full"
                            id="controller"
                            type="text" :placeholder="__('voyager::builder.controller')"
                            v-model="bread.controller">
                    </div>
                    <div class="w-full md:w-1/4 m-1">
                        <label class="label" for="policy">{{ __('voyager::builder.policy') }}</label>
                        <input
                            class="input w-full"
                            id="policy"
                            type="text" :placeholder="__('voyager::builder.policy')"
                            v-model="bread.policy">
                    </div>
                    <div class="w-full md:w-1/4 m-1">
                        <label class="label inline-flex" for="global_search">
                            {{ __('voyager::builder.global_search_display_field') }}
                            <icon icon="question-mark-circle" class="mx-2" v-tooltip="__('voyager::builder.global_search_display_field_hint')"></icon>
                        </label>
                        <select class="input w-full" v-model="bread.global_search_field">
                            <option :value="null">{{ __('voyager::generic.none') }}</option>
                            <option v-for="column in columns" :key="column">{{ column }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div slot="footer" class="inline-flex">
                <button class="button blue" @click="saveBread">
                    <icon icon="refresh" class="rotating-ccw" :size="4" v-if="savingBread" />
                    <span>{{ __('voyager::generic.save') }}</span>
                </button>
                <button class="button green" @click="backupBread">
                    <icon icon="refresh" class="rotating-ccw" :size="4" v-if="backingUp" />
                    <span>{{ __('voyager::generic.backup') }}</span>
                </button>
            </div>
        </collapsible>

        <card :show-header="false">
            <!-- Toolbar -->
            <div class="w-full mb-5 flex">
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
                <dropdown ref="formfield_dd" pos="right" class="self-center">
                    <div>
                        <a v-for="formfield in filteredFormfields"
                            :key="'formfield-'+formfield.type"
                            href="#"
                            @click.prevent="addFormfield(formfield); $refs.formfield_dd.close()"
                            class="link">
                            {{ formfield.name }}
                        </a>
                        <a
                            :href="route('voyager.plugins.index')+'/?type=formfield'"
                            target="_blank"
                            class="italic link">
                            {{ __('voyager::builder.formfields_more') }}
                        </a>
                    </div>
                    <div slot="opener">
                        <button class="button green small ml-2"
                                :disabled="bread.layouts.length == 0">
                            <icon icon="plus" />
                            <span>
                                {{ __('voyager::builder.add_formfield') }}
                            </span>
                        </button>
                    </div>
                </dropdown>
                <dropdown ref="layout_dd" pos="right" class="self-center">
                    <div>
                        <a href="#" @click.prevent="addLayout(false)" class="link">
                            {{ __('voyager::builder.list') }}
                        </a>
                        <a href="#" @click.prevent="addLayout(true)" class="link">
                            {{ __('voyager::builder.view') }}
                        </a>
                    </div>
                    <div slot="opener">
                        <button class="button green small">
                            <icon icon="plus" />
                            <span>
                                {{ __('voyager::builder.add_layout') }}
                            </span>
                        </button>
                    </div>
                </dropdown>
                <dropdown ref="actions_dd" pos="right" class="self-center">
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
                    <div slot="opener">
                        <button class="button blue small">
                            <icon icon="fire" />
                            <span>
                                {{ __('voyager::generic.actions') }}
                            </span>
                        </button>
                    </div>
                </dropdown>
                <button class="button blue small" @click="layoutOptionsOpen = true" :disabled="!currentLayout || currentLayout.type !== 'list'">
                    <icon icon="cog" />
                    <span>
                        {{ __('voyager::generic.options') }}
                    </span>
                </button>
                <slide-in v-if="currentLayout" :opened="layoutOptionsOpen" width="w-1/3" class="text-left" v-on:closed="layoutOptionsOpen = false" :title="__('voyager::generic.options')">
                    <locale-picker v-if="$language.localePicker" slot="actions" />
                    <div v-if="currentLayout.type == 'list'">
                        <label class="label mt-4">{{ __('voyager::builder.show_soft_deleted') }}</label>
                        <input type="checkbox" v-model="currentLayout.options.soft_deletes">

                        <label class="label" for="scope">{{ __('voyager::builder.scope') }}</label>
                        <select class="input w-full" v-model="currentLayout.options.scope">
                            <option :value="null">{{ __('voyager::generic.none') }}</option>
                            <option v-for="(scope, i) in scopes" :key="i">{{ scope }}</option>
                        </select>
                    </div>
                </slide-in>
            </div>

            <div class="card text-center text-xl" v-if="!currentLayout">
                {{ __('voyager::builder.create_select_layout') }}
            </div>
            <div class="card text-center text-xl" v-else-if="currentLayout && currentLayout.formfields.length == 0">
                {{ __('voyager::builder.add_formfield_to_layout') }}
            </div>
            <component
                v-else-if="currentLayout"
                :is="'bread-builder-' + currentLayout.type"
                :computed="computed"
                :columns="columns"
                :relationships="relationships"
                :formfields="currentLayout.formfields"
                :options="currentLayout.options"
                :options-id="openOptionsId"
                v-on:delete="deleteFormfield($event)"
                v-on:formfields="currentLayout.formfields = $event"
                v-on:options="currentLayout.options = $event"
                v-on:open-options="openOptionsId = $event" />
        </card>

        <collapsible :title="__('voyager::builder.layout_mapping')" ref="layout_mapping">
            <div class="flex">
                <div class="w-1/4">
                    <h6>{{ __('voyager::generic.browse') }}</h6>
                    <select class="input w-full mt-2" v-model="bread.use_layouts.browse">
                        <option v-for="(list, i) in lists" :key="'browse-layout'+i">
                            {{ list.name }}
                        </option>
                    </select>
                </div>
                <div class="w-1/4 ml-2">
                    <h6>{{ __('voyager::generic.read') }}</h6>
                    <select class="input w-full mt-2" v-model="bread.use_layouts.read">
                        <option v-for="(view, i) in views" :key="'read-layout'+i">
                            {{ view.name }}
                        </option>
                    </select>
                </div>
                <div class="w-1/4 ml-2">
                    <h6>{{ __('voyager::generic.edit') }}</h6>
                    <select class="input w-full mt-2" v-model="bread.use_layouts.edit">
                        <option v-for="(view, i) in views" :key="'edit-layout'+i">
                            {{ view.name }}
                        </option>
                    </select>
                </div>
                <div class="w-1/4 ml-2">
                    <h6>{{ __('voyager::generic.add') }}</h6>
                    <select class="input w-full mt-2" v-model="bread.use_layouts.add">
                        <option v-for="(view, i) in views" :key="'add-layout'+i">
                            {{ view.name }}
                        </option>
                    </select>
                </div>
            </div>
        </collapsible>

        <collapsible ref="bread_json" v-if="$store.debug" :title="__('voyager::builder.json_output')" :opened="false">
            <textarea class="input w-full" rows="10" v-model="jsonBread"></textarea>
        </collapsible>
    </div>
</template>

<script>
export default {
    props: ['data', 'isNew'],
    data: function () {
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
            openOptionsId: null,
            layoutOptionsOpen: false,
            focusMode: false,
        };
    },
    methods: {
        saveBread: function () {
            var vm = this;

            vm.savingBread = true;

            axios.put(this.route('voyager.bread.update', this.bread.table), {
                bread: vm.bread
            })
            .then(function (response) {
                new vm.$notification(vm.__('voyager::builder.bread_saved_successfully')).color('green').timeout().show();
            })
            .catch(function (errors) {
                var errors = errors.response.data;
                if (!vm.isObject(errors)) {
                    new vm.$notification(errors).color('red').timeout().show();
                } else {
                    Object.entries(errors).forEach(([key, val]) => {
                        val.forEach(function (e) {
                            new vm.$notification(e).color('red').timeout().show();
                        });
                    });
                }
            }).then(function () {
                vm.savingBread = false;
            });
        },
        backupBread: function () {
            var vm = this;
            vm.backingUp = true;
            axios.post(vm.route('voyager.bread.backup-bread'), {
                table: vm.bread.table
            })
            .then(function (response) {
                new vm.$notification(vm.__('voyager::builder.bread_backed_up', { name: response.data })).timeout().show();
            })
            .catch(function (error) {
                new vm.$notification(error.response.statusText).color('red').timeout().show();
            })
            .then(function () {
                vm.backingUp = false;
            });
        },
        loadProperties: function () {
            var vm = this;

            if (vm.loadingProps) {
                return;
            }

            vm.loadingProps = true;
            axios.post(vm.route('voyager.bread.get-properties'), {
                model: vm.bread.model,
                resolve_relationships: true,
            })
            .then(function (response) {
                Object.keys(response.data).map(function(key) {
                    Vue.set(vm, key, response.data[key]);
                });
            })
            .catch(function (error) {
                new vm.$notification(error.response.data).color('red').timeout().show();
                
            })
            .then(function () {
                vm.loadingProps = false;
            });
        },
        addLayout: function (view) {
            var vm = this;
            new vm
            .$notification(vm.__('voyager::builder.enter_name'))
            .prompt()
            .timeout()
            .show()
            .then(function (value) {
                if (value && value !== '') {
                    var filtered = vm.bread.layouts.where('name', value);

                    if (filtered.length > 0) {
                        new vm.$notification(vm.__('voyager::builder.name_already_exists')).color('red').timeout().show();

                        return;
                    }

                    var view_options = {};
                    var list_options = {
                        default_order_column: {
                            column: null,
                            type: null,
                        },
                        soft_deletes: true,
                        scope: null,
                    };

                    vm.bread.layouts.push({
                        name: value,
                        type: (view ? 'view' : 'list'),
                        options: (view ? view_options : list_options),
                        formfields: []
                    });

                    vm.currentLayoutName = value;
                }
            });
        },
        renameLayout: function () {
            var vm = this;
            new vm
            .$notification(vm.__('voyager::builder.enter_new_name'))
            .timeout()
            .prompt(vm.currentLayoutName)
            .show()
            .then(function (value) {
                if (value && value !== '') {
                    if (value == vm.currentLayoutName) {
                        return;
                    }
                    var filtered = vm.bread.layouts.where('name', value);

                    if (filtered.length > 0) {
                        new vm.$notification(vm.__('voyager::builder.name_already_exists')).color('red').timeout().show();

                        return;
                    }

                    vm.currentLayout.name = value;
                    vm.currentLayoutName = value;
                }
            });
        },
        deleteLayout: function () {
            var vm = this;
            new vm
            .$notification(vm.__('voyager::builder.delete_layout_confirm'))
            .color('yellow')
            .timeout()
            .confirm()
            .show()
            .then(function (result) {
                if (result) {
                    var name = vm.currentLayoutName;
                    vm.currentLayoutName = null;
                    vm.bread.layouts = vm.bread.layouts.whereNot('name', name);

                    if (vm.bread.layouts.length > 0) {
                        vm.currentLayoutName = vm.bread.layouts[0].name;
                    }
                }
            });
        },
        cloneLayout: function () {
            var layout = JSON.parse(JSON.stringify(this.currentLayout));
            layout.name = layout.name + ' 2';
            this.bread.layouts.push(layout);
            this.currentLayoutName = layout.name;

            this.$refs.actions_dd.close();
        },
        addFormfield: function (formfield) {
            // Merge any global options into the below options
            var listOptions = formfield.listOptions;
            var viewOptions = formfield.viewOptions;

            viewOptions.width = 'w-3/6';

            var formfield = {
                type: formfield.type,
                column: {
                    column: null,
                    type: null,
                },
                translatable: false,
                canBeTranslated: formfield.canBeTranslated,
                options: JSON.parse(JSON.stringify(this.currentLayout.type == 'list' ? listOptions : viewOptions)),
                validation: [],
            };

            if (this.currentLayout.type == 'list') {
                formfield.title = null;
            }

            this.currentLayout.formfields.push(formfield);
        },
        deleteFormfield: function (key) {
            var vm = this;
            new vm
            .$notification(vm.__('voyager::builder.delete_formfield_confirm'))
            .color('yellow')
            .timeout()
            .confirm()
            .show()
            .then(function (result) {
                if (result) {
                    vm.currentLayout.formfields.splice(key, 1);
                }
            });
        },
        setSlug: function (value) {
            var l = this.$language.locale;
            this.bread.slug = this.get_translatable_object(this.bread.slug);
            this.bread.slug[l] = this.slugify(value[l], { strict: true, lower: true });
        },
        toggleFocusMode: function () {
            this.focusMode = !this.focusMode;

            if (this.focusMode) {
                this.$refs.bread_settings.close();
                this.$refs.layout_mapping.close();
                if (this.$store.debug) {
                    this.$refs.bread_json.close();
                }
                this.$store.closeSidebar();
            } else {
                this.$refs.bread_settings.open();
                this.$refs.layout_mapping.open();
                this.$store.openSidebar();
            }
        }
    },
    computed: {
        views: function () {
            return this.bread.layouts.where('type', 'view');
        },
        lists: function () {
            return this.bread.layouts.where('type', 'list');
        },
        filteredFormfields: function () {
            var vm = this;
            return vm.$store.formfields.filter(function (formfield) {
                if (vm.currentLayout && vm.currentLayout.type == 'list') {
                    return formfield.inList;
                }
                return formfield.inView;
            });
        },
        currentLayout: function () {
            var vm = this;
            return this.bread.layouts.filter(function (layout, key) {
                if (layout.name == vm.currentLayoutName) {
                    vm.pushToUrlHistory(vm.addParameterToUrl('layout', key));
                    return true;
                }
                return false;
            })[0];
        },
        jsonBread: {
            get: function () {
                return JSON.stringify(this.bread, null, 2);
            },
            set: function (value) {
                
            }
        },
    },
    mounted: function () {
        var vm = this;
        Vue.prototype.$language.localePicker = true;

        // Load model-properties (only when we already know the model-name)
        if (vm.bread.model) {
            vm.loadProperties();
        }

        document.addEventListener('keydown', function (e) {
            if (event.ctrlKey && event.key === 's') {
                e.preventDefault();
                vm.saveBread();
            }
        });
    },
    created: function () {
        var layout = parseInt(this.getParameterFromUrl('layout', 0));
        if (this.bread.layouts.length >= (layout+1)) {
            this.currentLayoutName = this.bread.layouts[layout].name;
        }
    },
};
</script>