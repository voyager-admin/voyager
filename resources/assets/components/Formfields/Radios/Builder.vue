<template>
    <div>
        <div v-if="show == 'list-options' || show == 'view-options'">
            <key-value-form v-model="options.options"></key-value-form>
        </div>
        <div v-else-if="show == 'view'">
            <div v-for="option in options.options" :key="option.key">
                <input type="radio" :id="option.key" class="input" :value="option.key" :name="column">
                <label :for="option.key">{{ translate(option.value) }}</label>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['options', 'column', 'show'],
};
</script>