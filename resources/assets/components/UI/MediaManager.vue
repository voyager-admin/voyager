<template>
    <div class="min-h-64 w-full media-manager border rounded-lg p-4 mb-4">
        <input class="hidden" type="file" :multiple="multiple" @change="addUploadFiles($event.target.files)" ref="upload_input">
        <div class="flex-grow grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 w-full mb-4" v-show="pickFiles && pickedFiles.length > 0">
            <div v-for="(file, i) in pickedFiles" :key="i" class="item rounded-md border select-none h-auto">
                <div class="flex p-3">
                    <div class="flex-none" v-show="isFileImage(file.name)">
                        <div class="w-full flex justify-center">
                            <div class="w-full flex justify-center h-24">
                                <img :src="file.url" class="rounded object-contain h-24 max-w-full" />
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow ml-3 overflow-hidden">
                        <div class="flex flex-col h-full">
                            <div class="flex-none">
                                <p class="whitespace-no-wrap" v-tooltip="file.name">{{ file.name }}</p>
                                <p v-for="(field, i) in meta" :key="i">
                                    <language-input
                                        class="input w-full small mt-1"
                                        type="text" :placeholder="translate(field.value)"
                                        v-bind:value="file.meta[field.key]"
                                        v-on:input="file.meta[field.key] = $event"
                                    />
                                </p>
                            </div>
                            <div class="flex items-end justify-end flex-grow">
                                <button @click.stop="addPickedFile(file)">
                                    <icon icon="x" :size="4"></icon>
                                </button>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
        <slide-y-down-transition>
            <div>
                <div class="w-full mb-2" v-if="showToolbar">
                    <div class="inline-block">
                        <button class="button green small" @click="upload()" :disabled="filesToUpload.length == 0" v-if="!instantUpload" v-tooltip="__('voyager::media.upload')">
                            <icon icon="upload"></icon>
                        </button>
                        <button class="button accent small" @click="selectFilesToUpload()" v-tooltip="__('voyager::media.select_upload_files')">
                            <icon icon="check-circle"></icon>
                        </button>
                        <button class="button accent small" @click="loadFiles()" v-tooltip="__('voyager::generic.reload')">
                            <icon icon="refresh"></icon>
                        </button>
                        <button class="button accent small" @click="createFolder()" v-tooltip="__('voyager::media.create_folder')">
                            <icon icon="folder-add"></icon>
                        </button>
                        <button class="button red small" @click="deleteSelected()" v-if="selectedFiles.length > 0" v-tooltip="trans_choice('voyager::media.delete_files', selectedFiles.length)">
                            <icon icon="trash"></icon>
                        </button>
                        <button class="button green small" v-show="pickFiles && selectedFiles.length > 0" @click="pickSelectedFiles()" v-tooltip="trans_choice('voyager::media.select_files', selectedFiles.length)">
                            <icon icon="check-circle"></icon>
                        </button>
                    </div>
                </div>
                <div class="w-full mb-2 rounded-md breadcrumbs">
                    <div class="button-group">
                        <span v-for="(path, i) in pathSegments" :key="'path-'+i" class="inline-block items-center">
                            <button class="m-2" @click.prevent.stop="openPath(path, i)">
                                <icon v-if="path == ''" icon="home"></icon>
                                <span v-else>{{ path }}</span>
                            </button>
                            <button class="cursor-default px-0 py-0" v-if="pathSegments.length !== (i+1)">
                                /
                            </button>
                        </span>
                    </div>
                </div>
                <div class="flex w-full min-h-64">
                    <div class="w-full max-h-256 overflow-y-auto">
                        <div class="relative flex-grow grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 " @click="selectedFiles = []">
                            <div class="absolute w-full h-full flex items-center justify-center dragdrop pointer-events-none" v-if="((filesToUpload.length == 0 && files.length == 0) || dragging) && !loadingFiles">
                                <h4>{{ dragging ? dropText : dragText }}</h4>
                            </div>
                            <div class="absolute w-full h-full flex items-center justify-center opacity-75 loading pointer-events-none" v-if="loadingFiles">
                                <icon icon="helm" :size="32" class="block rotating-cw"></icon>
                            </div>
                            <div v-if="combinedFiles.length == 0" class="h-64"></div>
                            <div
                                class="item rounded-md border cursor-pointer select-none h-auto"
                                v-for="(file, i) in combinedFiles"
                                :key="i"
                                :class="[fileSelected(file) ? 'selected' : '', file.is_upload ? 'opacity-50' : '']"
                                v-on:click.prevent.stop="selectFile(file, $event)"
                                v-on:dblclick.prevent.stop="openFile(file)">
                                <div class="flex p-3">
                                    <div class="flex-none">
                                        <div class="w-full flex justify-center">
                                            <img :src="file.preview" class="rounded object-contain h-24 max-w-full" v-if="file.preview" />
                                            <img :src="file.file.url" class="rounded object-contain h-24 max-w-full" v-else-if="mimeMatch(file.file.type, 'image/*')" />
                                            <div v-else class="w-full flex justify-center h-24">
                                                <icon :icon="getFileIcon(file.file.type)" size="24"></icon>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow ml-3 overflow-hidden">
                                        <div class="flex flex-col h-full">
                                            <div class="flex-none">
                                                <p class="whitespace-no-wrap" v-tooltip="file.file.name">{{ file.file.name }}</p>
                                                <p class="text-sm" v-if="file.file.thumbnails.length > 0">
                                                    {{ trans_choice('voyager::media.thumbnail_amount', file.file.thumbnails.length) }}
                                                </p>
                                                <p class="text-xs" v-if="file.file.type !== 'directory'">{{ readableFileSize(file.file.size) }}</p>
                                            </div>
                                            <div class="flex items-end justify-end flex-grow">
                                                <button @click.stop="deleteUpload(file)" v-if="file.is_upload">
                                                    <icon icon="x" :size="4"></icon>
                                                </button>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                                <div
                                    class="flex-none h-1 bg-blue-500 rounded-b-md"
                                    v-if="file.status == Status.Uploading"
                                    :style="{ width: file.progress+'%' }">
                                </div>
                                <!-- TODO: Display indeterminate when progress == 100 -->
                                <div
                                    class="flex-none h-1 w-full bg-green-500 rounded-b-md"
                                    v-if="file.status == Status.Finished">
                                </div>
                                <div
                                    class="flex-none h-1 w-full bg-red-500 rounded-b-md"
                                    v-if="file.status == Status.Failed">
                                </div>
                            </div>
                        </div>
                    </div>
                    <slide-x-right-transition class="flex-none">
                        <div class="sidebar h-full border rounded-md p-2 ml-3 max-w-xs" v-if="selectedFiles.length > 0">
                            <div class="w-full flex justify-center">
                                <div v-if="selectedFiles.length > 1" class="w-full flex justify-center h-32">
                                    <icon icon="document-duplicate" size="32"></icon>
                                </div>
                                <img :src="selectedFiles[0].preview" class="rounded object-contain h-32 max-w-full" v-else-if="selectedFiles[0].preview" />
                                <img :src="selectedFiles[0].file.url" class="rounded object-contain h-32 max-w-full" v-else-if="mimeMatch(selectedFiles[0].file.type, 'image/*')" />
                                <video v-else-if="mimeMatch(selectedFiles[0].file.type, 'video/*')" controls>
                                    <source :src="selectedFiles[0].file.url" :type="selectedFiles[0].file.type" />
                                </video>
                                <audio v-else-if="mimeMatch(selectedFiles[0].file.type, 'audio/*')" controls>
                                    <source :src="selectedFiles[0].file.url" :type="selectedFiles[0].file.type" />
                                </audio>
                                <div v-else class="w-full flex justify-center h-32">
                                    <icon :icon="getFileIcon(selectedFiles[0].file.type)" size="32"></icon>
                                </div>
                            </div>
                            <div class="w-full flex justify-center mt-2">
                                <div v-if="selectedFiles.length == 1">
                                    <p>{{ selectedFiles[0].file.name }}</p>
                                    <p>{{ __('voyager::media.size') }}: {{ readableFileSize(selectedFiles[0].file.size) }}</p>
                                    <input
                                        type="text"
                                        class="input small w-full mt-1 select-none"
                                        v-if="selectedFiles[0].file.type !== 'directory'"
                                        :value="encodeURI(selectedFiles[0].file.url)"
                                        @dblclick="copyPath(encodeURI(selectedFiles[0].file.url))">

                                    <ul v-if="selectedFiles[0].file.thumbnails.length > 0" class="mt-2">
                                        <span>{{ __('voyager::media.thumbnails.thumbnails') }}</span>
                                        <li v-for="(thumb, i) in selectedFiles[0].file.thumbnails" :key="i" @dblclick="copyPath(encodeURI(thumb.file.url))">
                                            <a :href="thumb.file.url" target="_blank">{{ thumb.file.name }}</a>
                                        </li>
                                    </ul>
                                </div>
                                <div v-else>
                                    <p>{{ __('voyager::media.files_selected', { num: selectedFiles.length }) }}</p>
                                    <p>{{ __('voyager::generic.approximately') }} {{ readableFileSize(selectedFilesSize) }}</p>
                                </div>
                            </div>
                            
                        </div>
                    </slide-x-right-transition>
                </div>
            </div>
        </slide-y-down-transition>
    </div>
</template>
<script>
var Status = {
    Pending  : 1,
    Uploading: 2,
    Finished : 3,
    Failed   : 4,
};

Vue.prototype.Status = Status;

export default {
    props: {
        'uploadUrl': {
            type: String,
            required: true,
        },
        'listUrl': {
            type: String,
            required: true,
        },
        'instantUpload': {
            type: Boolean,
            default: true,
        },
        'multiple': {
            type: Boolean,
            default: true,
        },
        'maxSize': {
            type: Number,
            default: 0,
        },
        'multiSelect': {
            type: Boolean,
            default: true,
        },
        'dragText': {
            type: String,
            default: 'Drag your files here',
        },
        'dropText': {
            type: String,
            default: 'Drop it like its 🔥',
        },
        'showToolbar': {
            type: Boolean,
            default: true,
        },
        'allowedMimeTypes': {
            type: Array,
            default: function () {
                return [];
            }
        },
        'pickFiles': {
            type: Boolean,
            default: false,
        },
        'max': {
            type: Number,
            default: 0,
        },
        'meta': {
            type: Array,
            default: function () {
                return [];
            },
        },
        'value': {
            type: Array,
            default: function () {
                return [];
            },
        },
    },
    data: function () {
        return {
            filesToUpload: [],
            uploading: 0,
            files: [],
            selectedFiles: [],
            path: '',
            ddCapable: true,
            dragging: false,
            loadingFiles: false,
            dragEnterTarget: null,
            pickedFiles: this.value,
        };
    },
    methods: {
        addUploadFiles: function (files) {
            var vm = this;
            vm.filesToUpload = vm.filesToUpload.concat(Array.from(files).map(function (file) {
                // Validate size
                if (vm.maxSize > 0 && (file.size > vm.maxSize)) {
                    // TODO: Show error
                    return null;
                }

                if (file.type !== '') {
                    // Validate mime type
                    if (vm.allowedMimeTypes.length > 0) {
                        var result = false;
                        vm.allowedMimeTypes.forEach(function (mime) {
                            if (vm.mimeMatch(file.type, mime.toLowerCase())) {
                                result = true;
                            }
                        });

                        if (!result) {
                            return null;
                        }
                    }
                } else {
                    // TODO: Not all documents send a mimetype. Check extension?
                }

                // Check if file already exists by name AND size
                var exists = false;
                vm.filesToUpload.forEach(function (ex_file) {
                    if (ex_file.file.name == file.name && ex_file.file.size == file.size) {
                        exists = true;
                    }
                });
                if (exists) {
                    return null;
                }

                file.thumbnails = [];

                var f = {
                    file: file,
                    is_upload: true,
                    status: Status.Pending,
                    progress: 0,
                    preview: null,

                }
                // Create FileReader if it is an image
                var matcher = new vm.MimeMatcher('image/*');
                if (matcher.match(file.type)) {
                    let reader  = new FileReader();
                    reader.addEventListener('load', function () {
                        f.preview = reader.result;
                    });
                    reader.readAsDataURL(file);
                }

                return f;
            }).filter(x => !!x));

            if (vm.instantUpload) {
                vm.upload();
            }
        },
        loadFiles: function () {
            var vm = this;
            vm.loadingFiles = true;
            vm.selectedFiles = [];
            axios.post(vm.listUrl, {
                path: vm.path
            })
            .then(function (response) {
                vm.files = response.data;
            })
            .catch(function (response) {
                
            })
            .then(function () {
                // When loaded, clear finished filesToUpload
                vm.loadingFiles = false;
            });
        },
        upload: function () {
            var vm = this;

            var file = vm.filesToUpload.whereNot('status', Status.Finished)[0];

            if (file === undefined) {
                vm.loadFiles();
                vm.filesToUpload = vm.filesToUpload.whereNot('status', Status.Finished);
                return;
            }

            vm.uploadFile(file)
            .then(function (response) {
                file.status = Status.Finished;
                file.progress = 100;

                if (response.data.exists === true) {
                    new vm.$notification(vm.__('voyager::media.file_exists', { file: file.file.name })).color('red').timeout().show();
                    file.status = Status.Failed;
                } else {
                    if (response.data.success === false) {
                        new vm.$notification(vm.__('voyager::media.file_upload_failed', { file: file.file.name })).color('red').timeout().show();
                        file.status = Status.Failed;
                    }
                }
            })
            .catch(function (response) {
                file.status = Status.Failed;
                file.progress = 0;

                if (response.response.status == 413) {
                    new vm
                    .$notification(vm.__('voyager::generic.upload_too_large', { file: file.file.name, size: vm.readableFileSize(file.file.size) }))
                    .color('red')
                    .timeout()
                    .show();
                } else {
                    new vm
                    .$notification(vm.__('voyager::generic.upload_failed', { file: file.file.name }) + '<br>' + response.response.statusText)
                    .color('red')
                    .timeout()
                    .show();
                }
            }).then(function () {
                vm.upload();
            });
        },
        uploadFile: function (file) {
            var vm = this;
            let formData = new FormData();
            formData.append('file', file.file);
            formData.append('path', vm.path);
            return axios.post(vm.uploadUrl, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                },
                onUploadProgress: function(e) {
                    file.status = Status.Uploading;
                    file.progress = Math.round((e.loaded * 100) / e.total);
                }
            });
        },
        selectFilesToUpload: function () {
            this.$refs.upload_input.click();
        },
        selectFile: function (file, e) {
            if (!e.ctrlKey || !this.multiSelect) {
                this.selectedFiles = [];
            }
            this.selectedFiles.push(file);
        },
        fileSelected: function (file) {
            return this.selectedFiles.indexOf(file) >= 0;
        },
        openFile: function (file) {
            if (file.file.type == 'directory') {
                this.path = this.path + '/' + file.file.name;
                if (!this.pickFiles) {
                    this.pushCurrentPathToUrl();
                }
                this.loadFiles();
            } else if (this.pickFiles) {
                this.addPickedFile(file);
            }
        },
        pickSelectedFiles: function () {
            var vm = this;
            vm.selectedFiles.forEach(function (file) {
                this.addPickedFile(file);
            });
        },
        addPickedFile: function (file) {
            if (file.hasOwnProperty('file')) {
                file = file.file;
            }
            if (this.pickedFiles.where('url', file.url).length > 0) {
                this.removePickedFile(file.url);
            } else {
                if (this.max > 1 && this.pickedFiles.length >= this.max) {
                    new this.$notification(this.trans_choice('voyager::formfields.media_picker.max_warning', this.max))
                    .color('orange').timeout().show();
                    return;
                }
                var fileObj = JSON.parse(JSON.stringify(file));
                fileObj.meta = {};
                var vm = this;
                Object.keys(this.meta).forEach(function (i) {
                    fileObj.meta[vm.meta[i].key] = vm.get_translatable_object('');
                });
                if (this.max == 1) {
                    this.pickedFiles = [fileObj];
                } else {
                    this.pickedFiles.push(fileObj);
                }
            }
            this.$emit('input', this.pickedFiles);
        },
        removePickedFile: function (url) {
            this.pickedFiles = this.pickedFiles.filter(f => f.url !== url);
        },
        deleteUpload: function (file) {
            this.filesToUpload.splice(this.filesToUpload.indexOf(file), 1);
        },
        getExtensionFromFilename: function (name) {
            return this.stringAfterLast('.', name).toLowerCase();
        },
        isFileImage: function (name) {
            var ext = this.getExtensionFromFilename(name);

            if (ext == 'jpeg' || ext == 'jpg' || ext == 'png' || ext == 'gif' || ext == 'bmp') {
                return true;
            }

            return false;
        },
        getFileIcon: function (type) {
            if (type == 'directory') {
                return 'folder';
            }

            return 'document';
        },
        openPath: function (path, index) {
            this.path = this.pathSegments.slice(0, (index + 1)).join('/');

            // Push path to URL
            if (!this.pickFiles) {
                this.pushCurrentPathToUrl();
            }

            this.loadFiles();
        },
        pushCurrentPathToUrl: function () {
            var url = window.location.href.split('?')[0];
            url = this.addParameterToUrl('path', this.path, url);
            this.pushToUrlHistory(url);
        },
        deleteSelected: function () {
            var vm = this;
            var files = [];
            vm.selectedFiles.forEach(function (file) {
                files.push(file.file.relative_path + file.file.name);
            });

            new vm
            .$notification(vm.trans_choice('voyager::media.delete_files_confirm', files.length))
            .color('red')
            .timeout()
            .confirm()
            .show()
            .then(function (response) {
                if (response === true) {
                    axios.delete(vm.route('voyager.media.delete'), {
                        params: {
                            files: files,
                        }
                    })
                    .then(function (response) {
                        new vm.$notification(vm.trans_choice('voyager::media.delete_files_success', files.length)).color('green').timeout().show();
                    })
                    .catch(function (errors) {
                        //
                    })
                    .then(function () {
                        vm.loadFiles();
                    });
                }
            });
        },
        createFolder: function () {
            var vm = this;
            new vm
            .$notification(vm.__('voyager::media.create_folder_prompt'))
            .timeout()
            .prompt()
            .addButton({ key: true, value: vm.__('voyager::generic.ok'), color: 'green'})
            .addButton({ key: false, value: vm.__('voyager::generic.cancel'), color: 'red'})
            .show()
            .then(function (result) {
                if (result !== false) {
                    axios.post(vm.route('voyager.media.create_folder'), {
                        path: vm.path,
                        name: result,
                    })
                    .then(function (response) {
                        new vm.$notification(vm.__('voyager::media.create_folder_success', { name: result })).color('green').timeout().show();
                        // TODO: Open newly created folder?
                    })
                    .catch(function (errors) {
                        // TODO: ...
                    })
                    .then(function () {
                        vm.loadFiles();
                    });
                }
            });
        },
        copyPath: function (path) {
            this.copyToClipboard(path);
            new this.$notification(this.__('voyager::media.path_copied')).timeout().show();
        },
    },
    computed: {
        combinedFiles: function () {
            var vm = this;

            return vm.files.filter(function (file) {
                if (vm.allowedMimeTypes.length == 0) {
                    return true;
                }
                var result = false;
                vm.allowedMimeTypes.forEach(function (mime) {
                    if (mime === 'directory') {
                        if (file.file.type === 'directory') {
                            result = true;
                        }
                    } else if (vm.mimeMatch(file.file.type, mime.toLowerCase())) {
                        result = true;
                    }
                });

                return result;
            }).concat(vm.filesToUpload);
        },
        selectedFilesSize: function () {
            var size = 0;

            this.selectedFiles.forEach(function (file) {
                size += file.file.size;
            });

            return size;
        },
        pathSegments: function () {
            return this.path.split('/');
        }
    },
    watch: {
        value: function (files) {
            this.pickedFiles = files;
        }
    },
    mounted: function () {
        var vm = this;

        var div = document.createElement('div');
        vm.ddCapable = (('draggable' in div) || ('ondragstart' in div && 'ondrop' in div)) && 'FormData' in window && 'FileReader' in window;

        if (vm.ddCapable) {
            // Prevent browser opening a new tab
            ['drag', 'dragstart', 'dragend', 'dragover', 'dragenter', 'dragleave', 'drop'].forEach(function (event) {
                vm.$el.addEventListener(event, function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                });
            });

            // Indicates that we are dragging files over our wrapper
            ['drag', 'dragstart', 'dragover', 'dragenter'].forEach(function (event) {
                vm.$el.addEventListener(event, function (e) {
                    vm.dragEnterTarget = e.target;
                    e.stopPropagation();
                    e.preventDefault();
                    vm.dragging = true;
                });
            });

            // Indicates that we left our wrapper or dropped files
            ['dragend', 'dragleave', 'drop'].forEach(function (event) {
                vm.$el.addEventListener(event, function (e) {
                    if (vm.dragEnterTarget == e.target) {
                        e.stopPropagation();
                        e.preventDefault();
                        vm.dragging = false;
                    }
                });
            });

            vm.$el.addEventListener('drop', function (e) {
                e.stopPropagation();
                e.preventDefault();
                vm.dragging = false;
                vm.addUploadFiles(e.dataTransfer.files);
            });
        }

        var path = vm.getParameterFromUrl('path', '');
        if (path !== '/' && !vm.pickFiles) {
            vm.path = path;
        }

        vm.loadFiles();
    }
};
</script>

<style lang="scss">
@import "../../sass/mixins/bg-color";
@import "../../sass/mixins/border-color";
@import "../../sass/mixins/text-color";

.dark .media-manager {
    @include bg-color(media-bg-color-dark, 'colors.gray.850');
    @include border-color(media-border-color-dark, 'colors.gray.700');

    .item {
        @include bg-color(media-item-bg-color-dark, 'colors.gray.800');
        @include border-color(media-item-border-color-dark, 'colors.gray.700');

        &:hover {
            @include bg-color(media-item-hover-bg-color-dark, 'colors.gray.700');
        }

        &.selected {
            @include bg-color(media-item-selected-bg-color-dark, 'colors.gray.750');
            @include border-color(media-item-selected-border-color-dark, 'colors.blue.700');
        }
    }

    .breadcrumbs {
        @include bg-color(media-breadcrumbs-bg-color-dark, 'colors.gray.800');
        @include border-color(media-breadcrumbs-border-color-dark, 'colors.gray.700');
    }

    .sidebar {
        @include bg-color(media-sidebar-bg-color-dark, 'colors.gray.800');
        @include border-color(media-sidebar-border-color-dark, 'colors.gray.700');
    }

    .dragdrop {
        @include bg-color(media-bg-color-dark, 'colors.gray.850');
    }

    .loading {
        @include bg-color(media-bg-color-dark, 'colors.gray.850');
    }
}

.media-manager {
    @include bg-color(media-bg-color, 'colors.white');
    @include border-color(media-border-color, 'colors.gray.300');

    .item {
        @include bg-color(media-item-bg-color, 'colors.gray.100');
        @include border-color(media-item-border-color, 'colors.gray.300');

        &:hover {
            @include bg-color(media-item-hover-bg-color, 'colors.gray.200');
        }

        &.selected {
            @include bg-color(media-item-selected-bg-color, 'colors.gray.250');
            @include border-color(media-item-selected-border-color, 'colors.blue.300');
        }
    }

    .breadcrumbs {
        @include bg-color(media-breadcrumbs-bg-color, 'colors.gray.100');
        @include border-color(media-breadcrumbs-border-color, 'colors.gray.300');
    }

    .sidebar {
        @include bg-color(media-sidebar-bg-color, 'colors.white');
        @include border-color(media-sidebar-border-color, 'colors.gray.300');
    }

    .dragdrop {
        @include bg-color(media-bg-color, 'colors.white');
    }

    .loader {
        @include bg-color(media-bg-color, 'colors.white');
    }

    @keyframes indeterminate {
        0% {
            width: 30%;
            left: -40%;
        }
        50% {
            left: 100%;
            width: 100%;
        }
        to {
            left: 100%;
            width: 0;
        }
    }

    .progress_bar_indeterminate {
        transition: width 0.25s ease;
        animation: indeterminate 2s ease infinite;
    }
}
</style>