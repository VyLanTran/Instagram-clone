<script setup>
import { reactive, toRefs, computed, ref } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { usePage } from "@inertiajs/vue3";

import MainLayout from "@/Layouts/MainLayout.vue";
import ShowPostOverlay from "@/Components/ShowPostOverlay.vue";
import ContentOverlay from "@/Components/ContentOverlay.vue";
import ShowFollowingOverlay from "@/Components/ShowFollowingOverlay.vue";
import ShowFollowerOverlay from "@/Components/ShowFollowerOverlay.vue";

import Grid from "vue-material-design-icons/Grid.vue";
import PlayBoxOutline from "vue-material-design-icons/PlayBoxOutline.vue";
import BookmarkOutline from "vue-material-design-icons/BookmarkOutline.vue";

const currentUser = usePage().props.auth.user;
let data = reactive({ post: null });
const form = reactive({ file: null });

let openFollowingOverlay = ref(false);
let openFollowerOverlay = ref(false);

const props = defineProps({
    postsByUser: Object,
    user: Object,
    followingsByUser: Object,
    followersByUser: Object,
    allFollowings: Object,
});
const { postsByUser, user, followingsByUser, followersByUser, allFollowings } =
    toRefs(props);

// Update profile avatar
const getUploadedImage = (e) => {
    form.file = e.target.files[0];
    router.post(`/users`, form, {
        preserveState: false,
    });
};

const getIsFollowingActive = (object) => {
    if (object.user.id === object.following_user.id) {
        return false;
    }

    let isTrue = false;

    for (let i = 0; i < allFollowings.value.data.length; i++) {
        const following = allFollowings.value.data[i];
        if (
            following.user.id === object.user.id &&
            following.following_user.id === object.following_user.id
        ) {
            isTrue = true;
        }
    }

    return isTrue;
};

const isFollowingActive = computed(() => {
    return getIsFollowingActive({
        user: currentUser,
        following_user: user.value,
    });
});

const updatedPost = (object) => {
    for (let i = 0; i < postsByUser.value.data.length; i++) {
        const post = postsByUser.value.data[i];
        if (post.id === object.post.id) {
            data.post = post;
        }
    }
};

const addComment = (object) => {
    router.post(
        "/comments",
        {
            post_id: object.post.id,
            user_id: object.user.id,
            comment: object.comment,
        },
        {
            onFinish: () => updatedPost(object),
        }
    );
};

const deleteFunc = (object) => {
    let url = "";
    if (object.deleteType === "Post") {
        url = "/posts/" + object.id;
        setTimeout(() => (data.post = null), 100);
    } else {
        url = "/comments/" + object.id;
    }

    router.delete(url, {
        onFinish: () => updatedPost(object),
    });
};

const updateLike = (object) => {
    let deleteLike = false;
    let id = null;

    for (let i = 0; i < object.post.likes.length; i++) {
        const like = object.post.likes[i];
        if (
            like.user_id === object.user.id &&
            like.post_id === object.post.id
        ) {
            deleteLike = true;
            id = like.id;
        }
    }

    if (deleteLike) {
        router.delete("/likes/" + id, {
            onFinish: () => updatedPost(object),
        });
    } else {
        router.post(
            "/likes",
            {
                post_id: object.post.id,
            },
            {
                onFinish: () => updatedPost(object),
            }
        );
    }
};

const updateFollowing = (object) => {
    let deleteFollowing = false;
    let id = null; // id of the following in the Followings table

    for (let i = 0; i < allFollowings.value.data.length; i++) {
        const following = allFollowings.value.data[i];
        // if already follow, then unfollow
        if (
            following.user.id === object.user.id &&
            following.following_user.id === object.following_user.id
        ) {
            deleteFollowing = true;
            id = following.id;
        }
    }

    if (deleteFollowing) {
        router.delete("/followings/" + id);
    } else {
        router.post("/followings", {
            following_user_id: object.following_user.id,
        });
    }
};
</script>

<template>
    <Head title="Profile" />
    <MainLayout>
        <div class="pt-2 md:pt-6"></div>
        <div class="max-w-[880px] lg:ml-0 md:ml-[80px] md:pl-20 px-4 w-[100vw]">
            <div class="grid grid-cols-12 items-center">
                <!-- Upload profile avatar -->
                <div class="col-span-4 sm:col-span-3">
                    <label for="fileUser">
                        <img
                            class="rounded-full object-fit w-[90px] sm:w-[160px] cursor-pointer"
                            :src="user.file"
                        />
                    </label>
                    <!-- Only allow account owner to update avatar -->
                    <input
                        id="fileUser"
                        class="hidden"
                        type="file"
                        @input="getUploadedImage($event)"
                    />
                </div>

                <div class="hidden sm:block md:col-span-1"></div>

                <!-- User information (on the right of avatar) -->
                <div class="col-span-8 w-full mt-[-70px]">
                    <div class="flex items-center md:mb-8 mb-5">
                        <div
                            class="md:mr-6 mr-3 rounded-lg text-[16px] sm:text-[20px] font-extrabold"
                        >
                            {{ user.name }}
                        </div>
                        <!-- TODO: if current user -> edit, else -> follow (following) & message -->
                        <!-- Buttons for larger screens -->
                        <div class="md:block hidden">
                            <div
                                v-if="currentUser.id === user.id"
                                class="flex gap-2"
                            >
                                <button
                                    class="p-1 px-6 rounded-lg text-[16px] font-extrabold bg-gray-100 hover:bg-gray-200"
                                >
                                    Edit profile
                                </button>
                            </div>
                            <div v-else class="flex gap-2">
                                <button
                                    v-if="isFollowingActive"
                                    @click="
                                        updateFollowing({
                                            user: currentUser,
                                            following_user: user,
                                        })
                                    "
                                    class="p-1 px-6 rounded-lg text-[16px] font-extrabold bg-gray-100 hover:bg-gray-200"
                                >
                                    Following
                                </button>
                                <button
                                    v-else
                                    @click="
                                        updateFollowing({
                                            user: currentUser,
                                            following_user: user,
                                        })
                                    "
                                    class="p-1 px-6 rounded-lg text-[16px] text-white font-extrabold bg-[#279af2] hover:bg-blue-600"
                                >
                                    Follow
                                </button>
                                <button
                                    class="md:mr-6 p-1 px-5 rounded-lg text-[16px] font-extrabold bg-gray-100 hover:bg-gray-200"
                                >
                                    Message
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Buttons for smaller screens -->
                    <div class="md:hidden">
                        <div
                            v-if="currentUser.id === user.id"
                            class="flex gap-2"
                        >
                            <button
                                class="p-1 px-6 rounded-lg text-[16px] font-extrabold bg-gray-100 hover:bg-gray-200"
                            >
                                Edit profile
                            </button>
                        </div>
                        <div v-else class="flex gap-2">
                            <button
                                v-if="isFollowingActive"
                                @click="
                                    updateFollowing({
                                        user: currentUser,
                                        following_user: user,
                                    })
                                "
                                class="p-1 px-6 rounded-lg text-[16px] font-extrabold bg-gray-100 hover:bg-gray-200"
                            >
                                Following
                            </button>
                            <button
                                v-else
                                @click="
                                    updateFollowing({
                                        user: currentUser,
                                        following_user: user,
                                    })
                                "
                                class="p-1 px-6 rounded-lg text-[16px] text-white font-extrabold bg-[#279af2] hover:bg-blue-600"
                            >
                                Follow
                            </button>
                            <button
                                class="md:mr-6 p-1 px-5 rounded-lg text-[16px] font-extrabold bg-gray-100 hover:bg-gray-200"
                            >
                                Message
                            </button>
                        </div>
                    </div>
                    <!-- Stats for larger screens -->
                    <div class="md:block hidden">
                        <div class="flex items-center text-[16px]">
                            <div class="mr-6">
                                <span class="font-extrabold"
                                    >{{ postsByUser.data.length }}
                                </span>
                                posts
                            </div>
                            <button
                                class="mr-6"
                                @click="openFollowerOverlay = true"
                            >
                                <span class="font-extrabold">{{
                                    followersByUser.data.length
                                }}</span>
                                followers
                            </button>
                            <button
                                class="mr-6"
                                @click="openFollowingOverlay = true"
                            >
                                <span class="font-extrabold">{{
                                    followingsByUser.data.length
                                }}</span>
                                following
                            </button>
                        </div>
                    </div>

                    <!-- TODO: Bio section -->
                </div>
            </div>
        </div>

        <!-- Stats for smaller screens -->
        <div class="md:hidden">
            <div
                class="w-full flex items-center justify-around border-t border-t-gray-300 mt-8 text-[14px]"
            >
                <div class="text-center p-3">
                    <div class="font-extrabold">
                        {{ postsByUser.data.length }}
                    </div>
                    <div class="text-gray-400 font-semibold -mt-1.5">posts</div>
                </div>
                <button
                    @click="openFollowerOverlay = true"
                    class="text-center p-3"
                >
                    <div class="font-extrabold">
                        {{ followersByUser.data.length }}
                    </div>
                    <div class="text-gray-400 font-semibold -mt-1.5">
                        followers
                    </div>
                </button>
                <button
                    @click="openFollowingOverlay = true"
                    class="text-center p-3"
                >
                    <div class="font-extrabold">
                        {{ followingsByUser.data.length }}
                    </div>
                    following
                </button>
            </div>

            <!-- Categories for smaller screens -->
            <div
                class="w-full flex items-center justify-between border-t border-t-gray-300"
            >
                <div
                    class="p-3 w-1/4 flex justify-center border-t border-t-gray-900"
                >
                    <Link href="/">
                        <Grid
                            :size="28"
                            fillColor="#0095F6"
                            class="cursor-pointer"
                        />
                    </Link>
                </div>
                <div class="p-3 w-1/4 flex justify-center">
                    <Link href="/">
                        <PlayBoxOutline
                            :size="28"
                            fillColor="#8E8E8E"
                            class="cursor-pointer"
                        />
                    </Link>
                </div>
                <!-- TODO: This should go away if its not our account -->
                <div class="p-3 w-1/4 flex justify-center">
                    <Link href="/">
                        <BookmarkOutline
                            :size="28"
                            fillColor="#8E8E8E"
                            class="cursor-pointer"
                        />
                    </Link>
                </div>
            </div>
        </div>

        <!-- Categories for larger screens -->
        <div id="ContentSection" class="md:pr-1.5 lg:pl-0 md:pl-[90px]">
            <div class="md:block mt-10 hidden border-t border-t-gray-300">
                <div
                    class="flex items-center justify-between max-w-[600px] mx-auto font-extrabold text-gray-400 text-[13px]"
                >
                    <Link href="/">
                        <div
                            class="p-[17px] w-1/4 flex justify-center items-center border-t border-t-gray-900"
                        >
                            <Grid :size="15" fillColor="#000000" />
                            <div class="ml-2 -mb-[1px] text-gray-900">
                                POSTS
                            </div>
                        </div>
                    </Link>
                    <Link href="/">
                        <div
                            class="p-[17px] w-1/4 flex justify-center items-center"
                        >
                            <PlayBoxOutline :size="15" fillColor="#8E8E8E" />
                            <div class="ml-2 -mb-[1px] text-gray-900">
                                REELS
                            </div>
                        </div>
                    </Link>
                    <!-- TODO: Only show if log in user is this profile -->
                    <Link href="/">
                        <div
                            class="p-[17px] w-1/4 flex justify-center items-center"
                        >
                            <BookmarkOutline
                                :size="15"
                                fillColor="#8E8E8E"
                                class="cursor-pointer"
                            />
                            <span class="ml-2 -mb-[1px]">SAVED</span>
                        </div>
                    </Link>
                </div>
            </div>

            <div class="grid gap-1 grid-cols-3 relative px-[5%] lg:px-[8%]">
                <div v-for="postByUser in postsByUser.data" :key="postByUser">
                    <ContentOverlay
                        :selectedPost="postByUser"
                        @selectedPost="data.post = $event"
                    />
                </div>
            </div>

            <div class="pb-20"></div>
        </div>
    </MainLayout>

    <ShowPostOverlay
        v-if="data.post"
        :post="data.post"
        @addComment="addComment($event)"
        @updateLike="updateLike($event)"
        @deleteSelected="deleteFunc($event)"
        @closeOverlay="data.post = null"
    />

    <ShowFollowingOverlay
        v-if="openFollowingOverlay"
        :followings="followingsByUser"
        :getIsFollowingActive="getIsFollowingActive"
        @closeOverlay="openFollowingOverlay = false"
        @updateFollowing="updateFollowing($event)"
    />

    <ShowFollowerOverlay
        v-if="openFollowerOverlay"
        :followers="followersByUser"
        :getIsFollowingActive="getIsFollowingActive"
        @closeOverlay="openFollowerOverlay = false"
        @updateFollowing="updateFollowing($event)"
    />
</template>
