<?php
/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/
class SessionClient {

    private $ip;
    private $varsession; // не должна хранится в КУКИ !!!

    // Конструктор
    public function sessClient() {
	global $_SERVER;
	$this->ip = $_SERVER['REMOTE_ADDR'];
	$this->varsession = array();
    }
    
    
    private function postSesServ($_type, $_id, $_key) {
	$data = array('type' => $_type,
	    'id' => $_id,
	    'ip' => $this->ip,
	    'key' => $_key,
	    'vs' => array2json($this->varsession,0)
	    );
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_URL, HOSTTMS . "serverstream/workSessions.php");
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data); //массив -> array
	// CURL будет возвращать результат, а не выводить его в печать
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	// никакие заголовки получать с сервера не будем
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	// запретить проверку сертификата удаленного сервера
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	// не будем проверять существование имени
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	//  максимальное время для выполнения cURL запроса
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	//  при возникновение ошибок, останавливать запрос
	curl_setopt($ch, CURLOPT_FAILONERROR, TRUE);
	// получаем страничку
	$json = curl_exec($ch);
	// закрываем сеанс Curl
	curl_close($ch);
	
	return json2array($json,0);
    }

    public function session() {
	global $_COOKIE, $_SERVER;
	
	$this->ip = $_SERVER['REMOTE_ADDR'];
	
	if (isset($_COOKIE['_tmsws'])) {
	    $retrData = $this->postSesServ("_tmsws", $_COOKIE['_tmsws'], "");
	    
	    if($retrData['id1']=="") {
		setCookie("_tmsws", "");
		@header("Location: {$_SERVER['REQUEST_URI']}");
		die();
	    } else {
		setCookie("_tmsws", $retrData['id1'], $retrData['to1'], "/");
		//$this->varsession=eval($retrData['vs']);		
	    }
	} else if (isset($_COOKIE['_tmswst'])) {
	    $retrData = $this->postSesServ("_tmswst", "", $_COOKIE['_tmswst']);
	    
	    setCookie("_tmswst", "");
	    setCookie("_tmsws", $retrData['id1'], $retrData['to1'], "/");
	    @header("Location: {$_SERVER['REQUEST_URI']}");
	    die();
	} else {
	    $retrData = $this->postSesServ("_", "", "");
	    
	    if($retrData['t']=="2") {
		setCookie("_tmswst", $retrData['id2'], $retrData['to2'], "/");
		@header("Location: {$_SERVER['REQUEST_URI']}");
		die();
	    } else {
		setCookie("_tmsws", $retrData['id1'], $retrData['to1'], "/");
		//$this->varsession=eval($retrData['vs']);
	    }
	}
    }
    
    //Создание переменной в сессии
    public function setVarSess($nameVar,$valuevar) {
	global $_COOKIE;
	$this->varsession[$nameVar]=$valuevar;
	$retrData = $this->postSesServ("_tmsws", $_COOKIE['_tmsws'], "");
    }
    
    //Получение значения переменной сессии
    public function getVarSess($nameVar) {
	$returnData="";
	if(isset($this->varsession[$nameVar])) {
	    $returnData=$this->varsession[$nameVar];
	}
	return $returnData;
    }
    
    //Удаление переменной сессии
    public function delVarSess($nameVar) {
	global $_COOKIE;
	unset($this->varsession[$nameVar]);
	$retrData = $this->postSesServ("_tmsws", $_COOKIE['_tmsws'], "");
    }
    
    //Уничтожение сессии
    public function session_destroy() {
	global $_COOKIE;
	$this->ip = "";
	$this->varsession = array();
	// Очистка в БД
	$retrData = $this->postSesServ("destroy", $_COOKIE['_tmsws'], "");
    }
}
?>