define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            Table.api.init({
                extend: {
                    index_url: 'crawler/crawl_task_log/index' + location.search,
                    add_url: 'crawler/crawl_task_log/add',
                    edit_url: 'crawler/crawl_task_log/edit',
                    del_url: 'crawler/crawl_task_log/del',
                    multi_url: 'crawler/crawl_task_log/multi',
                    table: 'fa_crawl_task_logs'
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
                        {field: 'task_id', title: __('task_id')},
                        {field: 'site_id', title: __('site_id')},
                        {field: 'log_level', title: __('log_level')},
                        {field: 'stage', title: __('stage')},
                        {field: 'message', title: __('message')},
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
