define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            Table.api.init({
                extend: {
                    index_url: 'crawler/export_job/index' + location.search,
                    add_url: 'crawler/export_job/add',
                    edit_url: 'crawler/export_job/edit',
                    del_url: 'crawler/export_job/del',
                    multi_url: 'crawler/export_job/multi',
                    table: 'fa_export_jobs'
                }
            });

            var table = $("#table");
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('id')},
                        {field: 'job_no', title: __('job_no')},
                        {field: 'site_id', title: __('site_id')},
                        {field: 'task_id', title: __('task_id')},
                        {field: 'export_type', title: __('export_type')},
                        {field: 'status', title: __('status')},
                        {field: 'total_products', title: __('total_products')},
                        {field: 'file_path', title: __('file_path')},
                        {field: 'createtime', title: __('createtime')},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            Table.api.bindevent(table);
        },
        add: function () {
            Form.api.bindevent($("form[role=form]"));
        },
        edit: function () {
            Form.api.bindevent($("form[role=form]"));
        }
    };
    return Controller;
});
