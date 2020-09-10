<template>
    <div>
        <card :title="__('voyager::bread.read_type', { type: translate(bread.name_singular, true) })" :icon="bread.icon">
            <template #actions>
                <div class="flex items-center">
                    <a class="button small" v-if="prevUrl !== ''" :href="prevUrl">
                        <icon icon="chevron-left"></icon>
                        <span>{{ __('voyager::generic.back') }}</span>
                    </a>
                    <locale-picker :small="false"></locale-picker>
                </div>
            </template>
            <div class="flex flex-wrap w-full">
                <div v-for="(formfield, key) in layout.formfields" :key="'formfield-'+key" class="m-0" :class="formfield.options.width">
                    <card :title="translate(formfield.options.title, true)" :title-size="5" :show-title="translate(formfield.options.label, true) !== ''">
                        <div>
                            <component
                                :is="'formfield-'+kebab_case(formfield.type)+'-read'"
                                :data="getData(formfield.column)"
                                :translatable="formfield.translatable"
                                :options="formfield.options" />
                            <p class="description" v-if="translate(formfield.options.description, true) !== ''">
                                {{ translate(formfield.options.description, true) }}
                            </p>
                        </div>
                    </card>
                </div>
            </div>
        </card>
    </div>
</template>

<script>
export default {
    props: ['bread', 'data', 'primary', 'layout', 'prevUrl'],
    methods: {
        getData: function (column) {
            return this.data[column.column];
        }
    },
};
</script>