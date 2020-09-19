<template>
    <div v-if="action == 'query'"></div>
    <div v-else-if="action == 'edit' || action == 'add'">
        <input
            type="password"
            class="input w-full"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :placeholder="translate(options.placeholder, true)"
        >
    </div>
    <div v-else>
        ******
    </div>
</template>

<script>
import formfield from '../../../js/mixins/formfield';

export default {
    mixins: [formfield],
}
</script>