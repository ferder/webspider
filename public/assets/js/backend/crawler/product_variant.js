define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            Table.api.init({
                extend: {
                    index_url: 'crawler/product_variant/index' + location.search,
                    add_url: 'crawler/product_variant/add',
                    edit_url: 'crawler/product_variant/edit',
                    del_url: 'crawler/product_variant/del',
                    multi_url: 'crawler/product_variant/multi',
                    table: 'fa_product_variants'
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
                        {field: 'product_id', title: __('product_id')},
                        {field: 'option1_value', title: __('option1_value')},
                        {field: 'option2_value', title: __('option2_value')},
                        {field: 'option3_value', title: __('option3_value')},
                        {field: 'price', title: __('price')},
                        {field: 'sku', title: __('sku')},
                        {field: 'stock', title: __('stock')},
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
