'use strict';

import 'blockui';
import axios from 'axios';
import 'select2';

$(function () {
    let table = $('#threads-datatable');

    if (table.length) {
        table.dataTable({
            processing: true,
            serverSide: true,
            ajax: $(table).data('route'),
            responsive: true,
            columns: [
                {name: 'id', data: 'id', orderable: true, searchable: true, className: 'text-center'},
                {name: 'title', data: 'title', orderable: true, searchable: true},
                {name: 'user.first_name', data: 'user', orderable: true, searchable: true},
                {name: 'replies', data: 'replies', orderable: true, searchable: true},
                {name: 'views', data: 'views', orderable: true, searchable: true},
                {name: 'is_locked', data: 'is_locked', orderable: true, searchable: true, className: 'text-center'},
                {name: 'is_pinned', data: 'is_pinned', orderable: true, searchable: true, className: 'text-center'},
                {name: 'created_at', data: 'created_at', orderable: true, searchable: true},
                {name: 'deleted_at', data: 'deleted_at', orderable: true, searchable: true},
                {name: 'actions', data: 'actions', orderable: false, searchable: false, className: 'text-center'}
            ],
            order: [[0, 'desc']]
        });

        table.parent().parent().find('input[type=search]').attr('placeholder', 'Find thread..');
        table.parent().parent().find('.table-caption').html('Forum threads');

        // Toggleable items
        table.on('click', '.toggle-thread', (e) => {
            let wrapper = table.closest('.dataTables_wrapper'),
                id = $(e.target).data('thread-id'),
                attribute = $(e.target).data('attribute');

            wrapper.block({
                message: 'Please wait..',
                css: {
                    border: 'none',
                    color: '#fff',
                    background: 'rgba(0, 0, 0, 0.5)',
                    padding: '10px'
                }
            });

            let url = '/admin/forum/management/threads/:id:/toggle-state'.replace(':id:', id);

            axios.post(url, {attribute}).then(() => {
                $.growl.success({message: 'State changed!'});
            }).catch(() => {
                $.growl.error({message: 'Whoops.. Something went wrong!'});
            }).then(() => {
                wrapper.unblock();
                table.fnDraw();
            });
        });
    }

    // Category select
    $('.select2-categories').select2().on('change', (e) => {
        $(e.target).closest('form').submit();
    });

});