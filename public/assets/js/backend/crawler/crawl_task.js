define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            Table.api.init({
                extend: {
                    index_url: 'crawler/crawl_task/index' + location.search,
                    add_url: 'crawler/crawl_task/add',
                    edit_url: 'crawler/crawl_task/edit',
                    del_url: 'crawler/crawl_task/del',
                    multi_url: 'crawler/crawl_task/multi',
                    table: 'fa_crawl_tasks'
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
                        {field: 'task_no', title: __('task_no')},
                        {field: 'site_id', title: __('site_id')},
                        {field: 'rule_id', title: __('rule_id')},
                        {field: 'task_type', title: __('task_type')},
                        {field: 'status', title: __('status')},
                        {field: 'success_count', title: __('success_count')},
                        {field: 'fail_count', title: __('fail_count')},
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
