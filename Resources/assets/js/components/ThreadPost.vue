<template>
    <div class="post flex-container">
        <div class="text-center flex-row flex-author-row">
            <span class="badge badge-default" v-text="post.author.name"></span>
            <img :src="post.author.avatar" :alt="post.author.name" class="center-block img-thumbnail m-b-1">

            <span class="label label-danger m-b-1" v-if="post.author.blacklist_count">
                {{ post.author.blacklist_count }} active blacklist {{ post.author.blacklist_count | entryFilter }}
            </span>

            <div class="actions">
                <button type="button" class="btn btn-xs btn-default" @click="$parent.blockOrUnblockUser(post)">
                    <i class="fa fa-lock"></i> Block / <i class="fa fa-unlock"></i> Unblock
                </button>
            </div>
        </div>

        <div class="flex-row flex-post-row">
            <div class="panel panel-default editor-area">
                <div class="panel-heading">
                    <span class="panel-title font-size-11">#{{ postData.id }}</span>
                    <div class="panel-heading-controls">
                        <button type="button" class="btn btn-xs btn-info" @click="isEditing = true" v-show="!isEditing">
                            <i class="fa fa-edit"></i> Edit
                        </button>

                        <button type="button" class="btn btn-xs btn-danger" @click="deleteOrRecover()"
                                v-if="!isEditing && !postData.deleted_at">
                            <i class="fa fa-times-circle"></i> Delete
                        </button>

                        <button type="button" class="btn btn-xs btn-success" @click="deleteOrRecover()"
                                v-if="!isEditing && postData.deleted_at">
                            <i class="fa fa-times-circle"></i> Restore
                        </button>

                        <div v-show="isEditing">
                            <button type="button" class="btn btn-xs btn-danger" @click="isEditing = false">
                                <i class="fa fa-times"></i> Cancel
                            </button>

                            <button type="button" class="btn btn-xs btn-success" @click="savePost()">
                                <i class="fa fa-save"></i> Save
                            </button>
                        </div>
                    </div>
                </div>

                <div class="panel-body">
                    <div class="editor-wrapper" v-show="isEditing">
                        <textarea class="bb-editor" :id="'post-editor-' + _uid" v-text="postData.content"></textarea>
                    </div>
                    <div class="compiled-text" v-show="!isEditing" v-html="parsedContent"></div>
                </div>
            </div>
        </div>

    </div>
</template>
<script>
    import axios from 'axios';

    export default {
        /**
         * Component props.
         */
        props: {
            post: {
                type: Object,
                default: {}
            }
        },

        /**
         * Component data.
         */
        data() {
            return {
                isEditing: false,
                editorInstance: null,
                postData: {},
                parsedContent: ''
            };
        },

        /**
         * Created event.
         */
        created() {
            this.postData = this.post;
        },

        /**
         * Mounted event.
         */
        mounted() {
            sceditor.create(document.getElementById('post-editor-' + this._uid), {
                format: 'bbcode',
                emoticonsRoot: '/assets/forum/sceditor/',
                style: '/assets/forum/sceditor/minified/themes/content/default.min.css',
                height: 200,
                width: '100%'
            });

            this.editorInstance = sceditor.instance(document.getElementById('post-editor-' + this._uid));
            this.editorInstance.bind('keypress blur focus valuechange', () => {
                this.postData.content = this.editorInstance.getWysiwygEditorValue(true);
            });

            this.parsedContent = this.editorInstance.getWysiwygEditorValue(false);
        },

        /**
         * Component filters.
         */
        filters: {
            entryFilter: function (value) {
                return value === 1 ? 'entry' : 'entries';
            }
        },

        /**
         * Component methods.
         */
        methods: {
            /**
             * Save post content.
             */
            savePost() {
                this.$parent.setLoadingState();

                axios
                    .put(this.postData.admin_routes.update, this.postData)
                    .then(({data: post}) => {
                        this.postData = post;
                        this.isEditing = false;

                        this.parsedContent = this.editorInstance.getWysiwygEditorValue(false);
                    })
                    .catch(this.$parent.showBackendError)
                    .then(this.$parent.removeLoadingState);
            },

            /**
             * Delete/recover post.
             */
            deleteOrRecover() {
                if (!confirm('Are you sure?')) {
                    return;
                }

                this.$parent.setLoadingState();

                axios
                    .delete(this.postData.admin_routes.destroy)
                    .then(({data: post}) => {
                        this.postData = post;
                    })
                    .catch(this.$parent.showBackendError)
                    .then(this.$parent.removeLoadingState);
            }
        }
    };
</script>

<style lang="scss">
    .flex-container {
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px dashed #ccc;

        .flex-author-row {
            margin-bottom: 15px;

            span.badge {
                margin-bottom: 15px;
                display: inline-block;
            }
        }

        .flex-post-row {
            .panel {
                margin-bottom: 0 !important;
            }
        }

        @media screen and (min-width: 800px) {
            display: flex;

            .flex-row {
                flex: 1;
            }

            .flex-author-row {
                flex-grow: 1;
                flex-basis: auto;
                max-width: 200px;

                img {
                    max-width: 200px;
                }
            }

            .flex-post-row {
                padding-left: 15px;
            }
        }
    }
</style>