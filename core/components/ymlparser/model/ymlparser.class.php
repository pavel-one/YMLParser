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
        $offers = $this->document->find('offers offer');

        $products = $this->createProductsArray($offers);
        $tree = $this->createTree($categories, $products['products_parents']);

        $out = [
            'tree' => $tree,
            'products' => $products
        ];

        return $out;

    }

    public function createProductsArray(array $productsDOM)
    {
        $out = [];


        /** @var \DiDom\Element $item */
        foreach ($productsDOM as $item) {

            $name = $item->first('name')
                ? $item->first('name')->text()
                : $item->first('model')->text();
            $parentID = $item->first('categoryId')
                ? $item->first('categoryId')->text()
                : 0;

            $product = [
                'id' => $item->attr('id'),
                'text' => $name,
                'url' => $item->first('url') ? $item->first('url')->text() : '',
                'vendor' => $item->first('vendor') ? $item->first('vendor')->text() : '',
                'price' => $item->first('price') ? $item->first('price')->text() : '',
                'icon' => 'icon icon-shopping-cart',
            ];

            $out['products_parents'][$parentID][] = $product;
            $out['products_list'][] = $product;
        }

        return $out;
    }

    public function createTree(array $categoriesDOM, array $products)
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

        $firstKey = array_key_first($parentsArray);
        $tree = $this->_prepareTree($parentsArray, $parentsArray[$firstKey], $products);


//        $this->modx->log(1, print_r($tree, 1));

        return $tree;
    }

    private function _prepareTree(&$list, $parent, $products)
    {
        $tree = [];

        foreach ($parent as $item) {
            if (isset($list[$item['id']])) {
                $item['children'] = $this->_prepareTree($list, $list[$item['id']], $products);
            }
            if (isset($products[$item['id']])) {
                if (isset($item['children'])) {
                    $item['children'] = array_merge($item['children'], $products[$item['id']]);
                } else {
                    $item['children'] = $products[$item['id']];
                }
            }
            $tree[] = $item;
        }

        return $tree;
    }

}