define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            Table.api.init({
                extend: {
                    index_url: 'crawler/product_snapshot/index' + location.search,
                    add_url: 'crawler/product_snapshot/add',
                    edit_url: 'crawler/product_snapshot/edit',
                    del_url: 'crawler/product_snapshot/del',
                    multi_url: 'crawler/product_snapshot/multi',
                    table: 'fa_product_snapshots'
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
                        {field: 'site_id', title: __('site_id')},
                        {field: 'source_url', title: __('source_url')},
                        {field: 'title', title: __('title')},
                        {field: 'price', title: __('price')},
                        {field: 'sku', title: __('sku')},
                        {field: 'data_hash', title: __('data_hash')},
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
