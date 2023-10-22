<script setup>
import { ref, toRefs, defineEmits } from "vue";
import { usePage } from "@inertiajs/vue3";

import ShowPostOptionsOverlay from "@/Components/ShowPostOptionsOverlay.vue";
import LikesSection from "@/Components/LikesSection.vue";

import Close from "vue-material-design-icons/Close.vue";
import DotsHorizontal from "vue-material-design-icons/DotsHorizontal.vue";
import EmoticonHappyOutline from "vue-material-design-icons/EmoticonHappyOutline.vue";

let comment = ref("");
let deleteType = ref(null);
let id = ref(null);

const user = usePage().props.auth.user;

const props = defineProps(["post"]);
const { post } = toRefs(props);

const emit = defineEmits([
    "closeOverlay",
    "addComment",
    "updateLike",
    "deleteSelected",
]);

const textareaInput = (e) => {
    textarea.value.style.height = "auto";
    textarea.value.style.height = `${e.target.scrollHeight}px`;
};

const handleOverlayClick = (event) => {
    // Check if the click occurred inside the content area
    const contentArea = document.getElementById("ContentArea");
    if (contentArea && contentArea.contains(event.target)) {
        return; // Click occurred within the content area, do nothing
    }

    // Close the overlay
    emit("closeOverlay");
};
</script>

<template>
    <div
        id="OverlaySection"
        class="fixed z-50 top-0 left-0 w-full h-screen bg-[#000000] bg-opacity-60 p-3"
        @click="handleOverlayClick"
    >
        <button class="absolute right-3" @click="$emit('closeOverlay')">
            <Close :size="27" fillColor="#FFFFFF" />
        </button>

        <div
            id="ContentArea"
            class="max-w-6xl h-[calc(100%-100px)] mx-auto mt-10 bg-white rounded-sm"
            @click.stop
        >
            <div class="w-full md:flex h-full overflow-auto rounded-sm">
                <!-- Post image -->
                <div class="flex items-center bg-black w-full">
                    <img
                        class="min-w-[400px] mx-auto w-full h-full object-cover"
                        :src="post.file"
                    />
                </div>

                <!-- Account who owns this post -->
                <div class="md:max-w-[500px] w-full relative">
                    <div class="flex items-center justify-between p-3 border-b">
                        <div class="flex items-center">
                            <img
                                class="rounded-full w-[38px] h-[38px]"
                                :src="post.user.file"
                            />
                            <div class="ml-4 font-extrabold text-[15px]">
                                {{ post.user.name }}
                            </div>
                            <div
                                class="flex items-center text-[15px] text-gray-500"
                            >
                                <span class="-mt-5 ml-2 mr-[5px] text-[35px]"
                                    >.</span
                                >
                                <div>{{ post.created_at }}</div>
                            </div>
                        </div>
                        <!-- Only allow delete post if the user is the owner of this post -->
                        <button
                            v-if="user.id === post.user.id"
                            @click="
                                deleteType = 'Post';
                                id = post.id;
                            "
                        >
                            <DotsHorizontal class="cursor-pointer" :size="27" />
                        </button>
                    </div>

                    <div class="overflow-y-auto h-[calc(100%-170px)]">
                        <!-- First comment is always the caption -->
                        <div class="flex items-center justify-between p-3">
                            <div class="flex items-center relative">
                                <img
                                    class="absolute -top-1 rounded-full w-[38px] h-[38px]"
                                    :src="post.user.file"
                                />
                                <div class="ml-14">
                                    <span
                                        class="font-extrabold text-[15px] mr-2"
                                    >
                                        {{ post.user.name }}
                                    </span>
                                    <span class="text-[15px] text-gray-900">
                                        {{ post.caption }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Comment from other accounts -->
                        <div
                            v-if="post.comments"
                            class="p-3"
                            v-for="comment in post.comments"
                            :key="comment"
                        >
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <img
                                        class="rounded-full w-[38px] h-[38px]"
                                        :src="comment.user.file"
                                    />
                                    <div
                                        class="ml-4 font-extrabold text-[15px]"
                                    >
                                        {{ comment.user.name }}
                                    </div>
                                </div>

                                <!-- Only allow the owner of the comment to delete it -->
                                <DotsHorizontal
                                    v-if="user.id === comment.user.id"
                                    class="cursor-pointer"
                                    @click="
                                        deleteType = 'Comment';
                                        id = comment.id;
                                    "
                                    :size="27"
                                />
                            </div>

                            <div class="text-[13px] pl-[55px]">
                                {{ comment.text }}
                            </div>
                        </div>

                        <div class="pb-16 md:hidden"></div>
                    </div>

                    <LikesSection
                        v-if="post"
                        class="px-2 border-t mb-2"
                        :post="post"
                        @like="$emit('updateLike', $event)"
                    />

                    <!-- Add comment section -->
                    <div
                        class="absolute flex border bottom-0 w-full max-h-[200px] bg-white overflow-auto"
                    >
                        <!-- TODO: enable emotion icon -->
                        <EmoticonHappyOutline
                            class="pl-3 pt-[10px]"
                            :size="26"
                        />
                        <textarea
                            ref="textarea"
                            :oninput="textareaInput"
                            v-model="comment"
                            placeholder="Add a comment..."
                            rows="1"
                            class="w-full border-0 mt-2 mb-2 text-sm z-50 focus:ring-0 text-gray-600 text-[16px]"
                        ></textarea>
                        <!-- This button is only activated when the comment is not blank -->
                        <button
                            v-if="comment"
                            class="text-blue-600 font-extrabold pr-4"
                            @click="
                                $emit('addComment', { post, user, comment });
                                comment = '';
                            "
                        >
                            Post
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <ShowPostOptionsOverlay
        v-if="deleteType"
        :deleteType="deleteType"
        :id="id"
        @deleteSelected="
            $emit('deleteSelected', {
                deleteType: $event.deleteType,
                id: $event.id,
                post: post,
            });
            deleteType = null;
            id = null;
        "
        @close="
            deleteType = null;
            id = null;
        "
    />
</template>
