YMLParser.panel.Home = function (config) {
    config = config || {};
    Ext.apply(config, {
        baseCls: 'modx-formpanel',
        layout: 'anchor',
        /*
         stateful: true,
         stateId: 'ymlparser-panel-home',
         stateEvents: ['tabchange'],
         getState:function() {return {activeTab:this.items.indexOf(this.getActiveTab())};},
         */
        hideMode: 'offsets',
        items: [{
            xtype: 'modx-tabs',
            defaults: {border: false, autoHeight: true},
            border: false,
            hideMode: 'offsets',
            items: [{
                title: _('ymlparser_items'),
                layout: 'anchor',
                items: [{
                    html: _('ymlparser_intro_msg'),
                    cls: 'panel-desc',
                }, {
                    xtype: 'ymlparser-grid-items',
                    cls: 'main-wrapper',
                }]
            }]
        }]
    });
    YMLParser.panel.Home.superclass.constructor.call(this, config);
};
Ext.extend(YMLParser.panel.Home, MODx.Panel);
Ext.reg('ymlparser-panel-home', YMLParser.panel.Home);
