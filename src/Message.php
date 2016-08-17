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
    public function __construct($data)
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
        $str = '';

        foreach ($this->data as $k => $v) {
            $str .= sprintf('%s=%s&', $k, rawurlencode($v));
        }

        return rtrim($str, '&');
    }

    /**
     * @param string $rawPostDataString
     *
     * @return array
     */
    private function extractDataFromRawPostDataString($rawPostDataString)
    {
        $data = array();
        $keyValuePairs = preg_split('/&/', $rawPostDataString, null, PREG_SPLIT_NO_EMPTY);

        foreach ($keyValuePairs as $keyValuePair) {
            list($k, $v) = explode('=', $keyValuePair);

            $data[$k] = rawurldecode($v);
        }

        return $data;
    }
}
