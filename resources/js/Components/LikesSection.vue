<script setup>
import { computed, toRefs } from "vue";
import { usePage } from "@inertiajs/vue3";

import Heart from "vue-material-design-icons/Heart.vue";
import HeartOutline from "vue-material-design-icons/HeartOutline.vue";
import CommentOutline from "vue-material-design-icons/CommentOutline.vue";
import SendOutline from "vue-material-design-icons/SendOutline.vue";
import BookmarkOutline from "vue-material-design-icons/BookmarkOutline.vue";

const props = defineProps(["post"]);
const { post } = toRefs(props);

const emit = defineEmits(["like"]);

const user = usePage().props.auth.user;

const isHeartActive = computed(() => {
    let isTrue = false;

    // if one of the like contains matched user_id and post then that specific user liked that specific post
    for (let i = 0; i < post.value.likes.length; i++) {
        const like = post.value.likes[i];
        if (like.user_id === user.id && like.post_id === post.value.id) {
            isTrue = true;
        }
    }

    return isTrue;
});
</script>

<template>
    <!-- {{ post }} -->
    <div class="flex z-26 items-center justify-between pt-1">
        <div class="flex items-center gap-2">
            <button @click="$emit('like', { post, user })" class="-mt-[14px]">
                <HeartOutline
                    v-if="!isHeartActive"
                    class="pl-3 cursor-pointer"
                    :size="26"
                />
                <Heart
                    v-else
                    class="pl-3 cursor-pointer"
                    fillColor="#FF0000"
                    :size="26"
                />
            </button>
            <CommentOutline class="pl-3 pt-[10px] cursor-pointer" :size="26" />
            <SendOutline class="pl-3 pt-[10px] cursor-pointer" :size="26" />
        </div>

        <BookmarkOutline class="pl-3 pt-[10px] cursor-pointer" :size="26" />
    </div>
</template>
