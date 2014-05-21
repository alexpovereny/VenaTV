<?php

/**
 * liverail api interface class
 */
class LiveRailApi {

    /**
     * the api URL
     */
    private $_apiUrl;

    /**
     * the token
     */
    private $_token;

    /**
     * last xml
     */
    private $_xml;

    /**
     * the parsed XML
     */
    private $_xmlDoc;
    private $_jsonDoc;

    /**
     * use session for storing login data
     * @var bool
     */
    private $_useSession = true;

    /**
     * the constructor
     * @param string $pApiUrl
     * @return LiveRailApi
     */
    function __construct($pApiUrl = "http://api.liverail.com") {
        $this->_apiUrl = rtrim($pApiUrl, "/");
    }

//end function __constructor

    /**
     * sets the api url
     * @param mixed $pApiUrl
     * @return void
     */
    public function setApiUrl($pApiUrl) {
        $this->_apiUrl = $pApiUrl;
        if ($this->_useSession) {
            $_SESSION["LiveRailApi"]["url"] = $pApiUrl;
        } //end if
    }

//end function setApiUrl

    /**
     * sets the use session flag
     * @param bool $pVar
     * @return void
     */
    public function setUseSession($pVar = true) {
        $this->_useSession = (bool) $pVar;
    }

//end function setUseSession


    function api_login() {

        if (!isset($this->_token)) {
            return false;
        }

        return $this->_token;
    }

    /**
     * performs the login action
     * @param string $pUser
     * @param string $pPassword
     * @param bool $applyMd5
     * @return boolean
     */
    function login($pUser, $pPassword, $applyMd5 = true) {
        unset($_SESSION["LiveRailApi"]);
        $_SESSION["LiveRailApi"] = array();
        if ($this->_useSession) {
            if (isset($_SESSION["LiveRailApi"]["url"])) {
                $this->_apiUrl = $_SESSION["LiveRailApi"]["url"];
            } //end if
        } //end if

        /* make the login call */
        $postAr = array(
            "username" => $pUser,
            "password" => ( $applyMd5 ? md5($pPassword) : $pPassword )
        );
        $post = $this->_flattenArray($postAr);
        $curl = curl_init($this->_apiUrl . "/login");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        $data = curl_exec($curl);
        curl_close($curl);
        $this->_xml = $data;

        /* load the XML */
        $this->_xmlDoc = @simplexml_load_string($data);
        //echo '<br>---$this->_xmlDoc!!!---<br>';
        //var_dump($this->_xmlDoc->status);

        if ($this->_xmlDoc === false) {
            return false;
        } //end if

        /* check the status */
        if ((string) $this->_xmlDoc->status != "success") {
            return false;
        } //end if

        /* get the token */
        $token = (string) @$this->_xmlDoc->auth->token;
        if ($token == "") {
            return false;
        } //end if

        /* save the token */
        $this->_token = $token;

        if ($this->_useSession) {
            $_SESSION["LiveRailApi"] = array(
                "url" => $this->_apiUrl,
                "token" => $token,
                "username" => $pUser
            );
        } //end if

        /* load the XML */
        $xml = simplexml_load_string($data);
        /* load the JSON */
        $this->_jsonDoc = json_encode($xml, JSON_PRETTY_PRINT);

        return true;
    }

//end function login

    /**
     * sets a current entity
     * @param int $pEntity
     * @param bool $pHeaderMode
     * @return boolean
     */
    function setEntity($pEntity, $pHeaderMode = true) {
        if ($this->_useSession) {
            if (isset($_SESSION["LiveRailApi"]) && isset($_SESSION["LiveRailApi"]["token"])) {
                $this->setToken($_SESSION["LiveRailApi"]["token"]);
            } //end if
            if (isset($_SESSION["LiveRailApi"]["url"])) {
                $this->_apiUrl = $_SESSION["LiveRailApi"]["url"];
            } //end if
        } //end if

        if (is_null($this->_token)) {
            $this->_xml = null;
            return false;
        } //end if

        /* make the set entity call */
        $postAr = array(
            "entity_id" => $pEntity
        );
        $post = $this->_flattenArray($postAr);
        $curl = curl_init($this->_apiUrl . "/set/entity");
        if ($pHeaderMode) {
            $headers = array("LiveRailApiToken: " . base64_encode($this->_token));
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        } else {
            $post .= "&token=" . urlencode($this->_token);
        } //end if
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        $data = curl_exec($curl);
        curl_close($curl);
        $this->_xml = $data;


        /* load the XML */
        $xml = simplexml_load_string($data);
        /* load the JSON */
        $this->_jsonDoc = json_encode($xml, JSON_PRETTY_PRINT);

        /* echo '<br> --- data --- <br>';
          var_dump($data);

          echo '<br> --- $this->_jsonDoc --- <br>';
          var_dump($this->_jsonDoc); */

        /* load the XML */
        $this->_xmlDoc = @simplexml_load_string($data);
        if ($this->_xmlDoc === false) {
            return false;
        } //end if

        /* check the status */
        if ((string) $this->_xmlDoc->status != "success") {
            return false;
        } //end if

        if ($this->_useSession) {
            $_SESSION["LiveRailApi"]["entityId"] = $pEntity;
        } //end if



        return true;
    }

//end function setEntity

    /**
     * performs a logout
     * @return void
     */
    public function logout() {
        if ($this->_useSession) {
            if (isset($_SESSION["LiveRailApi"]["token"])) {
                $this->_token = $_SESSION["LiveRailApi"]["token"];
            } //end if
        } //end if
        $res = $this->callApi("/logout", array("token" => $this->_token), false);
        if ($this->_useSession) {
            $_SESSION["LiveRailApi"] = array();
            unset($_SESSION["LiveRailApi"]);
        } //end if
        return $res;
    }

//end function logout

    /**
     * unsets the current entity
     * @return void
     */
    public function unsetEntity() {
        if ($this->_useSession) {
            if (isset($_SESSION["LiveRailApi"]["token"])) {
                $this->_token = $_SESSION["LiveRailApi"]["token"];
            } //end if
        } //end if
        $res = $this->callApi("/unset/entity", array("token" => $this->_token), false);
        if ($this->_useSession && $res) {
            if (isset($_SESSION["LiveRailApi"]["entityId"])) {
                $_SESSION["LiveRailApi"]["entityId"] = 0;
                unset($_SESSION["LiveRailApi"]["entityId"]);
            } //end if
        } //end if
        return $res;
    }

//end function logout

    /**
     * make an api call
     * @param string $url
     * @param array $pPostAr
     * @param bool $pHeaderMode for coditza
     * @return boolean
     */
    public function callApi($pUrl, $pPostAr, $pHeaderMode = true) {
        if ($this->_useSession) {
            if (isset($_SESSION["LiveRailApi"]["url"])) {
                $this->_apiUrl = $_SESSION["LiveRailApi"]["url"];
            } //end if
        } //end if

        /* make the set entity call */
        $curl = curl_init($this->_apiUrl . "/" . ltrim($pUrl, "/"));
        if (is_array($pPostAr)) {
            $post = $this->_flattenArray($pPostAr);
        } else {
            $post = $pPostAr;
        } //end if

        if ($pHeaderMode) {
            $headers = array("LiveRailApiToken: " . base64_encode($this->_token));
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        } //end if

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        $data = curl_exec($curl);
        curl_close($curl);
        $this->_xml = $data;

        //   echo '<br>--- $data ---<br>';
        //   var_dump($data);
        /* load the XML */
        $xml = simplexml_load_string($data);

        //    echo '<br>--- $xml ---<br>';
        //    var_dump($xml);
        /* load the JSON */
        $this->_jsonDoc = json_encode($xml, JSON_PRETTY_PRINT);
        /*
          echo '<br>--- $this->_jsonDoc ---<br>';
          var_dump($this->_jsonDoc);
          echo '<br>---2 $this->_jsonDoc ---<br>';
          var_dump($this->_jsonDoc.error);
          echo '<br>---3 $this->_jsonDoc ---<br>';
          echo (string) $this->_jsonDoc->error->message; */
        // echo '<br> --- $data --- <br>';
        //  var_dump($this->_xml);
        /* load the XML */
        $this->_xmlDoc = @simplexml_load_string($data);
        /* echo '<br>--- $this->_xmlDoc ---<br>';
          var_dump($this->_xmlDoc);
          echo '<br>--- $this->_xmlDoc->error ---<br>';
          var_dump($this->_xmlDoc->error);
          echo '<br>--- $this->_xmlDoc->error->message ---<br>';
          var_dump($this->_xmlDoc->error->message);
          echo '<br>---!!! $this->_xmlDoc->error->message ---<br>';
          echo (string) $this->_xmlDoc->error->message;
          echo '<br>--- json_encode $this->_xmlDoc->error->message ---<br>';
          var_dump(json_encode($this->_xmlDoc->error, JSON_PRETTY_PRINT)); */

        //   echo '<br>---!!! $this->_xmlDoc->error->message ---<br>';
        //   echo (string) $this->_xmlDoc->error->message;

        if ($this->_xmlDoc === false) {
            return false;
        } //end if
        // echo '<br> --- $this->_xmlDoc --- <br>';
        //  var_dump($this->_xmlDoc);
        /* check the status */
        if ((string) $this->_xmlDoc->status != "success") {
            return false;
        } //end if

        /* load the XML */
        //   $xml = simplexml_load_string($data);
        /* load the JSON */
        //   $this->_jsonDoc = json_encode($xml, JSON_PRETTY_PRINT);
        //   echo '<br> --- _jsonDoc --- <br>';
        //   var_dump($this->_jsonDoc);

        return true;
    }

//end function callApi

    /**
     * returns the token
     * @return string
     */
    public function getToken() {
        return $this->_token;
    }

//end function getToken

    /**
     * sets the token
     * @param string $pToken
     * @return void
     */
    public function setToken($pToken) {
        $this->_token = $pToken;
    }

//end function setToken

    /**
     * returns the last XML returned by the API
     * @return string
     */
    public function getLastApiXml() {
        return $this->_xml;
    }

//end function returnLastApiXml

    /**
     * returns an instance of SimpleXML for the last API xml
     * @return SimpleXML
     */
    public function getLastApiXmlDoc() {
        return $this->_xmlDoc;
    }

//end function getLastApiXmlDoc

    public function getLastApiJsonDoc() {
        $json = json_decode($this->_jsonDoc);
        return $json;
    }

//end function getLastApiJsonDoc

    /**
     * flattens an array & returns is as a "GET" string
     * @param mixed $pArray
     * @param string $pPrefix
     * @return string
     */
    private function _flattenArray($pArray, $pPrefix = "") {
        $finalAr = array();
        foreach ($pArray as $k => $v) {
            if ($pPrefix != "") {
                $k = "{$pPrefix}[{$k}]";
            } //end if
            if (is_array($v)) {
                $finalAr[] = $this->_flattenArray($v, $k);
            } else {
                $finalAr[] = $k . "=" . urlencode($v);
            } //end if
        } //end foreach

        return implode("&", $finalAr);
    }

//end function _flattenArray
}

//end class LiveRailApi