<?php

class YMLParserLinkCheckProcessor extends modProcessor
{
    public $languageTopics = ['ymlparser'];
    /** @var XMLReader $reader */
    public $reader;
    /** @var XMLReader $xml */
    public $xml;

    public function initialize()
    {
        $this->reader = new XMLReader();
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

        if (!$this->xml = $this->reader->open($link)) return $this->failure('Ошибка чтения xml');



        return $this->success('Успешно');
    }

}

return 'YMLParserLinkCheckProcessor';