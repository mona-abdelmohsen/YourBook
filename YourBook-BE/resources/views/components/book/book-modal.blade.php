




<!-- Albums onboarding modal -->
<!-- /partials/pages/feed/modals/albums-help-modal.html -->
<div id="albums-help-modal" class="modal albums-help-modal is-xsmall has-light-bg">
    <div class="modal-background"></div>
    <div class="modal-content">
        <div class="card">
            <div class="card-heading">
                <h3>Add Photos</h3>
                <!-- Close X button -->
                <div class="close-wrap">
                            <span class="close-modal">
            <i data-feather="x"></i>
          </span>
                </div>
            </div>
            <div class="card-body">
                <div class="content-block is-active">
                    <img src="../assets/img/illustrations/cards/albums.svg" alt="" />
                    <div class="help-text">
                        <h3>Manage your photos</h3>
                        <p>
                            Lorem ipsum sit dolor amet is a dummy text used by the typography
                            industry and the web industry.
                        </p>
                    </div>
                </div>

                <div class="content-block">
                    <img src="../assets/img/illustrations/cards/upload.svg" alt="" />
                    <div class="help-text">
                        <h3>Upload your photos</h3>
                        <p>
                            Lorem ipsum sit dolor amet is a dummy text used by the typography
                            industry and the web industry.
                        </p>
                    </div>
                </div>

                <div class="slide-dots">
                    <div class="dot is-active"></div>
                    <div class="dot"></div>
                </div>
                <div class="action">
                    <button type="button" class="button is-solid accent-button next-modal raised bg-main-light " data-modal="albums-modal">
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Album upload modal -->
<!-- /partials/pages/feed/modals/albums-modal.html -->
<div id="albums-modal" class="modal albums-modal is-xxl has-light-bg">
    <div class="modal-background"></div>
    <div class="modal-content">
        <div class="card">
            <div class="card-heading">
                <h3>New Book</h3>
                <div class="button is-solid accent-button fileinput-button bg-main" @click="startUploadFlow">
                    <i class="mdi mdi-plus"></i>
                    Add Media
                </div>

                <!-- Close X button -->
                <div class="close-wrap">
                    <span class="close-modal" @click="closeUploaderModal">
                        <i data-feather="x"></i>
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="left-section">
                    <div class="album-form">
                        <div class="control">
                            <input type="text" class="input is-sm no-radius is-fade" v-model="book_name" placeholder="book name" />
                            <div class="icon">
                                <i data-feather="camera"></i>
                            </div>
                        </div>
                        <div class="field my-5">
                            <label class="label">Category</label>
                            <select id="category">
                                <option disabled hidden>Choose Category</option>
                                @foreach(auth()->user()->categories as $cat)
                                    <option value="{{$cat->id}}">{{$cat->title}}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="field my-5">
                            <label class="label">Book Description (optional)</label>
                            <div class="control">
                                <textarea v-model="description"  class="textarea has-fixed-size" placeholder="What is in your mind..."></textarea>
                            </div>
                        </div>



                    </div>

                </div>
                <div class="right-section has-slimscroll">
                    <div class="modal-uploader">
                        <div id="actions" class="columns is-multiline no-mb">
                            <div class="column is-12">
{{--                                <button type="reset" class="button is-solid grey-button cancel">--}}
{{--                                    <span>Clear all</span>--}}
{{--                                </button>--}}
                                <span class="file-count">
                                    <span id="modal-uploader-file-count">@{{ media.length }}</span> file(s)
                                </span>
                            </div>
{{--                            <div class="column is-12" :class="hideOverallUploadProgress">--}}
{{--                                <!-- The global file processing state -->--}}
{{--                                <div class="fileupload-process">--}}
{{--                                    <div id="total-progress" style="opacity: 1" class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">--}}
{{--                                        <div class="progress-bar progress-bar-success" :style="{width: overallUploadProgress+'%'}"></div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}

                        </div>

                        <div class="columns is-multiline" id="previews">

                            <div id="template" v-for="ele in media" class="column is-4 is-template">
                                <div class="preview-box">
                                    <!-- This is used as the file preview template -->
{{--                                    <div class="remove-button">--}}
{{--                                        <i class="mdi mdi-close"></i>--}}
{{--                                    </div>--}}
                                    <div style="height: 150px; overflow:hidden">
                                        <span class="preview" v-if="ele.isImage">
                                            <img :src="ele.original_url" style="width:100%;height: 100%;object-fit:cover;"/>
                                        </span>
                                        <a v-if="!ele.isImage" class="video-button" data-fancybox="" :href="ele.original_url">
                                            <img :src="ele.thumb_url" style="width:100%;height: 100%;object-fit:cover;"/>
                                        </a>
                                    </div>
                                    <div class="preview-body">
                                        <div class="item-meta">
                                            <div>
                                                <p class="name">@{{ ele.name }}</p>
                                            </div>
                                            <small class="size">@{{ formatFileSize(ele.size) }}</small>
                                        </div>
{{--                                        <div class="upload-item-progress" v-if="ele.uploadProgress != 100">--}}
{{--                                            <div class="progress active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">--}}
{{--                                                <div class="progress-bar progress-bar-success" :style="{width: ele.uploadProgress+'%'}"></div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
                                        <div class="upload-item-description">
                                            <div class="control">
                                                <textarea class="textarea is-small is-fade no-radius" v-model="ele.description" rows="4" placeholder="Describe this media ..."></textarea>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <!-- Dropdown menu -->
                <div class="dropdown is-up is-spaced is-modern is-neutral is-right dropdown-trigger">
                    <div>
                        <button class="button" aria-haspopup="true">
                            <i class="main-icon" data-feather="smile"></i>
                            <span>@{{ privacy }}</span>
                            <i class="caret" data-feather="chevron-down"></i>
                        </button>
                    </div>
                    <div class="dropdown-menu" role="menu">
                        <div class="dropdown-content">
                            <a href="#" class="dropdown-item" @click="selectPrivacy('public')">
                                <div class="media">
                                    <i data-feather="globe"></i>
                                    <div class="media-content">
                                        <h3>Public</h3>
                                        <small>Anyone can see this publication.</small>
                                    </div>
                                </div>
                            </a>
                            <a class="dropdown-item" @click="selectPrivacy('friend')">
                                <div class="media">
                                    <i data-feather="users"></i>
                                    <div class="media-content">
                                        <h3>Friends</h3>
                                        <small>only friends can see this publication.</small>
                                    </div>
                                </div>
                            </a>
                            <hr class="dropdown-divider" />
                            <a class="dropdown-item" @click="selectPrivacy('private')">
                                <div class="media">
                                    <i data-feather="lock"></i>
                                    <div class="media-content">
                                        <h3>Only me</h3>
                                        <small>Only me can see this publication.</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <button type="button" class="button is-solid accent-button" @click="createBook($event)">
                    Create Book
                </button>
            </div>
        </div>
    </div>
</div>

<lr-config
    ctx-name="my-uploader"
    pubkey="f3fd6eb40036c3ec33cf"
    max-local-file-size-bytes="10000000"
    source-list="local, url, camera, dropbox, facebook, gdrive, gphotos, instagram"
    use-cloud-image-editor="true"
    external-sources-preferred-types="image/jpeg,image/png,image/png"
    accept="image/*,video/*"
></lr-config>
<lr-upload-ctx-provider
    id="uploaderctx"
    ctx-name="my-uploader"
></lr-upload-ctx-provider>
<lr-file-uploader-regular
    css-src="https://cdn.jsdelivr.net/npm/@uploadcare/blocks@0.30.5/web/lr-file-uploader-regular.min.css"
    ctx-name="my-uploader"
    class="my-config"
    style="width:0;"
>
</lr-file-uploader-regular>

<script type="module">
    import * as LR from "https://cdn.jsdelivr.net/npm/@uploadcare/blocks@0.30.5/web/lr-file-uploader-regular.min.js";
    LR.registerBlocks(LR);
</script>

@if(env('APP_ENV') == 'local')
    <script src="{{asset('js/vue-dev.js')}}"></script>
@else
    <script src="{{asset('js/vue-prod.js')}}"/></script>
@endif

<script>
    var app = null;
    window.onload = (event) => {
        app = new Vue({
            el: '#albums-modal',
            data: {
                uploaderCtx: null,
                book_name: '',
                media: [],
                privacy: 'public',
                description: '',
            },
            mounted(){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': '{{csrf_token()}}'
                    }
                });

                this.uploaderCtx = document.querySelector('#uploaderctx');
                this.initDropdowns();
                const dataOutput = document.querySelector('lr-upload-ctx-provider')

                $("#category").select2({
                    tags: true,
                });

                dataOutput.addEventListener('data-output', this.updateMediaArray);
                dataOutput.addEventListener('remove', this.mediaRemoved);
                // dataOutput.addEventListener('cloud-modification', this.cloudModified);
                dataOutput.addEventListener('upload-finish', this.uploadFinish);
            },
            computed: {
                overallUploadProgress: function(){
                    overall = 0;
                    for (const ele of this.media) {
                        overall = overall + ele.uploadProgress;
                    }
                    return this.media.length > 0? overall/this.media.length: 0;
                },
                hideOverallUploadProgress: function(){
                    if(this.media.length && this.overallUploadProgress == 100){
                        return {
                            'is-hidden': true,
                        };
                    }else if(this.media.length == 0){
                        return {
                            'is-hidden': true,
                        };
                    }
                    return {
                        'is-hidden': false,
                    };
                },
            },
            methods: {
                selectPrivacy: function(privacy){
                    app.privacy = privacy;
                    $('.dropdown-trigger').removeClass('is-active');
                },
                createBook: async function(event){
                    var $this = $(event.target);
                    $this.addClass('is-loading');

                    // validation ...
                    if(app.media.length <= 0){
                        this.alert('error', 'Select Media !', 5000);
                        $this.removeClass('is-loading');
                        return;
                    }

                    if(app.book_name == ''){
                        this.alert('error', 'Choose Book Name.', 5000);
                        $this.removeClass('is-loading');
                        return;
                    }

                    let category = $("#category").select2('data');
                    if(category.length <= 0){
                        this.alert('error', 'Select Category');
                        $this.removeClass('is-loading');
                        return;
                    }


                    const data = {
                        media: app.media,
                        book_name: app.book_name,
                        privacy: app.privacy,
                        category: category[0].text,
                        category_id: category[0].selected? category[0].id: null,
                        description: app.description,
                    };

                    const res = await app.storeBook(data);

                    $this.removeClass('is-loading');
                },
                storeBook : async function(data){
                    return $.ajax({
                        type: 'POST',
                        data: data,
                        url: '{{route('book.store')}}',
                        success: function(res){
                            app.alert('success', 'Book Created, Enjoy', 5000);
                            window.location.replace('{{route('book.show', '')}}/'+res);
                        },
                    });
                },
                matchNewUploaded: function(data){
                    const uploadedUUID = app.media.map(function(i){
                        return i.custom_properties.uc_uuid;
                    });

                    data = data.filter(function(i){
                        return !uploadedUUID.includes(i.uuid) && i.isValid;
                    })

                    return data;
                },
                uploadFinish: function(e){
                    const newUploads = app.matchNewUploaded(e.detail);
                    const data = newUploads.map(function(item){
                        return {
                            'cdnUrl': item.cdnUrl,
                            'name'  : item.name,
                            'uuid'  : item.uuid,
                            'isImage': item.isImage,
                            'description': '',
                        };
                    })

                    $.ajax({
                        type: 'POST',
                        data: {
                            media: data,
                        },
                        url: '{{route('upload.handler')}}',
                        success: function(res){
                            app.media.push(...res)
                            console.log(res);
                        },
                    });
                },
                cloudModified: function(modifiedData, oldData, matchModifiedItemIdx){
                    const data = modifiedData.map(function(item){
                        return {
                            'cdnUrl': item.cdnUrl,
                            'name'  : item.name,
                            'uuid'  : item.uuid,
                            'isImage': item.isImage,
                            'description': '',
                        };
                    })

                    $.ajax({
                        type: 'POST',
                        data: {
                            toDelete: [oldData],
                            media: data,
                        },
                        url: '{{route('upload.handler')}}',
                        success: function(res){
                            app.media.splice(matchModifiedItemIdx, 1);
                            app.media.splice(matchModifiedItemIdx, 0, res[0]);
                        },
                    });
                },
                mediaRemoved: function(e){
                    // UC uuids
                    let deletedMediaUUID = e.detail.filter(i => i.isValid).map(function(i){
                        return i.uuid;
                    });
                    // remove media from server & upload care
                    let indicesToRemove = app.media.reduce((accumulator, obj, index) => {
                        if (deletedMediaUUID.includes(obj.custom_properties.uc_uuid)) {
                            accumulator.push(index);
                        }
                        return accumulator;
                    }, []);

                    let mediaItem = indicesToRemove.map(i => app.media[i]);
                    mediaItem = mediaItem.map((item) => {
                        return {
                            'media_id' : item.media_id,
                            'uc_uuid'  : item.custom_properties.uc_uuid
                        }
                    });

                    app.media = app.media.filter((element, index) => !indicesToRemove.includes(index));

                    $.ajax({
                        type: 'POST',
                        data: {
                            toDelete: mediaItem,
                        },
                        url: '{{route('upload.handler')}}',
                    });

                },
                removeMedia: function(uc_uuid){
                    // remove the file from upload care...
                },
                updateMediaArray: function(e){
                    // detect validation error
                    for (const obj of e.detail ?? []) {
                        if(obj.validationErrorMessage){
                            app.alert('error', obj.validationErrorMessage, 10000);
                            app.removeMedia(obj.uuid);
                            return;
                        }
                    }

                    // detect cloud modified file
                    let matchModifiedItemIdx = app.media.findIndex(function(i){
                        let mediaOutput = e.detail.filter(function (o) {
                            return o.uuid == i.custom_properties.uc_uuid;
                        });
                        if(mediaOutput.length){
                            return i.custom_properties.uc_cdnUrl != mediaOutput[0].cdnUrl;
                        }
                        return false;
                    });
                    if(matchModifiedItemIdx !== -1){
                        let mediaItem = app.media[matchModifiedItemIdx];
                        let mediaOutput = e.detail.filter(function(o){
                            return o.uuid == mediaItem.custom_properties.uc_uuid;
                        });

                        app.cloudModified(mediaOutput, mediaItem, matchModifiedItemIdx)
                    }

                },
                startUploadFlow: function(){
                    this.uploaderCtx.initFlow();
                },
                initDropdowns: function(){
                    $('.dropdown-trigger').click(function () {
                        $('.dropdown-trigger').removeClass('is-active')
                        $(this).addClass('is-active')
                    })

                    $(document).click(function (e) {
                        var target = e.target
                        if (
                            !$(target).is('.dropdown-trigger img') &&
                            !$(target).parents().is('.dropdown-trigger')
                        ) {
                            $('.dropdown-trigger').removeClass('is-active')
                        }
                    })
                },
                closeUploaderModal: function(){
                    $("#albums-modal").hide();
                },
                formatFileSize: function(bytes) {
                    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];

                    if (bytes === 0) return '0 Byte';

                    const i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));

                    return Math.round(100 * (bytes / Math.pow(1024, i))) / 100 + ' ' + sizes[i];
                },
                alert: function (type, message, time_ms) {
                    Swal.fire({
                        toast: true,
                        position: 'top-right',
                        timerProgressBar: true,
                        timer: time_ms,
                        text: message,
                        icon: type,
                        showConfirmButton: false,
                        showCloseButton: true,
                    })

                },
            },
        });

    };











</script>
