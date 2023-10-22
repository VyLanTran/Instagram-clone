<script setup>
import { toRefs } from "vue";
import { usePage } from "@inertiajs/vue3";

import HomeOutline from "vue-material-design-icons/HomeOutline.vue";
import PlayBoxOutline from "vue-material-design-icons/PlayBoxOutline.vue";
import MessageOutline from "vue-material-design-icons/MessageOutline.vue";
import BookmarkOutline from "vue-material-design-icons/BookmarkOutline.vue";
import PlusBoxOutline from "vue-material-design-icons/PlusBoxOutline.vue";
import AccountOutline from "vue-material-design-icons/AccountOutline.vue";
import Logout from "vue-material-design-icons/Logout.vue";

const props = defineProps({ name: String });
const { name } = toRefs(props);

const user = usePage().props.auth.user;

let icon = null;

if (name.value === "Home") icon = HomeOutline;
if (name.value === "Reels") icon = PlayBoxOutline;
if (name.value === "Messages") icon = MessageOutline;
if (name.value === "Saved") icon = BookmarkOutline;
if (name.value === "Create") icon = PlusBoxOutline;
if (name.value === "Profile") icon = AccountOutline;
if (name.value === "Log out") icon = Logout;
</script>

<template>
    <div
        class="w-full xl:inline-block xl:hover:bg-gray-100 p-2 rounded-md transition duration-300 ease-in-out cursor-pointer"
    >
        <div class="flex items-center">
            <img
                v-if="name === 'Profile'"
                class="rounded-full w-[28px] cursor-pointer"
                :src="user.file"
            />
            <component v-else :is="icon" :size="28" />
            <span class="xl:block hidden text-black text-[14px] pl-4">
                {{ name }}
            </span>
        </div>
    </div>
</template>
