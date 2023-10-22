<script setup>
import { Link, usePage } from "@inertiajs/vue3";

const currentUser = usePage().props.auth.user;

const props = defineProps({
    user: Object,
    getIsFollowingActive: Function,
});

const emit = defineEmits(["updateFollowing"]);
</script>

<template>
    <div class="flex items-center">
        <div class="w-full flex items-center justify-between">
            <Link
                :href="
                    route('users.show', {
                        id: user.id,
                    })
                "
                class="flex items-center"
            >
                <img
                    class="rounded-full w-[46px] h-[46px] mr-[-5px]"
                    :src="user.file"
                />
                <div class="ml-4 font-extrabold text-[15px]">
                    <!-- TODO: should be username on top and chosen name below -->
                    {{ user.name }}
                </div>
            </Link>
            <div v-if="user.id !== currentUser.id">
                <button
                    v-if="
                        getIsFollowingActive({
                            user: currentUser,
                            following_user: user,
                        })
                    "
                    @click="
                        $emit('updateFollowing', {
                            user: currentUser,
                            following_user: user,
                        })
                    "
                    class="p-1 px-4 rounded-lg text-[15px] text-black font-extrabold bg-gray-200 hover:bg-gray-300"
                >
                    Following
                </button>
                <button
                    v-else
                    @click="
                        $emit('updateFollowing', {
                            user: currentUser,
                            following_user: user,
                        })
                    "
                    class="p-1 px-4 rounded-lg text-[15px] text-white font-extrabold bg-[#059af7] hover:bg-blue-600"
                >
                    Follow
                </button>
            </div>
        </div>
    </div>
</template>
