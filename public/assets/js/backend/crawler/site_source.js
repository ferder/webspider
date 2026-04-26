define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            Table.api.init({
                extend: {
                    index_url: 'crawler/site_source/index' + location.search,
                    add_url: 'crawler/site_source/add',
                    edit_url: 'crawler/site_source/edit',
                    del_url: 'crawler/site_source/del',
                    multi_url: 'crawler/site_source/multi',
                    table: 'fa_site_sources'
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
                        {field: 'site_name', title: __('site_name')},
                        {field: 'base_url', title: __('base_url')},
                        {field: 'domain', title: __('domain')},
                        {field: 'country', title: __('country')},
                        {field: 'language', title: __('language')},
                        {field: 'currency', title: __('currency')},
                        {field: 'platform_type', title: __('platform_type'), searchList: {'unknown':'unknown','shopify':'shopify','woocommerce':'woocommerce','magento':'magento','prestashop':'prestashop','shopware':'shopware','custom':'custom'}},
                        {field: 'status', title: __('status'), searchList: {'pending_detect':'pending_detect','detecting':'detecting','detected':'detected','ready':'ready','manual_required':'manual_required','disabled':'disabled'}},
                        {field: 'detect_score', title: __('detect_score')},
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
        },
        batchimport: function () {
            Form.api.bindevent($("form[role=form]"));
        }
    };
    return Controller;
});
