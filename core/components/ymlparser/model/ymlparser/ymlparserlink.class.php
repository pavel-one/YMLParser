<?php

class YMLParserLink extends xPDOSimpleObject
{
    /**
     * @return bool|array
     */
    public function getTree()
    {
        /** @var YMLParser $YMLParser */
        $YMLParser = $this->xpdo->getService('ymlparser');
        $link = $this->get('link');

        if (!$link) {
            return false;
        }

        $xml = file_get_contents($link);
        if (!$xml) {
            return false;
        }

        return $YMLParser->prepareXML($xml)['tree'];
    }
}