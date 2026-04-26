define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            Table.api.init({
                extend: {
                    index_url: 'crawler/site_rule/index' + location.search,
                    add_url: 'crawler/site_rule/add',
                    edit_url: 'crawler/site_rule/edit',
                    del_url: 'crawler/site_rule/del',
                    multi_url: 'crawler/site_rule/multi',
                    table: 'fa_site_rules'
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
                        {field: 'site_id', title: __('site_id')},
                        {field: 'rule_name', title: __('rule_name')},
                        {field: 'rule_type', title: __('rule_type')},
                        {field: 'status', title: __('status')},
                        {field: 'need_js_render', title: __('need_js_render')},
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
