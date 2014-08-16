<?php

namespace Mdb\PayPal\Ipn;

class Message
{
    /**
     * @var array
     */
    private $data;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
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
     * @return string
     */
    public function __toString()
    {
        $str = '';

        foreach ($this->data as $k => $v) {
            $str .= sprintf('%s=%s&', $k, urlencode($v));
        }

        return rtrim($str, '&');
    }
}
