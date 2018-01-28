'use strict';

import 'blockui';
import axios from 'axios';

new Vue({
    /**
     * Element to bind application on to.
     */
    el: '#thread-app',

    /**
     * Application data.
     */
    data: {
        posts: {
            total: 0,
            per_page: 2,
            from: 1,
            to: 0,
            current_page: 1
        },

        blockModal: {
            isLoading: true,
            data: {},
            entries: [],

            disabled: {
                forum: false,
                thread: false,
                categories: []
            },

            form: {
                level: null,
                category_id: null,
                expires_at: null
            }
        },

        offset: 9
    },

    /**
     * Created event listener.
     */
    created() {
        this.loadPosts();
    },

    /**
     * Mounted event listener.
     */
    mounted() {
        let self = this;

        $('a.editable').editable({
            mode: 'inline',
            success(response, newValue) {
                if (response.status === 'error') {
                    return response.message;
                }
            }
        });

        let select = $('#block-category').select2({
            ajax: {
                url: '/admin/forum/blacklist/get-categories',
                dataType: 'json',
                data(params) {
                    return {
                        search: params.term,
                        disable: self.blockModal.disabled.categories
                    };
                }
            }
        });

        select.data('select2').$selection.css('height', '30px');
        select.on('select2:select', e => {
            self.blockModal.form.category_id = e.params.data.id;
        });
    },

    /**
     * Available methods.
     */
    methods: {
        /**
         * Fetch posts for requested page.
         */
        loadPosts() {
            this.setLoadingState();

            let route = `${$('#thread-app').data('route')}?page=${this.posts.current_page}`;

            axios.get(route).then(({data: posts}) => {
                this.posts = posts;
            }).catch(this.showBackendError).then(this.removeLoadingState);
        },

        /**
         * Display server error error.
         *
         * @param err
         */
        showBackendError(err) {
            console.error(err);

            if (err.response) {
                return $.growl.error({
                    title: err.response.statusText,
                    message: err.response.data.message
                });
            }

            $.growl.error({
                message: 'Unknown server error!'
            });
        },

        /**
         * Sets loading state.
         */
        setLoadingState() {
            $(this.$el).block({
                message: 'Please wait',
                css: {
                    padding: '15px',
                    border: 'none',
                    background: 'rgba(0, 0, 0, 0.5)',
                    color: '#fff'
                }
            });
        },

        /**
         * Removes loading state.
         */
        removeLoadingState() {
            $(this.$el).unblock();
        },

        /**
         * Load blacklist entries.
         */
        loadBlacklistEntries() {
            axios
                .get('/admin/forum/blacklist/get-entries', {
                    params: this.blockModal.data
                })
                .then(({data}) => {
                    this.blockModal.disabled = data.disabled;
                    this.blockModal.entries = data.entries;
                    this.blockModal.isLoading = false;
                })
                .catch(this.showBackendError);
        },

        /**
         * Show user blacklist management modal.
         *
         * @param post
         */
        blockOrUnblockUser(post) {
            this.blockModal.isLoading = true;
            this.blockModal.data = {
                user_id: post.user.id,
                thread_id: post.thread_id
            };

            $('#block-modal').modal('show');

            this.loadBlacklistEntries();
        },

        /**
         * Remove user blacklist entry.
         *
         * @param event
         * @param entry
         */
        removeBlacklistEntry(event, entry) {
            let button = $(event.target);
            button.data('loading-text', $('<i>').addClass('fa fa-spin fa-spinner')).button('loading');

            axios
                .delete('/admin/forum/blacklist/' + entry.id)
                .then(res => {
                    this.blockModal.isLoading = true;
                    this.loadBlacklistEntries();
                    this.loadPosts();
                })
                .catch((err) => {
                    this.showBackendError(err);
                    button.button('reset');
                });
        },

        /**
         * Add blacklist entry.
         *
         * @param event
         */
        addBlacklistEntry(event) {
            let button = $(event.target);
            button.data('loading-text', $('<i>').addClass('fa fa-spin fa-spinner'));
            button.button('loading');

            axios
                .post('/admin/forum/blacklist/create', {
                    user_id: this.blockModal.data.user_id,
                    thread_id: this.blockModal.data.thread_id,
                    category_id: this.blockModal.form.category_id,
                    level: this.blockModal.form.level,
                    expires_at: this.blockModal.form.expires_at
                })
                .then(() => {
                    this.loadBlacklistEntries();
                    this.loadPosts();
                })
                .catch(this.showBackendError)
                .then(() => {
                    button.button('reset');
                });
        }
    },

    /**
     * Components used by application.
     */
    components: {
        'pagination': require('./components/Pagination.vue'),
        'thread-post': require('./components/ThreadPost.vue')
    }
});