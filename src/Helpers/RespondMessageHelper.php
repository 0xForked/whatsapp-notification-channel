<?php

if (! function_exists('extract_message'))
{
    /**
     * @param $message
     * @param $needle
     * @return mixed
     *
     * Warning! Expected Message must be:
     * The communication with worker failed. `Server error: `POST http://{url}`
     *  resulted in a `500 Internal Server Error` response:
     *   {"code":500,"message":"session already active"}
     *   `
     * so we can extract by message and needle
     *
     */
    function extract_message($message, $needle)
    {
        $index = strpos($message, $needle) + strlen($needle);

        $result = substr($message, $index);

        $expect = trim(str_replace('`', '', trim($result)));

        return json_decode($expect);
    }
}
