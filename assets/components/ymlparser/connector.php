<?php
if (file_exists(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php')) {
    /** @noinspection PhpIncludeInspection */
    require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
} else {
    require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/config.core.php';
}
/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CONNECTORS_PATH . 'index.php';
/** @var YMLParser $YMLParser */
$YMLParser = $modx->getService('YMLParser', 'YMLParser', MODX_CORE_PATH . 'components/ymlparser/model/');
$modx->lexicon->load('ymlparser:default');

// handle request
$corePath = $modx->getOption('ymlparser_core_path', null, $modx->getOption('core_path') . 'components/ymlparser/');
$path = $modx->getOption('processorsPath', $YMLParser->config, $corePath . 'processors/');
$modx->getRequest();

/** @var modConnectorRequest $request */
$request = $modx->request;
$request->handleRequest([
    'processors_path' => $path,
    'location' => '',
]);