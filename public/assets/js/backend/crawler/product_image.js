define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            Table.api.init({
                extend: {
                    index_url: 'crawler/product_image/index' + location.search,
                    add_url: 'crawler/product_image/add',
                    edit_url: 'crawler/product_image/edit',
                    del_url: 'crawler/product_image/del',
                    multi_url: 'crawler/product_image/multi',
                    table: 'fa_product_images'
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
                        {field: 'variant_id', title: __('variant_id')},
                        {field: 'image_url', title: __('image_url')},
                        {field: 'image_position', title: __('image_position')},
                        {field: 'is_main', title: __('is_main')},
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
