define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {
    var Controller = {
        index: function () {
            Table.api.init({extend: {index_url: 'spider/exportjob/index', add_url: 'spider/exportjob/create'}});
            var table = $('#table');
            table.bootstrapTable({url: $.fn.bootstrapTable.defaults.extend.index_url, columns: [[
                {field: 'id', title: 'ID'}, {field: 'site_id', title: '站点'}, {field: 'crawl_task_id', title: '任务ID'},
                {field: 'filter_type', title: '筛选类型'}, {field: 'status', title: '状态'},
                {field: 'file_path', title: '下载链接', formatter: Table.api.formatter.url},
                {field: 'error_message', title: '错误信息'}, {field: 'createtime', title: '创建时间', formatter: Table.api.formatter.datetime}
            ]]});
            Table.api.bindevent(table);
        },
        create: function () { Form.api.bindevent($("form[role=form]")); }
    }; return Controller;
});
