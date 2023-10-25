<?php


namespace App\Services;


class Validator
{
    /**
     * @param $settingValue
     * @param $publisherValue
     * @return bool
     */
    public function eq($settingValue, $publisherValue)
    {
        if ($publisherValue !== $settingValue) {
            return false;
        }
        return true;
    }

    /**
     * @param $settingValue
     * @param $publisherValue
     * @return bool
     */
    public function neq($settingValue, $publisherValue)
    {
        if ($publisherValue === $settingValue) {
            return false;
        }
        return true;
    }

    /**
     * @param $settingValue
     * @param $publisherValue
     * @return bool
     */
    public function gt($settingValue, $publisherValue)
    {
        if ($publisherValue > $settingValue) {
            return true;
        }
        return false;
    }

    /**
     * @param $settingValue
     * @param $publisherValue
     * @return bool
     */
    public function lt($settingValue, $publisherValue)
    {
        if ($publisherValue < $settingValue) {
            return true;
        }
        return false;
    }

    /**
     * @param $settingValue
     * @param $publisherValue
     * @return bool
     */
    public function range_v($settingValue, $publisherValue)
    {
        $range = explode('-', $settingValue);
        if ($publisherValue > $range[0] && $publisherValue < $range[1])
        {
            return true;
        }
        return false;
    }
}