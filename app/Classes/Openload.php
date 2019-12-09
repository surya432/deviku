<?php

namespace App\Classes;

use GuzzleHttp\Client as GuzzleClient;
use Guzzle\Http\Exception\ClientErrorResponseException;
use GuzzleHttp\Psr7\Request;
use Auth;
use Exception;

class Openload
{
    public $baseUrl = "https://api.openload.co/1";
    public function clientCurl($urlEndpoint, $data)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->baseUrl . $urlEndpoint . $data,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Cache-Control: no-cache",
                "Content-Type:  application/json",
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return $err;
        } else {
            return $response;
        }
    }
    public function getKey($data, $mirror)
    {
        try {
            $keys = explode(":::", $data['keys']);
            $data = "login=$keys[0]&key=$keys[1]";
            return $data;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    public function OpenloadUpload($data)
    {
        $response = $this->clientCurl("/remotedl/add?", $data);
        return json_decode($response, true);
    }
    public function OpenloadStatus($data)
    {
        $response = $this->clientCurl("/remotedl/status?", $data);
        return json_decode($response, true);
    }
}
