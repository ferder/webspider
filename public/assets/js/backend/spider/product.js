define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {
    var Controller = {
        index: function () {
            Table.api.init({
                extend: {
                    index_url: 'spider/product/index',
                    detail_url: 'spider/product/detail',
                }
            });
            var table = $('#table');
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [[
                    {field: 'id', title: 'ID'},
                    {field: 'title', title: '商品标题', operate: 'LIKE'},
                    {field: 'site_id', title: '来源站点'},
                    {field: 'source_url', title: '来源URL', operate: 'LIKE'},
                    {field: 'price', title: '价格'},
                    {field: 'compare_price', title: '原价'},
                    {field: 'sku', title: 'SKU'},
                    {field: 'stock', title: '库存'},
                    {field: 'image_count', title: '图片数量'},
                    {field: 'variant_count', title: '规格数量'},
                    {field: 'status', title: '状态'},
                    {field: 'last_crawled_at', title: '最后采集时间', formatter: Table.api.formatter.datetime},
                    {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, buttons: [{name: 'detail', text: '详情', classname: 'btn btn-xs btn-info btn-dialog', icon: 'fa fa-list', url: 'spider/product/detail'}], formatter: Table.api.formatter.operate}
                ]]
            });
            Table.api.bindevent(table);
        }
    };
    return Controller;
});
