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

    /**
     * @param string $xml
     * @return array
     */
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
                'content' => $item->first('description') ? $item->first('description')->html() : '',
                'article' => $item->first('barcode') ? $item->first('barcode')->text() : '',
                'weight' => $item->first('weight') ? $item->first('weight')->text() : '',
                'icon' => 'icon icon-shopping-cart',
                'options' => []
            ];

            $options = $item->find('param');
            if (count($options)) {
                foreach ($options as $option) {
                    if ($t_name = $option->attr('name')) {
                        $product['options'][] = [
                            'name' => $t_name,
                            'unit' => $option->attr('unit'),
                            'value' => $option->text()
                        ];
                    }
                }
            }

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
                'isCategory' => true,
                'text' => $item->text(),
            ];
        }

        $parentsArray = []; //Временный массив рассортированных по родителям массивов
        /** @var array $item */
        foreach ($categoriesArray as $item) {
            $parentsArray[$item['parentId']][] = $item;
        }

        reset($parentsArray);

        $firstKey = key($parentsArray);
        $tree = $this->_prepareTree($parentsArray, $parentsArray[$firstKey], $products);


//        $this->modx->log(1, print_r($tree, 1));

        return $tree;
    }

    protected function _prepareTree(&$list, $parent, $products)
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


    /**
     * @param YMLParserLink $link
     * @param int $parentID
     * @return bool
     */
    public function importTree(YMLParserLink $link, int $parentID)
    {
        $tree = $link->getTree();
        if (!$tree) {
            return false;
        }

        $this->importTask($tree, $parentID);

    }

    /**
     * @param array $tree
     * @param int $parentID
     * @return bool
     */
    public function importTask(array $tree, int $parentID)
    {
        if (!$tree) {
            return false;
        }
        foreach ($tree as $item) {
            if (isset($item['isCategory'])) {

                /** @var msCategory $find */
                $find = $this->modx->getObject('msCategory', [
                    'pagetitle' => $item['text'],
                    'parent' => $parentID
                ]);

                if (!$find) {
                    $new = $this->_createCategory($item, $parentID);
                    if (!$new) {
                        continue;
                    }
                    $newParent = $new->get('id');
                } else {
                    $newParent = $find->get('id');
                }

                if (isset($item['children'])) {
                    $this->importTask($item['children'], $newParent);
                }
            } else {
                $created = $this->_createProduct($item, $parentID);

                if (is_array($created)) {
                    $this->modx->log(1, "Ошибка парсинга: \n" . print_r($created, true));
                    continue;
                }

                $productID = $created->get('id');
            }


        }
    }


    /**
     * @param array $product
     * @param int $parentID
     * @return array|msProduct
     */
    protected function _createProduct(array $product, int $parentID)
    {
        $this->modx->log(1, print_r($product, 1));
        /**
         * TODO: Options
         * TODO: Images
         */


        $data = [
            'class_key' => 'msProduct',
            'pagetitle' => $product['text'],
            'parent' => $parentID,
            'template' => $this->modx->getOption('ms2_template_product_default', [], 0),
            'show_in_tree' => $this->modx->getOption('ms2_product_show_in_tree_default', [], 0),
            'published' => 1,

            //Данные
            'price' => $product['price'],
            'old_price' => 0,
            'favorite' => 0,
            'popular' => 0,

            //стандартные опции товара
//            'color' => array('Синий', 'Красный'),
//            'size' => array('S', 'M'),
//            'tags' => array('Тег1', 'Тег2'),

            //свои опции созданные в настройках
//            'options-КЛЮЧ_ОПЦИИ' => array('значение1', 'значение2'),

            //TV - 10 это id TV
//            'tv10' => 'Значение'
        ];

        if ($product['vendor']) {
            $data['vendor'] = $this->_createVendor($product['vendor']);
        }

        $action = 'create';

        /** @var msProduct|null $find */
        $find = $this->modx->getObject('msProduct', [
            'pagetitle' => $product['text'],
            'parent' => $parentID,
        ]);

        if ($find) {
            $action = 'update';
            $data['id'] = $find->get('id');
            $data['context_key'] = 'web';
        }

        /** @var modProcessorResponse $response */
        $response = $this->modx->runProcessor('resource/' . $action, $data);

        if ($response->isError()) {
            return $response->getResponse();
        }

        /** @var msProduct $obj */
        $obj = $this->modx->getObject('msProduct', $response->response['object']['id']);
        return $obj;
    }

    /**
     * @param string $vendorName
     * @return bool|int
     */
    public function _createVendor(string $vendorName)
    {
        /** @var msVendor $find */
        $find = $this->modx->getObject('msVendor', [
            'name' => $vendorName
        ]);

        if ($find) {
            return $find->id;
        }
        /** @var msVendor $new */
        $new = $this->modx->newObject('msVendor');
        $new->set('name', $vendorName);
        if ($new->save()) {
            return $new->id;
        }
        return false;
    }

    /**
     * @param array $category
     * @param integer $parentID
     * @return bool|msCategory
     */
    protected function _createCategory(array $category, $parentID)
    {
        /** @var msCategory $new */
        $new = $this->modx->newObject('msCategory');
        $new->fromArray([
            'pagetitle' => $category['text'],
            'parent' => $parentID,
            'class_key' => 'msCategory',
            'published' => 1,
            'alias' => '',
            'template' => $this->modx->getOption('ms2_template_category_default', [], 0),
        ]);
        if (!$new->save()) {
            return false;
        };
        return $new;
    }

}