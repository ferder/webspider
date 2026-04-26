define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {
    var Controller = {
        index: function () {
            Table.api.init({extend: {index_url: 'spider/crawltask/index', add_url: 'spider/crawltask/createFullCrawl'}});
            var table = $('#table');
            table.bootstrapTable({url: $.fn.bootstrapTable.defaults.extend.index_url, columns: [[
                {field: 'id', title: 'ID'}, {field: 'task_type', title: '任务类型'}, {field: 'site_id', title: '站点'},
                {field: 'rule_id', title: '规则'}, {field: 'total_products', title: '商品数'},
                {field: 'success_count', title: '成功'}, {field: 'fail_count', title: '失败'},
                {field: 'new_count', title: '新增'}, {field: 'update_count', title: '更新'},
                {field: 'status', title: '状态'}, {field: 'createtime', title: '创建时间', formatter: Table.api.formatter.datetime}
            ]]});
            Table.api.bindevent(table);
        },
        createfullcrawl: function () { Form.api.bindevent($("form[role=form]")); }
    }; return Controller;
});
