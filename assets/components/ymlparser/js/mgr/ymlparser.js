var YMLParser = function (config) {
    config = config || {};
    YMLParser.superclass.constructor.call(this, config);
};
Ext.extend(YMLParser, Ext.Component, {
    page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, view: {}, utils: {}
});
Ext.reg('ymlparser', YMLParser);

YMLParser = new YMLParser();