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

    /**
     * @param string $key
     *
     * @return string
     */
    public function get($key)
    {
        $value = '';

        if (isset($this->data[$key])) {
            $value = $this->data[$key];
        }

        return $value;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return http_build_query($this->getAll(), null, '&');
    }

    /**
     * @param string $rawPostDataString
     *
     * @return array
     */
    private function extractDataFromRawPostDataString($rawPostDataString)
    {
        parse_str($rawPostDataString, $data);

        return $data;
    }
}
