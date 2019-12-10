<?php

class YMLParserItemCreateProcessor extends modObjectCreateProcessor
{
    public $objectType = 'YMLParserItem';
    public $classKey = 'YMLParserItem';
    public $languageTopics = ['ymlparser'];
    //public $permission = 'create';


    /**
     * @return bool
     */
    public function beforeSet()
    {
        $name = trim($this->getProperty('name'));
        if (empty($name)) {
            $this->modx->error->addField('name', $this->modx->lexicon('ymlparser_item_err_name'));
        } elseif ($this->modx->getCount($this->classKey, ['name' => $name])) {
            $this->modx->error->addField('name', $this->modx->lexicon('ymlparser_item_err_ae'));
        }

        return parent::beforeSet();
    }

}

return 'YMLParserItemCreateProcessor';