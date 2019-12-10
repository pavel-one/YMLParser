Ext.onReady(function () {
    YMLParser.config.connector_url = OfficeConfig.actionUrl;

    var grid = new YMLParser.panel.Home();
    grid.render('office-ymlparser-wrapper');

    var preloader = document.getElementById('office-preloader');
    if (preloader) {
        preloader.parentNode.removeChild(preloader);
    }
});