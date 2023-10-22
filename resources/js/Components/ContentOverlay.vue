<script setup>
import { ref, toRefs } from "vue";

import Heart from "vue-material-design-icons/Heart.vue";
import Comment from "vue-material-design-icons/Comment.vue";

defineEmits(["selectedPost"]);
const props = defineProps(["selectedPost"]);
const { selectedPost } = toRefs(props);

let isHover = ref(false);
</script>

<template>
    <div
        @mouseenter="isHover = true"
        @mouseleave="isHover = false"
        class="flex items-center justify-center cursor-pointer relative"
        @click="$emit('selectedPost', selectedPost)"
    >
        <div
            v-if="isHover"
            :class="isHover ? 'bg-black bg-opacity-30' : ''"
            class="absolute w-full h-full z-50 flex items-center justify-around text-lg font-extrabold text-white"
        >
            <div class="flex items-center justify-around w-[50%]">
                <div class="flex items-center justify-center">
                    <Heart fillColor="#FFFFFF" :size="26" />
                    <div class="pl-1">{{ selectedPost.likes.length }}</div>
                </div>
                <div class="flex items-center justify-center">
                    <Comment fillColor="#FFFFFF" :size="26" />
                    <div class="pl-1">{{ selectedPost.comments.length }}</div>
                </div>
            </div>
        </div>

        <img
            class="aspect-square mx-auto z-0 object-cover cursor-pointer"
            :src="selectedPost.file"
        />
    </div>
</template>
