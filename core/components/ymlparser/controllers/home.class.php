<?php

/**
 * The home manager controller for YMLParser.
 *
 */
class YMLParserHomeManagerController extends modExtraManagerController
{
    /** @var YMLParser $YMLParser */
    public $YMLParser;


    /**
     *
     */
    public function initialize()
    {
        $this->YMLParser = $this->modx->getService('YMLParser', 'YMLParser', MODX_CORE_PATH . 'components/ymlparser/model/');
        parent::initialize();
    }


    /**
     * @return array
     */
    public function getLanguageTopics()
    {
        return ['ymlparser:default'];
    }


    /**
     * @return bool
     */
    public function checkPermissions()
    {
        return true;
    }


    /**
     * @return null|string
     */
    public function getPageTitle()
    {
        return $this->modx->lexicon('ymlparser');
    }


    /**
     * @return void
     */
    public function loadCustomCssJs()
    {
        $this->addCss($this->YMLParser->config['cssUrl'] . 'mgr/main.css');
        $this->addJavascript($this->YMLParser->config['jsUrl'] . 'mgr/ymlparser.js');
        $this->addJavascript($this->YMLParser->config['jsUrl'] . 'mgr/misc/utils.js');
        $this->addJavascript($this->YMLParser->config['jsUrl'] . 'mgr/misc/combo.js');
        $this->addJavascript($this->YMLParser->config['jsUrl'] . 'mgr/widgets/links.grid.js');
        $this->addJavascript($this->YMLParser->config['jsUrl'] . 'mgr/widgets/links.windows.js');
        $this->addJavascript($this->YMLParser->config['jsUrl'] . 'mgr/widgets/home.panel.js');
        $this->addJavascript($this->YMLParser->config['jsUrl'] . 'mgr/sections/home.js');

        $this->addHtml('<script type="text/javascript">
        YMLParser.config = ' . json_encode($this->YMLParser->config) . ';
        YMLParser.config.connector_url = "' . $this->YMLParser->config['connectorUrl'] . '";
        Ext.onReady(function() {MODx.load({ xtype: "ymlparser-page-home"});});
        </script>');
    }


    /**
     * @return string
     */
    public function getTemplateFile()
    {
        $this->content .= '<div id="ymlparser-panel-home-div"></div>';

        return '';
    }
}