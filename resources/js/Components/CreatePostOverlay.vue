<script setup>
import { ref, reactive } from "vue";
import { router, usePage } from "@inertiajs/vue3";

import Close from "vue-material-design-icons/Close.vue";
import ArrowLeft from "vue-material-design-icons/ArrowLeft.vue";
import MapMarkerOutline from "vue-material-design-icons/MapMarkerOutline.vue";

const user = usePage().props.auth.user;

const emit = defineEmits(["close"]);

const form = reactive({
    file: null,
    isVideo: false,
    caption: null,
    location: null,
});

let isValidFile = ref(null);
let fileName = ref("");
let caption = ref(null);
let location = ref(null);
let isVideo = ref(false);
let error = ref({
    file: null,
});

let file = ref(null);
let errorType = ref(null);
let fileData = ref(null);
let errors = ref(null);
let isUploading = ref(false);

const closeOverlay = () => {
    form.file = null;
    form.caption = null;
    form.location = null;
    form.isVideo = false;
    fileName.value = "";
    caption.value = null;
    location.value = null;
    isVideo = false;
    emit("close");
};

const clearFile = () => {
    form.file = null;
    isVideo = false;
    form.isVideo = false;
    fileName.value = "";
};

const handleOverlayClick = (event) => {
    // Check if the click occurred inside the content area
    const contentArea = document.getElementById("ContentArea");
    if (contentArea && contentArea.contains(event.target)) {
        return; // Click occurred within the content area, do nothing
    }

    // Close the overlay
    closeOverlay();
};

const onDrop = (e) => {
    form.file = e.dataTransfer.files[0];
    let extension = form.file.name.substring(
        form.file.name.lastIndexOf(".") + 1
    );

    if (
        extension == "png" ||
        extension == "jpg" ||
        extension == "jpeg" ||
        extension == "mp4"
    ) {
        isValidFile.value = true;
        if (extension == "mp4") {
            isVideo = true;
            form.isVideo = true;
        }
    } else {
        isValidFile.value = false;
        return;
    }

    fileName.value = URL.createObjectURL(e.dataTransfer.files[0]);

    setTimeout(() => {
        document
            .getElementById("Caption")
            .scrollIntoView({ behavior: "smooth" });
    }, 300);
};

const getUploadedImage = (e) => {
    form.file = e.target.files[0];
    let extension = form.file.name.substring(
        form.file.name.lastIndexOf(".") + 1
    );

    if (
        extension == "png" ||
        extension == "jpg" ||
        extension == "jpeg" ||
        extension == "mp4"
    ) {
        isValidFile.value = true;
        if (extension == "mp4") {
            isVideo = true;
            form.isVideo = true;
        }
    } else {
        isValidFile.value = false;
        return;
    }

    fileName.value = URL.createObjectURL(e.target.files[0]);
    setTimeout(() => {
        document
            .getElementById("Caption")
            .scrollIntoView({ behavior: "smooth" });
    }, 300);
};

const createPost = () => {
    console.log(form);
    error.value.file = null;

    router.post("/posts", form, {
        forceFormData: true,
        preserveScroll: true,
        onError: (errors) => {
            errors && errors.file ? (error.value.file = errors.file) : "";
        },
        onSuccess: () => {
            closeOverlay();
        },
    });
};
</script>

<template>
    <div
        id="OverlaySection"
        class="fixed z-50 top-0 left-0 w-full h-screen bg-[#000000] bg-opacity-60 p-3"
        @click="handleOverlayClick"
    >
        <button class="absolute right-3 cursor-pointer" @click="closeOverlay()">
            <Close :size="27" fillColor="#FFFFFF" />
        </button>

        <div
            id="ContentArea"
            class="max-w-4xl h-[calc(100%-100px)] mx-auto mt-10 bg-white rounded-xl"
            @click.stop
        >
            <div
                class="flex items-center justify-between w-full rounded-t-xl p-3 border-b border-b-gray-300"
            >
                <ArrowLeft
                    :size="30"
                    fillColor="#000000"
                    @click="closeOverlay()"
                />
                <div class="text-lg font-extrabold">Create new post</div>
                <button
                    @click="createPost()"
                    class="text-lg text-blue-500 hover:text-gray-900 font-extrabold"
                >
                    Post
                </button>
            </div>

            <div
                class="w-full md:flex h-[calc(100%-55px)] rounded-xl overflow-auto"
            >
                <!-- Left part is for uploading file -->
                <div
                    class="flex relative items-center bg-gray-100 w-full h-full overflow-hidden"
                >
                    <!-- If no file is chosen yet -->
                    <div
                        v-if="!fileName"
                        for="fileInput"
                        @drop.prevent="onDrop"
                        @dragover.prevent="($event) => {}"
                        class="flex flex-col items-center mx-auto mt-[-10%]"
                    >
                        <div class="mt-4 text-[17px]">
                            Select a photo or video to upload
                        </div>
                        <div class="mt-1.5 text-gray-500 text-[13px]">
                            Or drage and drop a file here
                        </div>
                        <div class="mt-12 text-gray-400 text-[13px]">
                            Image: PNG, JPG, JPEG only
                        </div>
                        <div class="mt-4 text-gray-400 text-[13px]">
                            Video: MP4, Up to 30 minutes, Less than 2GB
                        </div>

                        <label
                            for="file"
                            class="flex justify-center px-2 py-1.5 mt-8 text-white text-[15px] w-[60%] bg-[#f02c56] rounded-sm cursor-pointer"
                        >
                            Select file
                        </label>
                        <input
                            id="file"
                            type="file"
                            @input="getUploadedImage($event)"
                            accept=".png, .jpg, .jpeg, .mp4"
                            class="hidden"
                        />
                        <div
                            v-if="error && error.file"
                            class="text-red-500 text-center p-2 font-extrabold"
                        >
                            {{ error.file }}
                        </div>
                        <div
                            v-if="!fileName && isValidFile === false"
                            class="text-red-500 text-center p-2 font-extrabold"
                        >
                            File not accepted
                        </div>
                    </div>
                    <button
                        v-if="fileName && isValidFile === true"
                        class="absolute top-0 right-0 p-2"
                        @click="clearFile"
                    >
                        <Close :size="27" fillColor="#000000" />
                    </button>
                    <!-- If no error, display the uploaded file -->
                    <img
                        v-if="fileName && isValidFile === true && !isVideo"
                        class="min-w-[400px] p-4 mx-auto"
                        :src="fileName"
                    />
                    <video
                        v-if="fileName && isValidFile === true && isVideo"
                        autoplay
                        loop
                        muted
                        class="min-w-[400px] p-4 mx-auto"
                        :src="fileName"
                    />
                </div>

                <!-- Right part is for caption -->
                <div id="Caption" class="max-w-[720px] w-full relative">
                    <div class="flex items-center justify-between p-4">
                        <div class="flex items-center">
                            <img
                                class="rounded-full w-[32px] h-[32px]"
                                :src="user.file"
                            />
                            <div class="ml-4 font-extrabold text-[15px]">
                                {{ user.name }}
                            </div>
                        </div>
                    </div>

                    <div class="flex w-full max-h-[200px] bg-white border-b">
                        <textarea
                            ref="caption"
                            v-model="form.caption"
                            placeholder="Write a caption..."
                            rows="10"
                            class="placeholder-gray-400 w-full border-0 mt-2 mb-2 z-50 focus:ring-0 text-gray-800 text-[16px]"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-between border-b p-3">
                        <input
                            ref="location"
                            v-model="form.location"
                            placeholder="Add Location"
                            class="text-md placeholder-gray-500 text-gray-800 text-[16px] p-0 border-none focus:ring-0"
                            @input="getUploadedImage($event)"
                        />
                        <MapMarkerOutline :size="27" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
