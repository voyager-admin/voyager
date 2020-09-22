<template>
    <div class="flex">
        <card
            v-for="(widget, i) in widgets"
            :key="'widget-'+i"
            :dont-show-header="((widget.title || widget.icon) ? false : true)"
            :title="widget.title"
            :icon="widget.icon"
            :class="widget.width"
        >
            <component :is="widget.component" v-bind="widget.parameters"></component>
        </card>
        <card :title="__('voyager::generic.welcome_to_voyager')" icon="helm" :icon-size="8" class="w-full" v-if="widgets.length == 0">
            <div>{{ __('voyager::generic.welcome_text') }}</div>
        </card>
    </div>
</template>

<script>
export default {
    props: ['widgets'],
}
</script>