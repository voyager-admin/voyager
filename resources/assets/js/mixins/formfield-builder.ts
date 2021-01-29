import { defineComponent } from 'vue';

export default defineComponent({
    emits: ['update:options'],
    props: {
        action: {
            type: String,
            required: true,
            validator: function (value: any) {
                return ['view', 'view-options', 'list-options'].indexOf(value) >= 0;
            }
        },
        options: {
            type: Object,
            required: true,
        },
        column: {
            type: Object,
            required: true,
        },
        columns: {
            type: Array,
            default: (): Array<any> => [],
        },
        relationships: {
            type: Array,
            default: (): Array<any> => [],
        }
    },
    mounted: function () {
        var options = Object.keys(this.options).length;
        // @ts-ignore
        if (this.defaultListOptions && this.action == 'list-options' && options == 0) {
            // @ts-ignore
            this.$emit('update:options', JSON.parse(JSON.stringify(this.defaultListOptions)));
        // @ts-ignore
        } else if (this.defaultViewOptions && this.action == 'view-options' && options == 1) {
            if (this.options.hasOwnProperty('width')) {
                // @ts-ignore
                var new_options = JSON.parse(JSON.stringify(this.defaultViewOptions));
                new_options.width = this.options.width;
                this.$emit('update:options', new_options);
            }
        }
    }
});