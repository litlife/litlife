<?php

namespace Tests;

trait SharedDataTrait
{
    protected function setSharedVar($key, $value)
    {
        $GLOBALS['shared_data'][$key] = $value;
    }

    protected function getSharedVar($key)
    {
        if (!isset($GLOBALS['shared_data']) or !is_array($GLOBALS['shared_data'])) {
            $GLOBALS['shared_data'] = array();
        }

        if (array_key_exists($key, $GLOBALS['shared_data'])) {
            return $GLOBALS['shared_data'][$key];
        } else {
            return null;
        }
    }
}