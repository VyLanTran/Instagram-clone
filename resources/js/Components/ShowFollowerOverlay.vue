<script setup>
import { usePage } from "@inertiajs/vue3";

import Close from "vue-material-design-icons/Close.vue";
import UserFollowingStatus from "./UserFollowingStatus.vue";

const emit = defineEmits(["closeOverlay", "updateFollowing"]);
const currentUser = usePage().props.auth.user;

const props = defineProps({
    followers: Object,
    getIsFollowingActive: Function,
});

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
        class="fixed flex items-center z-50 top-0 left-0 w-full h-screen bg-[#000000] bg-opacity-60 p-3"
        @click="handleOverlayClick"
    >
        <div
            id="ContentArea"
            @click.stop
            class="max-w-sm w-full mx-auto mt-10 bg-white rounded-xl text-center"
        >
            <div
                class="flex items-center justify-between w-full rounded-t-xl p-3 border-b border-b-gray-300"
            >
                <div></div>
                <div class="text-lg font-extrabold">Followers</div>
                <button class="cursor-pointer" @click="emit('closeOverlay')">
                    <Close :size="27" fillColor="#000000" />
                </button>
            </div>

            <div
                class="w-full md:flex h-[calc(100%-55px)] rounded-xl overflow-auto"
            >
                <!-- If I follow this person, my account should appear on top -->
                <div class="w-full p-3 flex flex-col gap-4">
                    <div v-for="follower in followers.data" :key="follower">
                        <UserFollowingStatus
                            v-if="follower.user.id === currentUser.id"
                            :user="follower.user"
                            @updateFollowing="$emit('updateFollowing', $event)"
                            :getIsFollowingActive="getIsFollowingActive"
                        />
                    </div>
                    <div v-for="follower in followers.data" :key="follower">
                        <UserFollowingStatus
                            v-if="follower.user.id !== currentUser.id"
                            :user="follower.user"
                            @updateFollowing="$emit('updateFollowing', $event)"
                            :getIsFollowingActive="getIsFollowingActive"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
