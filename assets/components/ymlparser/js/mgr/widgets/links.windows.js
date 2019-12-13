YMLParser.window.CreateItem = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'ymlparser-item-window-create';
    }
    Ext.applyIf(config, {
        title: _('ymlparser_item_create'),
        width: 550,
        autoHeight: true,
        url: YMLParser.config.connector_url,
        action: 'mgr/link/create',
        fields: this.getFields(config),
        keys: [{
            key: Ext.EventObject.ENTER, shift: true, fn: function () {
                this.submit()
            }, scope: this
        }]
    });
    YMLParser.window.CreateItem.superclass.constructor.call(this, config);
};
Ext.extend(YMLParser.window.CreateItem, MODx.Window, {

    getFields: function (config) {
        return [{
            xtype: 'textfield',
            fieldLabel: _('ymlparser_item_name'),
            name: 'name',
            id: config.id + '-name',
            anchor: '99%',
            allowBlank: false,
        }, {
            xtype: 'textfield',
            fieldLabel: _('ymlparser_item_link'),
            name: 'link',
            id: config.id + '-link',
            // height: 150,
            allowBlank: false,
            anchor: '99%',
            parentID: config.id,
            listeners: {
                change: YMLParser.utils.changeLink
            }
        }, {
            html: '<div id="tree-view"></div>',
        }, {
            xtype: 'xcheckbox',
            boxLabel: _('ymlparser_item_active'),
            name: 'active',
            id: config.id + '-active',
            checked: true,
        }];
    },

    loadDropZones: function () {
    }

});
Ext.reg('ymlparser-item-window-create', YMLParser.window.CreateItem);


YMLParser.window.UpdateItem = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'ymlparser-item-window-update';
    }
    Ext.applyIf(config, {
        title: _('ymlparser_item_update'),
        width: 550,
        autoHeight: true,
        url: YMLParser.config.connector_url,
        action: 'mgr/item/update',
        fields: this.getFields(config),
        keys: [{
            key: Ext.EventObject.ENTER, shift: true, fn: function () {
                this.submit()
            }, scope: this
        }]
    });
    YMLParser.window.UpdateItem.superclass.constructor.call(this, config);
};
Ext.extend(YMLParser.window.UpdateItem, MODx.Window, {

    getFields: function (config) {
        return [{
            xtype: 'hidden',
            name: 'id',
            id: config.id + '-id',
        }, {
            xtype: 'textfield',
            fieldLabel: _('ymlparser_item_name'),
            name: 'name',
            id: config.id + '-name',
            anchor: '99%',
            allowBlank: false,
        }, {
            xtype: 'textfield',
            fieldLabel: _('ymlparser_item_link'),
            name: 'link',
            id: config.id + '-link',
            // height: 150,
            anchor: '99%'
        }, {
            xtype: 'textfield',
            fieldLabel: _('ymlparser_item_parse_date'),
            name: 'parse_date',
            id: config.id + '-parse_date',
            // height: 150,
            disabled: true,
            anchor: '99%'
        }, {
            xtype: 'xcheckbox',
            boxLabel: _('ymlparser_item_active'),
            name: 'active',
            id: config.id + '-active',
        }];
    },

    loadDropZones: function () {
    }

});
Ext.reg('ymlparser-item-window-update', YMLParser.window.UpdateItem);