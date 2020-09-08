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
            <div v-html="widget.view"></div>
        </card>

        <card title="Welcome to Voyager II" icon="helm" :icon-size="8" class="w-full" v-if="widgets.length == 0">
            <div>Voyager 2 has been re-built using Laravel and VueJS. There are a lot of other cool things about version 2</div>
        </card>
    </div>
</template>

<script>
export default {
    props: ['widgets'],
}
</script>