YMLParser.combo.Search = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        xtype: 'twintrigger',
        ctCls: 'x-field-search',
        allowBlank: true,
        msgTarget: 'under',
        emptyText: _('search'),
        name: 'query',
        triggerAction: 'all',
        clearBtnCls: 'x-field-search-clear',
        searchBtnCls: 'x-field-search-go',
        onTrigger1Click: this._triggerSearch,
        onTrigger2Click: this._triggerClear,
    });
    YMLParser.combo.Search.superclass.constructor.call(this, config);
    this.on('render', function () {
        this.getEl().addKeyListener(Ext.EventObject.ENTER, function () {
            this._triggerSearch();
        }, this);
    });
    this.addEvents('clear', 'search');
};
Ext.extend(YMLParser.combo.Search, Ext.form.TwinTriggerField, {

    initComponent: function () {
        Ext.form.TwinTriggerField.superclass.initComponent.call(this);
        this.triggerConfig = {
            tag: 'span',
            cls: 'x-field-search-btns',
            cn: [
                {tag: 'div', cls: 'x-form-trigger ' + this.searchBtnCls},
                {tag: 'div', cls: 'x-form-trigger ' + this.clearBtnCls}
            ]
        };
    },

    _triggerSearch: function () {
        this.fireEvent('search', this);
    },

    _triggerClear: function () {
        this.fireEvent('clear', this);
    },

});
Ext.reg('ymlparser-combo-search', YMLParser.combo.Search);
Ext.reg('ymlparser-field-search', YMLParser.combo.Search);

YMLParser.combo.Cat = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        name: config.name || 'parent_id',
        fieldLabel: config.fieldLabel || 'Категория',
        hiddenName: config.name || 'parent_id',
        displayField: 'pagetitle',
        valueField: 'id',
        anchor: '99%',
        fields: ['id', 'pagetitle'],
        pageSize: 20,
        typeAhead: false,
        editable: true,
        allowBlank: false,
        url: YMLParser.config['connector_url'],
        baseParams: {
            action: 'mgr/misc/category/getlist',
            combo: true,
        },
        tpl: new Ext.XTemplate('\
            <tpl for=".">\
                <div class="x-combo-list-item">\
                    <span>\
                        <small>({id})</small>\
                        <b>{pagetitle}</b>\
                    </span>\
                </div>\
            </tpl>',
            {compiled: true}
        ),
    });
    YMLParser.combo.Cat.superclass.constructor.call(this, config);
};
Ext.extend(YMLParser.combo.Cat, MODx.combo.ComboBox);
Ext.reg('ymlparser-combo-cat', YMLParser.combo.Cat);