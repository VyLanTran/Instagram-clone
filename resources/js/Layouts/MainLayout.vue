<script setup>
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";

import Magnify from "vue-material-design-icons/Magnify.vue";
import HomeOutline from "vue-material-design-icons/HomeOutline.vue";
import ChevronLeft from "vue-material-design-icons/ChevronLeft.vue";
import PlayBoxOutline from "vue-material-design-icons/PlayBoxOutline.vue";
import MessageOutline from "vue-material-design-icons/MessageOutline.vue";
import BookmarkOutline from "vue-material-design-icons/BookmarkOutline.vue";
import PlusBoxOutline from "vue-material-design-icons/PlusBoxOutline.vue";
import Logout from "vue-material-design-icons/Logout.vue";

import MenuItem from "@/Components/MenuItem.vue";
import CreatePostOverlay from "@/Components/CreatePostOverlay.vue";

// TODO: fix active effect
let activeMenuItem = ref("");
// whether the overlay to create new post is shown
let showCreatePost = ref(false);
</script>

<template>
    <div id="MainLayout" class="w-full h-screen">
        <!-- Top Navbar for Home page -->
        <div
            v-if="$page.url === '/'"
            id="TopNavbar"
            class="fixed z-30 block w-full bg-white h-[61px] border-b border-b-gray-300"
        >
            <div class="flex items-center justify-between h-full">
                <!-- Logo -->
                <Link href="/">
                    <div class="xl:block hidden">
                        <div class="flex flex-row items-center">
                            <img
                                class="w-[60px] m-4 cursor-pointer"
                                src="/bird-logo.png"
                            />
                            <div class="text-xl text-[#ff5349] font-bold">
                                Social Tap
                            </div>
                        </div>
                    </div>
                    <img
                        class="xl:hidden block w-[60px] m-4 cursor-pointer"
                        src="/bird-logo.png"
                    />
                    <!-- TODO: Drop down (optional) -->
                </Link>

                <!-- Search bar -->
                <div
                    class="flex items-center w-[50%] lg:w-[40%] xl:w-[30%] pr-6"
                >
                    <div
                        class="flex items-center w-full bg-gray-100 rounded-lg"
                    >
                        <Magnify class="pl-4" fillColor="#8E8E8E" :size="27" />
                        <input
                            type="text"
                            placeholder="Search"
                            class="bg-transparent w-full placeholder-[#8E8E8E] border-0 ring-0 focus:ring-0"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Navbar for other pages -->
        <div
            v-if="$page.url !== '/'"
            id="TopNavUser"
            class="fixed flex items-center justify-between z-30 w-full bg-white h-[61px] border-b border-b-gray-300"
        >
            <Link href="/" class="px-4">
                <ChevronLeft :size="30" class="cursor-pointer" />
            </Link>
            <!-- Todo: change name to the name of the account that we visit, not our account -->
            <div class="font-extrabold text-lg">
                {{ $page.props.auth.user.name }}
            </div>
            <div></div>
        </div>

        <!-- Left Sidebar -->
        <div
            id="LeftSidebar"
            class="fixed h-full bg-white xl:w-[242px] w-[72px] mt-[61px] md:block hidden border-r border-r-gray-300"
        >
            <!-- Menu items -->
            <div class="flex flex-col justify-between h-screen">
                <div class="px-3 py-6">
                    <div class="flex flex-col gap-4">
                        <Link href="/">
                            <MenuItem name="Home" />
                        </Link>
                        <Link href="/">
                            <MenuItem name="Reels" />
                        </Link>
                        <Link href="/">
                            <MenuItem name="Messages" />
                        </Link>
                        <Link href="/">
                            <MenuItem name="Saved" />
                        </Link>
                        <MenuItem
                            @click="showCreatePost = true"
                            name="Create"
                        />
                        <Link
                            :href="
                                route('users.show', {
                                    id: $page.props.auth.user.id,
                                })
                            "
                        >
                            <MenuItem name="Profile" />
                        </Link>
                    </div>
                </div>

                <Link
                    :href="route('logout')"
                    as="button"
                    method="post"
                    class="flex-grow px-3 pt-[120px]"
                >
                    <MenuItem name="Log out" />
                </Link>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div
            id="RightSidebar"
            class="flex lg:justify-between h-full w-[100%-280px] xl:pl-[280px] lg:pl-[100px] overflow-auto pt-[61px]"
        >
            <div
                class="mx-auto md:pt-6 pt-20"
                :class="
                    $page.url === '/' ? 'lg:w-8/12 w-full' : 'max-w-[1200px]'
                "
            >
                <main>
                    <slot />
                </main>
            </div>

            <div
                v-if="$page.url === '/'"
                id="SuggestionsSection"
                class="lg:w-4/12 lg:block hidden text-black mt-10"
            >
                <div
                    class="max-w-[300px] flex items-center justify-between py-3"
                >
                    <div class="text-gray-500 font-extrabold">
                        Suggested for you
                    </div>
                    <button
                        class="text-[#ff5349] hover:text-gray-900 text-xs font-extrabold"
                    >
                        See All
                    </button>
                </div>

                <!-- TODO: only suggested those who we didn't follow -->
                <div
                    v-for="randUser in $page.props.randomUsers"
                    :key="randUser"
                >
                    <Link
                        :href="route('users.show', { id: randUser.id })"
                        class="flex items-center justify-between max-w-[300px] pb-2"
                    >
                        <div class="flex items-center">
                            <img
                                class="rounded-full z-10 w-[37px] h-[37px]"
                                :src="randUser.file"
                            />
                            <div class="pl-4">
                                <div class="text-black font-extrabold">
                                    {{ randUser.name }}
                                </div>
                                <div
                                    class="text-gray-500 text-extrabold text-sm"
                                >
                                    Suggested for you
                                </div>
                            </div>
                        </div>
                        <button
                            class="text-[#ff5349] hover:text-gray-900 text-xs font-extrabold"
                        >
                            Follow
                        </button>
                    </Link>
                </div>
            </div>
        </div>

        <!-- Bottom Navbar for small screen -->
        <div
            id="BottomNav"
            class="fixed z-30 bottom-0 w-full md:hidden flex items-center justify-around bg-white border-t py-2 border-t-gray-300"
        >
            <Link href="/">
                <HomeOutline
                    :size="28"
                    @click="activeMenuItem = 'Home'"
                    :class="
                        activeMenuItem === 'Home'
                            ? 'text-[#ff5349]'
                            : 'text-[#000000]'
                    "
                />
            </Link>
            <Link href="/">
                <PlayBoxOutline
                    :size="28"
                    @click="activeMenuItem = 'Reels'"
                    :class="
                        activeMenuItem === 'Reels'
                            ? 'text-[#ff5349]'
                            : 'text-[#000000]'
                    "
                />
            </Link>
            <Link href="/">
                <MessageOutline :size="22" />
            </Link>
            <Link href="/">
                <BookmarkOutline :size="28" />
            </Link>
            <PlusBoxOutline
                @click="showCreatePost = true"
                :size="28"
                class="cursor-pointer"
            />
            <Link :href="route('users.show', { id: $page.props.auth.user.id })">
                <img
                    class="rounded-full w-[30px]"
                    :src="$page.props.auth.user.file"
                />
            </Link>
            <Link :href="route('logout')" as="button" method="post">
                <Logout :size="28" />
            </Link>
        </div>
    </div>

    <!-- A new window will be popped up when click on 'Create' -->
    <CreatePostOverlay v-if="showCreatePost" @close="showCreatePost = false" />
</template>
