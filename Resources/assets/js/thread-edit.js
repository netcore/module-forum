'use strict';

new Vue({
    el: '#thread-app',
    components: {
        'thread-post': require('./components/ThreadPost.vue')
    }
});

$(() => {
    $('a.editable').editable({
        style: 'inline'
    });
});