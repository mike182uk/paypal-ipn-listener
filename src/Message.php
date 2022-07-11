<?php

namespace Mdb\PayPal\Ipn;

class Message
{
    /**
     * @var array
     */
    private $data;

    /**
     * @param array|string $data
     */
    public function __construct($data = [])
    {
        if (!is_array($data)) {
            $data = $this->extractDataFromRawPostDataString($data);
        }

        $this->data = $data;
    }

    public function get(string $key) : string
    {
        $value = '';

        if (isset($this->data[$key])) {
            $value = $this->data[$key];
        }

        return $value;
    }

    public function getAll() : array
    {
        return $this->data;
    }

    public function __toString() : string
    {
        return http_build_query($this->getAll(), '', '&');
    }

    private function extractDataFromRawPostDataString($rawPostDataString) : array
    {
        parse_str($rawPostDataString, $data);

        return $data;
    }
}
