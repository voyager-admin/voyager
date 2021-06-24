<template>
    <div v-if="action == 'query'"></div>
    <template v-else-if="action == 'edit' || action == 'add'">
        <input
            type="password"
            class="input w-full"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :placeholder="translate(options.placeholder, true)"
        >
    </template>
    <span v-else>
        ******
    </span>
</template>

<script>
import formfield from '@mixins/formfield';

export default {
    mixins: [formfield],
}
</script>