<?php
namespace hdevplus;
class connector
{
    private $resCURL;
    private $arrHeader = [];
    public $arrRequestData = [];
    private $strApiUrl;
    private $strSecureKey;
    public $result;
    public $response;
    public function __construct(string $strApiUrl, string $strSecureKey)
    {
        $this->strApiUrl = $strApiUrl;
        $this->strSecureKey = $strSecureKey;
        $this->resCURL = curl_init($this->strApiUrl);
        $this->arrHeader[] = 'Authorization: Bearer ' . $_COOKIE['api_token'];
    }

    public function request($arrRequestData)
    {
        $this->arrHeader[] = 'data-id:' . $arrRequestData['dataID'];
        $arrRequestData['session_id'] = $_COOKIE['session_id'];
        // 콘솔 접속 확인은 api_token 과 secureKey 존제 여부로 판단
        $arrRequestData['api_token'] = $arrRequestData['console'] = $_COOKIE['api_token'];
        if(!$arrRequestData['api_token']) {
            $arrRequestData['secure_key'] = $this->strSecureKey;
        }
        $this->arrRequestData = array_merge($arrRequestData, $_REQUEST ? : array());

        curl_setopt($this->resCURL, CURLOPT_HEADER, false);
        curl_setopt($this->resCURL, CURLOPT_HTTPHEADER, $this->arrHeader);
        curl_setopt($this->resCURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->resCURL, CURLOPT_POST, 1);
        curl_setopt($this->resCURL, CURLOPT_POSTFIELDS, http_build_query($this->arrRequestData));
        curl_setopt($this->resCURL, CURLOPT_REFERER, "");
        curl_setopt($this->resCURL, CURLOPT_TIMEOUT, 50);
        curl_setopt($this->resCURL, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->resCURL, CURLOPT_SSL_VERIFYHOST, false);
        $this->result = $strResult = curl_exec($this->resCURL);
        $this->response = $arrResponse = curl_getinfo($this->resCURL);
        if (curl_errno($this->resCURL)) {
            echo(curl_errno($this->resCURL) . ' || ' . curl_error($this->resCURL));
        }

        curl_close($this->resCURL);
        error_log(var_export($arrResponse,true));
        error_log($strResult);
        if (is_array($arrResponse) && $arrResponse['http_code'] == 200) {
            $arrReturn = json_decode($strResult, true);
            if($arrReturn['redirect_url']){
                header('location:'.$arrReturn['redirect_url']);
                exit;
            }
            setcookie("session_id", $arrReturn['session']['id'], [
                'expires' => time() + (60 * 60 * 24 * 365),
                'path' => '/',
                'secure' => true,
                'samesite' => 'None'
            ]);
            setcookie("api_token", $arrReturn['session']['api_token'], [
                'expires' => time() + (60 * 60 * 24 * 365),
                'path' => '/',
                'secure' => true,
                'samesite' => 'None'
            ]);
        } else {
            $arrReturn = $arrResponse;
            error_log(var_export($arrReturn,true));
        }
        return ($arrReturn);
    }
    public function loadScript(){
        $arrResult = json_decode($this->result, true);
        foreach(is_array($arrResult['env']['js'])?$arrResult['env']['js']:[] as $strScript){
            echo '<script src="'.$strScript.'"></script>';
        }
    }
}