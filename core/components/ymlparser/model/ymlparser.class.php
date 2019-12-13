<?php
require __DIR__ . '/vendor/autoload.php';

use DiDom\Document;

class YMLParser
{
    /** @var modX $modx */
    public $modx;

    /** @var Document $document */
    public $document;

    /**
     * @param modX $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config = [])
    {
        $this->modx =& $modx;
        $this->document = new Document();
        $corePath = MODX_CORE_PATH . 'components/ymlparser/';
        $assetsUrl = MODX_ASSETS_URL . 'components/ymlparser/';

        $this->config = array_merge([
            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'processorsPath' => $corePath . 'processors/',

            'connectorUrl' => $assetsUrl . 'connector.php',
            'assetsUrl' => $assetsUrl,
            'cssUrl' => $assetsUrl . 'css/',
            'jsUrl' => $assetsUrl . 'js/',
        ], $config);

        $this->modx->addPackage('ymlparser', $this->config['modelPath']);
        $this->modx->lexicon->load('ymlparser:default');
    }

    public function prepareXML($xml)
    {
        $this->document->loadXml($xml);
        $categories = $this->document->find('categories category');

        $tree = $this->createTree($categories);

        $out = [
            'tree' => $tree,
        ];

        return $out;

    }

    public function createTree(array $categoriesDOM)
    {
        $categoriesArray = [];
        /** @var \DiDom\Element $item */
        foreach ($categoriesDOM as $item) {
            $categoriesArray[] = [
                'id' => $item->attr('id'),
                'parentId' => $item->attr('parentId') ?: 0,
                'text' => $item->text(),
            ];
        }

        $parentsArray = []; //Временный массив рассортированных по родителям массивов
        /** @var array $item */
        foreach ($categoriesArray as $item) {
            $parentsArray[$item['parentId']][] = $item;
        }

        $tree = $this->_prepareTree($parentsArray, $parentsArray[0]);

        $this->modx->log(1, print_r($tree, 1));

        return $tree;
    }

    private function _prepareTree(&$list, $parent)
    {
        $tree = [];
        foreach ($parent as $item) {
            if(isset($list[$item['id']])){
                $item['children'] = $this->_prepareTree($list, $list[$item['id']]);
            }
            $tree[] = $item;
        }

        return $tree;
    }

}