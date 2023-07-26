<v-media {{ $attributes }} >
    <x-admin::shimmer.image class="w-[110px] h-[110px] rounded-[4px]"></x-admin::shimmer.image>
</v-media>

@pushOnce('scripts')
    <script 
        type="text/x-template" 
        id="v-media-template"
    >
        <div
            class="flex flex-col items-center justify-center bg-[#F5F5F5] rounded-[4px] cursor-pointer hover:bg-gray-100"
            :class="{'border border-dashed border-gray-300 rounded-[18px]': isDragOver }"
            :style="{'max-width': + this.width + 'px', 'max-height': + this.height + 'px'}"
            v-if="uploadedFiles.isPicked"
        >
            <div 
                class="relative group flex justify-center"
                :style="{'width': + this.width + 'px', 'height': + this.height + 'px'}"
                @mouseenter="uploadedFiles.showDeleteButton = true"
                @mouseleave="uploadedFiles.showDeleteButton = false"
            >
                <img
                    class="rounded-[4px] object-cover"
                    :src="uploadedFiles.url"
                    :class="{'opacity-25' : uploadedFiles.showDeleteButton}"
                >

                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <span 
                        class="icon-cancel text-[24px] text-black cursor-pointer"
                        @click="removeFile"
                    >
                    </span>
                </div>
            </div>
        </div>

        <label 
            :for="name"
            class="flex flex-col justify-center items-center cursor-pointer hover:bg-gray-100"
            :style="{'max-width': + this.width + 'px', 'max-height': + this.height + 'px'}"
            v-show="! uploadedFiles.isPicked"
            @dragover="onDragOver"
            @dragleave="onDragLeave"
            @drop="onDrop"
        >
            <div 
                class="grid justify-items-center items-center w-full h-[120px] border border-dashed border-gray-300 rounded-[4px] cursor-pointer transition-all hover:border-gray-400"
                :style="{'max-width': + this.width + 'px', 'max-height': + this.height + 'px'}"
            >
                <div class="flex flex-col items-center">
                    <span class="icon-image text-[24px]"></span>
                    <p class="grid text-[14px] text-gray-600 font-semibold text-center">
                        @{{ this.label }}<span class="text-[12px]">png, jpeg, jpg</span>
                    </p>
                </div>
            </div>

            <v-field
                type="file"
                :name="name"
                :id="name"
                class="hidden"
                :accept="acceptedTypes"
                :rules="appliedRules"
                :multiple="isMultiple"
                @change="onFileChange"
            >
            </v-field>
        </label>

        <div 
            class="flex items-center"
            v-if="isMultiple"
        >
            <ul class="flex gap-[10px] flex-wrap justify-left mt-2">
                <li 
                    v-for="(file, index) in uploadedFiles"
                    :key="index"
                >
                    <template v-if="isImage(file)">
                        <div 
                            class="relative group flex justify-center h-12 w-12"
                            @mouseenter="file.showDeleteButton = true"
                            @mouseleave="file.showDeleteButton = false"
                        >
                            <img
                                :src="file.url"
                                :alt="file.name"
                                class="rounded-[4px] min-w-[48px] max-h-[48px]"
                                :class="{'opacity-25' : file.showDeleteButton}"
                            >

                            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <span 
                                    class="icon-cancel text-[24px] text-black cursor-pointer"
                                    @click="removeFile(index)"
                                >
                                </span>
                            </div>
                        </div>
                    </template>

                    <template v-else>
                        <div
                            class="relative group flex justify-center h-12 w-12"
                            @mouseenter="file.showDeleteButton = true"
                            @mouseleave="file.showDeleteButton = false"
                        >
                            <video
                                :src="file.url"
                                :alt="file.name"
                                class="rounded-[4px] min-w-[50px] max-h-[50px]"
                                :class="{'opacity-25' : file.showDeleteButton}"
                            >
                            </video>

                            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <span 
                                    class="icon-cancel text-[24px] text-black cursor-pointer"
                                    @click="removeFile(index)"
                                >
                                </span>
                            </div>
                        </div>
                    </template>
                </li>
            </ul>
        </div>
    </script>

    <script type="module">
        app.component("v-media", {
            template: '#v-media-template',

            props: {
                name: {
                    type: String, 
                    default: 'attachments',
                }, 

                isMultiple: {
                    type: Boolean,
                    default: false,
                }, 

                rules: {
                    type: String,
                },

                acceptedTypes: {
                    type: String, 
                    default: 'image/*, video/*,'
                }, 

                label: {
                    type: String, 
                    default: 'Add attachments'
                }, 

                src: {
                    type: String,
                    default: ''
                },

                width: {
                    type: Number,
                    default: 110,
                },

                height: {
                    type: Number,
                    default: 110,
                },
            },

            data() {
                return {
                    uploadedFiles: [],

                    isDragOver: false,

                    appliedRules: '',
                };
            },

            created() {
                this.appliedRules = this.rules;

                if (this.src != '') {
                    this.appliedRules = '';

                    this.uploadedFiles = {
                        isPicked: true,
                        url: this.src,
                    }
                }
            },

            methods: {
                onFileChange(event) {
                    let files = event.target.files;

                    for (let i = 0; i < files.length; i++) {
                        let file = files[i];

                        let reader = new FileReader();

                        reader.onload = () => {
                            if (! this.isMultiple) {
                                this.uploadedFiles = {
                                    isPicked: true,
                                    name: file.name,
                                    url: reader.result,
                                }

                                return;
                            }

                            this.uploadedFiles.push({
                                name: file.name,
                                url: reader.result,
                            });
                        };

                        reader.readAsDataURL(file);
                    }
                },

                handleDroppedFiles(files) {
                    for (let i = 0; i < files.length; i++) {
                        let file = files[i];

                        let reader = new FileReader();
                        
                        reader.onload = () => {
                            if (! this.isMultiple) {
                                this.uploadedFiles = {
                                    isPicked: true,
                                    name: file.name,
                                    url: reader.result,
                                }

                                return;
                            }

                            this.uploadedFiles.push({
                                name: file.name,
                                url: reader.result,
                            });
                        };

                        reader.readAsDataURL(file);
                    }
                },

                isImage(file) {
                    if (! file.name) {
                        return;
                    }

                    return file.name.match(/\.(jpg|jpeg|png|gif)$/i);
                },

                onDragOver(event) {
                    event.preventDefault();

                    this.isDragOver = true;
                },

                onDragLeave(event) {
                    event.preventDefault();

                    this.isDragOver = false;
                },
                
                onDrop(event) {
                    event.preventDefault();

                    this.isDragOver = false;

                    let files = event.dataTransfer.files;

                    this.handleDroppedFiles(files);
                },

                removeFile(index) {
                    if (! this.isMultiple) {
                        this.uploadedFiles = [];

                        this.appliedRules = this.rules;
                        
                        return;
                    }

                    if (typeof this.uploadedFiles == 'object') {
                        return;
                    }

                    this.uploadedFiles.splice(index, 1);
                },
            },        
        });
    </script>
@endpushOnce