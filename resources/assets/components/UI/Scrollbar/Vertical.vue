<template>
    <div v-if="height < 100" class="track vertical" ref="container" @click="jump">
        <div
            class="handle"
            :class="dragging || draggingFromParent ? '' : 'transition'"
            ref="scrollbar"
            @touchstart="startDrag"
            @mousedown="startDrag "
            :style="{ height: height+'%', top: scrolling + '%' }">
        </div>
    </div>
</template>

<script>
export default {
    props: {
        draggingFromParent: Boolean,
        scrolling: Number,
        wrapper: Object,
        area: Object,
        onChangePosition: Function,
        onDragging: Function,
        onStopDrag: Function,
    },
    data: function () {
        return  {
            height: 0,
            dragging: false,
            start: 0,
        };
    },
    watch: {
        'wrapper.height' () {
            this.calculateSize();
        },
        'area.height' (val) {
            console.log(val);
            this.calculateSize();
        }
    },
    methods: {
        startDrag: function (e) {
            e.preventDefault();
            e.stopPropagation();
            e = e.changedTouches ? e.changedTouches[0] : e;
            this.dragging = true;
            this.start = e.clientY;
        },
        onDrag: function (e) {
            if (this.dragging) {
                this.onDragging();
                e.preventDefault();
                e.stopPropagation();
                e = e.changedTouches ? e.changedTouches[0] : e;
                let yMovement = e.clientY - this.start;
                let yMovementPercentage = yMovement / this.wrapper.height * 100;
                this.start = e.clientY;
                let next = this.scrolling + yMovementPercentage;
                this.onChangePosition(next, 'vertical');
            }
        },
        stopDrag: function (e) {
            if (this.dragging) {
                this.onStopDrag();
                this.dragging = false;
            }
        },
        jump: function (e) {
            if (e.target === this.$refs.container) {
                let position = this.$refs.scrollbar.getBoundingClientRect();
                let yMovement = e.clientY - position.top;
                let centerize = (this.height / 2);
                let yMovementPercentage = yMovement / this.wrapper.height * 100 - centerize;
                this.start = e.clientY;
                let next = this.scrolling + yMovementPercentage;
                this.onChangePosition(next, 'vertical');
            }
        },
        calculateSize: function () {
            this.height = this.wrapper.height / this.area.height * 100;
        },
        getSize: function () {
            let $scrollArea = this.$refs.container.parentElement;
            let $scrollWrapper = $scrollArea.parentElement;
            let elementSize = {
                scrollAreaHeight: $scrollArea.children[0].clientHeight,
                scrollAreaWidth: $scrollArea.children[0].clientWidth,
                scrollWrapperHeight: $scrollWrapper.clientHeight,
                scrollWrapperWidth: $scrollWrapper.clientWidth,
            }

            return elementSize;
        },
    },
    mounted: function () {
        this.calculateSize();
        document.addEventListener("mousemove", this.onDrag);
        document.addEventListener("touchmove", this.onDrag);
        document.addEventListener("mouseup", this.stopDrag);
        document.addEventListener("touchend", this.stopDrag);
    },
    beforeDestroy() {
        document.removeEventListener("mousemove", this.onDrag);
        document.removeEventListener("touchmove", this.onDrag);
        document.removeEventListener("mouseup", this.stopDrag);
        document.removeEventListener("touchend", this.stopDrag);
    },
}
</script>