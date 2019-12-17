<?php

class YmlParserCatGetListProcessor extends modObjectGetListProcessor
{
    public $objectType = 'msCategory';
    public $classKey = 'msCategory';
    public $defaultSortField = 'pagetitle';
    public $defaultSortDirection = 'DESC';


    /**
     * @param xPDOQuery $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $query = trim($this->getProperty('query'));
        $c->select('id,pagetitle');
        if ($query) {
            $c->where([
                'pagetitle:LIKE' => "%{$query}%"
            ]);
        }

        return $c;
    }

}

return 'YmlParserCatGetListProcessor';