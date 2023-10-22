<script setup>
import { toRefs, ref, onMounted } from "vue";

import VolumeOff from "vue-material-design-icons/VolumeOff.vue";
import VolumeHigh from "vue-material-design-icons/VolumeHigh.vue";

const props = defineProps(["post"]);
const { post } = toRefs(props);
let video = ref(null);
let muted = ref(true);

const toggleMuted = () => {
    muted.value = !muted.value;
    if (video.value) {
        video.value.muted = muted.value;
    }
};

onMounted(() => {
    if (video.value) {
        // Set the current time to 0 to start playing from the beginning.
        video.value.currentTime = 0;
        video.value.play();
    }
});
</script>

<template>
    <div class="relative" style="position: relative">
        <video
            ref="video"
            loop
            :muted="muted"
            :src="post.file"
            class="object-cover mx-auto h-full"
            style="width: 100%; height: 100%"
        />
        <button
            @click="muted = !muted"
            class="absolute bottom-2 right-2 cursor-pointer"
            style="
                position: absolute;
                background-color: transparent;
                border: none;
                outline: none;
                overflow: hidden;
            "
        >
            <div class="bg-gray-800 rounded-full w-7 h-7 relative">
                <VolumeOff
                    v-if="muted"
                    :size="16"
                    fillColor="#ffffff"
                    style="
                        position: absolute;
                        top: 50%;
                        left: 50%;
                        transform: translate(-50%, -50%);
                    "
                />
                <VolumeHigh
                    v-else
                    :size="16"
                    fillColor="#ffffff"
                    style="
                        position: absolute;
                        top: 50%;
                        left: 50%;
                        transform: translate(-50%, -50%);
                    "
                />
            </div>
        </button>
    </div>
</template>
