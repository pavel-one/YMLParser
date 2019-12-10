YMLParser.page.Home = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        components: [{
            xtype: 'ymlparser-panel-home',
            renderTo: 'ymlparser-panel-home-div'
        }]
    });
    YMLParser.page.Home.superclass.constructor.call(this, config);
};
Ext.extend(YMLParser.page.Home, MODx.Component);
Ext.reg('ymlparser-page-home', YMLParser.page.Home);