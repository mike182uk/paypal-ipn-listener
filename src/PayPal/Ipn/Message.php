<?php

namespace PayPal\Ipn;

use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;

class Message implements ArrayAccess, IteratorAggregate
{
    /**
     * IPN message data.
     *
     * @var array
     */
    protected $data;

    /**
     * Create a new instance of the IPN message.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Create a new instance of the IPN message using globals for the data.
     *
     * @return Message
     */
    public static function createFromGlobals()
    {
        // reading posted data directly from $_POST causes serialization
        // issues with array data in POST, read raw POST data from input stream instead
        $rawPost = static::getRawPost();
        $rawPostArray = explode('&', $rawPost);
        $data = array();

        foreach ($rawPostArray as $keyValuePair) {
            $keyValuePair = explode ('=', $keyValuePair);
            if (count($keyValuePair) == 2) {
                $data[$keyValuePair[0]] = urldecode($keyValuePair[1]);
            }
        }

        return new static($data);
    }

    /**
     * Get the raw post data.
     *
     * @return string
     */
    protected static function getRawPost()
    {
        return file_get_contents('php://input');
    }

    /**
     * Determine if a given offset exists.
     *
     * @param  string  $key
     * @return boolean
     */
    public function offsetExists($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * Get the value at a given offset.
     *
     * @param  string $key
     * @return string
     */
    public function offsetGet($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    /**
     * Set the value at a given offset.
     *
     * @param  string $key
     * @param  string $value
     * @return string
     */
    public function offsetSet($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Unset the value at a given offset.
     *
     * @param string $key
     */
    public function offsetUnset($key)
    {
        unset($this->data[$key]);
    }

    /**
     * Get iterator for IteratorAggregate interface.
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->data);
    }

    /**
     * Convert the IPN message to its string representation.
     *
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
