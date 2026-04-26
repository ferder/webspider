define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            Table.api.init({
                extend: {
                    index_url: 'crawler/excel_template_field/index' + location.search,
                    add_url: 'crawler/excel_template_field/add',
                    edit_url: 'crawler/excel_template_field/edit',
                    del_url: 'crawler/excel_template_field/del',
                    multi_url: 'crawler/excel_template_field/multi',
                    table: 'fa_excel_template_fields'
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
                        {field: 'field_name', title: __('field_name')},
                        {field: 'field_key', title: __('field_key')},
                        {field: 'field_type', title: __('field_type')},
                        {field: 'is_required', title: __('is_required')},
                        {field: 'default_value', title: __('default_value')},
                        {field: 'field_order', title: __('field_order')},
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
