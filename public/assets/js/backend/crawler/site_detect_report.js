define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            Table.api.init({
                extend: {
                    index_url: 'crawler/site_detect_report/index' + location.search,
                    add_url: 'crawler/site_detect_report/add',
                    edit_url: 'crawler/site_detect_report/edit',
                    del_url: 'crawler/site_detect_report/del',
                    multi_url: 'crawler/site_detect_report/multi',
                    table: 'fa_site_detect_reports'
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
                        {field: 'platform_guess', title: __('platform_guess')},
                        {field: 'detect_status', title: __('detect_status')},
                        {field: 'detect_score', title: __('detect_score')},
                        {field: 'anti_crawl_level', title: __('anti_crawl_level')},
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
