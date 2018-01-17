<template>
    <div class="editor-area">
        <button type="button" class="btn btn-xs btn-info m-b-1" @click="isEditing = true" v-show="!isEditing">
            <i class="fa fa-edit"></i> Edit
        </button>

        <div v-show="isEditing">
            <button type="button" class="btn btn-xs btn-danger m-b-1" @click="isEditing = false">
                <i class="fa fa-times"></i> Cancel
            </button>

            <button type="button" class="btn btn-xs btn-success m-b-1" @click="savePost()">
                <i class="fa fa-save"></i> Save
            </button>

            <textarea class="bb-editor" :id="'post-editor-' + _uid" v-text="postData.content"></textarea>
        </div>

        <div class="compiled-text" v-show="! isEditing" v-html="postData.parsed_content"></div>
    </div>
</template>
<script>
    import 'blockui';
    import axios from 'axios';

    export default {
        props: {
            post: {
                type: Object
            },
            update: String
        },
        data() {
            return {
                isEditing: false,
                editorSelector: null,
                postData: {},
            };
        },
        created() {
            this.postData = this.post;
        },
        mounted() {
            this.editorSelector = document.getElementById('post-editor-' + this._uid);
        },
        watch: {
            isEditing(value) {
                if (value && !sceditor.instance(this.editorSelector)) {
                    setTimeout(() => {
                        sceditor.create(this.editorSelector, {
                            format: 'bbcode',
                            emoticonsRoot: '/assets/forum/sceditor/',
                            height: 200,
                            width: '100%'
                        });
                    });
                } else {
                    sceditor.instance(this.editorSelector).destroy();
                }
            }
        },
        methods: {
            savePost() {
                $(this.$el).block({
                    message: 'Saving post..',
                    css: {
                        padding: '15px',
                        border: 'none',
                        background: 'rgba(0, 0, 0, 0.5)',
                        color: '#fff'
                    }
                });

                let content = sceditor.instance(this.editorSelector).val();

                axios
                    .put(this.update, { content })
                    .then(({data: post}) => {
                        this.postData = post;
                        this.isEditing = false;
                    })
                    .catch(() => {
                        $.growl.error({
                            message: 'Unable to save post, server error!'
                        });
                    })
                    .then(() => {
                        $(this.$el).unblock();
                    });
            }
        }
    };
</script>