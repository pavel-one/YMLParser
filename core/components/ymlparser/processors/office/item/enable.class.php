<?php

class YMLParserOfficeItemEnableProcessor extends modObjectProcessor
{
    public $objectType = 'YMLParserItem';
    public $classKey = 'YMLParserItem';
    public $languageTopics = ['ymlparser'];
    //public $permission = 'save';


    /**
     * @return array|string
     */
    public function process()
    {
        if (!$this->checkPermissions()) {
            return $this->failure($this->modx->lexicon('access_denied'));
        }

        $ids = $this->modx->fromJSON($this->getProperty('ids'));
        if (empty($ids)) {
            return $this->failure($this->modx->lexicon('ymlparser_item_err_ns'));
        }

        foreach ($ids as $id) {
            /** @var YMLParserItem $object */
            if (!$object = $this->modx->getObject($this->classKey, $id)) {
                return $this->failure($this->modx->lexicon('ymlparser_item_err_nf'));
            }

            $object->set('active', true);
            $object->save();
        }

        return $this->success();
    }

}

return 'YMLParserOfficeItemEnableProcessor';
