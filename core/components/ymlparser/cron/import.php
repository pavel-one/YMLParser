<?php
set_time_limit(0);
ini_set('memory_limit', '-1');


define('MODX_API_MODE', true);
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/index.php';

$modx->getService('error','error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');

/** @var YMLParser $YMLParser */
$YMLParser = $modx->getService('YMLParser', 'YMLParser', MODX_CORE_PATH . 'components/ymlparser/model/', []);

$tasks = $modx->getCollection('YMLParserLink', [
    'active' => 1,
]);

/** @var YMLParserLink $task */
foreach ($tasks as $task) {
    $repeat = $task->get('repeat');
    $parentID = $task->get('parent_id') ?: 0;
    $YMLParser->importTree($task, $parentID);

    if (!$repeat) {
        $task->set('active', null);
    }

    $task->set('parse_date', date('d.m.Y G:i'));
    $task->save();
}