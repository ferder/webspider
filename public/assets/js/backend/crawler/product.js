define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            Table.api.init({
                extend: {
                    index_url: 'crawler/product/index' + location.search,
                    add_url: 'crawler/product/add',
                    edit_url: 'crawler/product/edit',
                    del_url: 'crawler/product/del',
                    multi_url: 'crawler/product/multi',
                    table: 'fa_products'
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
                        {field: 'title', title: __('title')},
                        {field: 'source_url', title: __('source_url')},
                        {field: 'price', title: __('price')},
                        {field: 'compare_price', title: __('compare_price')},
                        {field: 'currency', title: __('currency')},
                        {field: 'sku', title: __('sku')},
                        {field: 'status', title: __('status')},
                        {field: 'last_crawled_at', title: __('last_crawled_at')},
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
