<?php
set_time_limit(0);
ini_set('memory_limit', '-1');

class YMLParserLinkCheckProcessor extends modProcessor
{
    public $languageTopics = ['ymlparser'];

    /** @var YMLParser $YMLParser */
    public $YMLParser;

    public function initialize()
    {
        $this->YMLParser = $this->modx->getService('YMLParser', 'YMLParser', MODX_CORE_PATH . 'components/ymlparser/model/', []);

        return parent::initialize();
    }

    public function process()
    {
        $link = $this->getProperty('link');
        if (!$link) return $this->failure('Не задана ссылка');

        if (!preg_match('/^(http\w?:\/\/)(.*)\.(.*)/', $link)) {
            return $this->failure('Ссылка не соответствует паттерну');
        }

        $headers = get_headers($link);
        $code = explode(' ', $headers[0])[1];
        if ($code != 200) return $this->failure('Код ответа не равен 200');

        $mime = explode(';', $headers[3])[0];
        if ($mime != 'Content-Type: text/xml') return $this->failure('Это не xml документ');

        $xml = file_get_contents($link);

        $data = $this->YMLParser->prepareXML($xml);


        return $this->success('Успешно', $data);
    }

}

return 'YMLParserLinkCheckProcessor';