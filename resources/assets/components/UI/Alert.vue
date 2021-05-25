<template>
    <div
        class="rounded-md p-2 border"
        :class="`border-${color}-500`"
        uses="border-red-500 border-orange-500 border-yellow-500 border-green-500 border-teal-500 border-blue-500 border-indigo-500 border-purple-500 border-pink-500 border-gray-500"
    >
        <div class="flex space-x-3 items-center">
            <div class="flex-shrink-0 h-5 w-5" v-if="icon">
                <icon
                    :icon="icon"
                    :size="5"
                    :class="`text-${color}-500`"
                    uses="text-red-500 text-orange-500 text-yellow-500 text-green-500 text-teal-500 text-blue-500 text-indigo-500 text-purple-500 text-pink-500 text-gray-500"
                />
            </div>
            <div>
                <h3
                    class="text-base leading-5 font-medium"
                    :class="`text-${color}-500`"
                    uses="text-red-500 text-orange-500 text-yellow-500 text-green-500 text-teal-500 text-blue-500 text-indigo-500 text-purple-500 text-pink-500 text-gray-500"
                    v-if="$slots.title"
                >
                    <slot name="title"></slot>
                </h3>
                <div
                    class="text-sm leading-5"
                    :class="[`text-${color}-500`, {'mt-1': $slots.title}]"
                    uses="text-red-500 text-orange-500 text-yellow-500 text-green-500 text-teal-500 text-blue-500 text-indigo-500 text-purple-500 text-pink-500 text-gray-500"
                >
                    <slot></slot>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    emits: ['close'],
    props: {
        color: {
            type: String,
            default: 'green'
        },
        icon: {
            type: String,
            default: 'information-circle'
        },
    }
};
</script>