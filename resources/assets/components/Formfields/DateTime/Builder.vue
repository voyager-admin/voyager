<template>
    <div v-if="action == 'list-options' || action == 'view-options'">
        <div class="input-group mt-2">
            <label class="label">Type</label>
            <select class="input w-full" :value="options.type" @change="options.displayFormat = FORMATS[$event.target.value]; options.type = $event.target.value">
                <option v-for="(format, type) in FORMATS" :key="type" :value="type">
                    {{ titleCase(type) }}
                </option>
            </select>
        </div>
        <div class="input-group mt-2">
            <label class="label">Display format</label>
            <input type="text" class="input w-full" v-model="options.displayFormat">
        </div>

        <template v-if="action == 'view-options'">
            <div class="input-group mt-2">
                <label class="label">Past (in days)</label>
                <input type="number" class="input w-full" v-model.number="options.past" min="0">
            </div>
            <div class="input-group mt-2">
                <label class="label">Future (in days)</label>
                <input type="number" class="input w-full" v-model.number="options.future" min="0">
            </div>
            <div class="input-group mt-2">
                <label class="label">Inline</label>
                <input type="checkbox" class="input" v-model="options.inline">
            </div>
            <div class="input-group mt-2">
                <label class="label">Inline</label>
                <input type="checkbox" class="input" v-model="options.inline">
            </div>
            <div class="input-group mt-2">
                <label class="label">Close on select</label>
                <input type="checkbox" class="input" v-model="options.closeOnSelect">
            </div>
            <div class="input-group mt-2">
                <label class="label">Sunday first</label>
                <input type="checkbox" class="input" v-model="options.sundayFirst">
            </div>
        </template>
    </div>
    <div v-else-if="action == 'view'">
        
    </div>
</template>

<script>
import formfieldBuilder from '../../../js/mixins/formfield-builder';
import { FORMATS } from '../../UI/DateTime.vue';

export default {
    mixins: [formfieldBuilder],
    computed: {
        defaultViewOptions() {
            return {
                displayFormat: 'DD-MM-YYYY',
                type: 'date',
                past: 720,
                future: 720,
                inline: false,
                closeOnSelect: false,
                sundayFirst: false,
                placement: 'bottom-start'
            };
        },
        defaultListOptions() {
            return {
                displayFormat: 'DD-MM-YYYY',
                type: 'date',
            };
        },
    },
    data() {
        return {
            FORMATS
        };
    }
}
</script>