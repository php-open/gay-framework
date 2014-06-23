<?php
/*
* yum install sharutils
* $smtp = new smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);
* $smtp->setAttachments('BDTaskReport2012-04-01.xlsx');
* $smtp->sendmail('haiyang.xu@baifendian.com', $smtpusermail,$mail_subject, $mail_body, 'HTML');
*/
date_default_timezone_set('PRC');
class smtp {
    /* Public Variables */

    var $smtp_port;

    var $time_out;

    var $host_name;

    var $log_file;

    var $relay_host;

    var $debug;

    var $auth;

    var $user;

    var $pass;
	
	var $mailAttachments;	

    /* Private Variables */
    var $sock;

    /* Constractor */

    function smtp($relay_host = "", $smtp_port = 25, $auth = false, $user, $pass)
    {
        $this->debug = false;

        $this->smtp_port = $smtp_port;

        $this->relay_host = $relay_host;

        $this->time_out = 30; //is used in fsockopen()

        $this->auth = $auth; //auth

        $this->user = $user;

        $this->pass = $pass;

        $this->host_name = "localhost"; //is used in HELO command
        $this->log_file = realpath(dirname(__FILE__))."/mail.log";

        $this->sock = false;
		
		$this->mailAttachments = '';
    }

    /* Main Function */

    function sendmail($to, $from, $subject = "", $body = "", $mailtype ="HTML", $cc = "", $bcc = "", $additional_headers = "")
    {
        $mail_from = $this->get_address($this->strip_comment($from));

        $body = ereg_replace("(^|(\r\n))(\.)", "\\1.\\3", $body);

        $header = "MIME-Version:1.0\r\n";

        $header .= "To: " . $to . "\r\n";

        if ($cc != "") {
            $header .= "Cc: " . $cc . "\r\n";
        }

        $header .= "From: $from<" . $from . ">\r\n";

        $header .= "Subject: " . "=?UTF-8?B?".base64_encode($subject)."?=" . "\r\n";

        $header .= $additional_headers;

        $header .= "Date: " . date("r") . "\r\n";

        $header .= "X-Mailer:By Redhat (PHP/" . phpversion() . ")\r\n";

        list($msec, $sec) = explode(" ", microtime());

        $header .= "Message-ID: <" . date("YmdHis", $sec) . "." . ($msec * 1000000) . "." . $mail_from . ">\r\n";

        $TO = explode(";", $this->strip_comment($to));

        if ($cc != "") {
            $TO = array_merge($TO, explode(";", $this->strip_comment($cc)));
        }

        if ($bcc != "") {
            $TO = array_merge($TO, explode(";", $this->strip_comment($bcc)));
        }
		
		//----------------------------------
		$boundary = $this->getRandomBoundary();
		$header .= "Content-Type: multipart/mixed;";
		$header .= ' boundary="'.$boundary. '"'. "\r\n";
		$header .= "X-CM-HeaderCharset: UTF-8\r\n";	
		
		$header .= "--".$boundary. "\r\n";
		$bodyBoundary = $this->getRandomBoundary(1);
		
		if( $mailtype=='HTML' )
		{
			$htmlHeader = $this->formatHTMLHeader($body);
		}
		else
		{
			$textHeader = $this->formatTextHeader($body);
		}

		$header .= "Content-Type: multipart/alternative;";
		$header .= ' boundary="'.$bodyBoundary. '"';
		$header .= "\r\n\r\n";
		$header .= "--".$bodyBoundary. "\r\n";
		
		if( $mailtype=='HTML' )
		{
			$header .= $htmlHeader;
			$header .= "\r\n";
		}
		else
		{
			//$header .= "--".$bodyBoundary. "\r\n";
			$header .= $textHeader;
			$header .= "\r\n";
		}
		
		$header .= "--".$bodyBoundary. "--\r\n\r\n";
		
		//--获取附件值
		if(strlen(trim($this->mailAttachments))>0)
		{
			$attachmentArray = explode(",",$this->mailAttachments);
			//--根据附件的个数进行循环
			for($i=0;$i<count($attachmentArray);$i++){
				//--分割
				$header .= "--".$boundary. "\r\n";
				//--附件信息
				$header .= $this->formatAttachmentHeader($attachmentArray[$i]);
			}
			$header .= "--".$boundary. "--\r\n\r\n";
		}
		//----------------------------------
		
        $sent = true;

        foreach ($TO as $rcpt_to) {
            $rcpt_to = $this->get_address($rcpt_to);

            if (!$this->smtp_sockopen($rcpt_to)) {
                $this->log_write("Error: Cannot send email to " . $rcpt_to . "\n");

                $sent = false;

                continue;
            }

            //if ($this->smtp_send($this->host_name, $mail_from, $rcpt_to, $header, $body)) {
			if ($this->smtp_send($this->host_name, $mail_from, $rcpt_to, $header)) {
                $this->log_write("E-mail has been sent to <" . $rcpt_to . ">\n");
            } else {
                $this->log_write("Error: Cannot send email to <" . $rcpt_to . ">\n");

                $sent = false;
            }

            fclose($this->sock);

            $this->log_write("Disconnected from remote host\n");
        }

        return $sent;
    }

    /* Private Functions */

    //function smtp_send($helo, $from, $to, $header, $body = "")
	function smtp_send($helo, $from, $to, $header)
    {
        if (!$this->smtp_putcmd("HELO", $helo)) {
            return $this->smtp_error("sending HELO command");
        }
        // auth
        if ($this->auth) {
            if (!$this->smtp_putcmd("AUTH LOGIN", base64_encode($this->user))) {
                return $this->smtp_error("sending HELO command");
            }

            if (!$this->smtp_putcmd("", base64_encode($this->pass))) {
                return $this->smtp_error("sending HELO command");
            }
        }

        if (!$this->smtp_putcmd("MAIL", "FROM:<" . $from . ">")) {
            return $this->smtp_error("sending MAIL FROM command");
        }

        if (!$this->smtp_putcmd("RCPT", "TO:<" . $to . ">")) {
            return $this->smtp_error("sending RCPT TO command");
        }

        if (!$this->smtp_putcmd("DATA")) {
            return $this->smtp_error("sending DATA command");
        }

        //if (!$this->smtp_message($header, $body)) {
		if (!$this->smtp_message($header)) {
            return $this->smtp_error("sending message");
        }

        if (!$this->smtp_eom()) {
            return $this->smtp_error("sending <CR><LF>.<CR><LF> [EOM]");
        }

        if (!$this->smtp_putcmd("QUIT")) {
            return $this->smtp_error("sending QUIT command");
        }

        return true;
    }

    function smtp_sockopen($address)
    {
        if ($this->relay_host == "") {
            return $this->smtp_sockopen_mx($address);
        } else {
            return $this->smtp_sockopen_relay();
        }
    }

    function smtp_sockopen_relay()
    {
        $this->log_write("Trying to " . $this->relay_host . ":" . $this->smtp_port . "\n");

        $this->sock = @fsockopen($this->relay_host, $this->smtp_port, $errno, $errstr, $this->time_out);

        if (!($this->sock && $this->smtp_ok())) {
            $this->log_write("Error: Cannot connenct to relay host " . $this->relay_host . "\n");

            $this->log_write("Error: " . $errstr . " (" . $errno . ")\n");

            return false;
        }

        $this->log_write("Connected to relay host " . $this->relay_host . "\n");

        return true;;
    }

    function smtp_sockopen_mx($address)
    {
        $domain = ereg_replace("^.+@([^@]+)$", "\1", $address);

        if (!@getmxrr($domain, $MXHOSTS)) {
            $this->log_write("Error: Cannot resolve MX \"" . $domain . "\"\n");

            return false;
        }

        foreach ($MXHOSTS as $host) {
            $this->log_write("Trying to " . $host . ":" . $this->smtp_port . "\n");

            $this->sock = @fsockopen($host, $this->smtp_port, $errno, $errstr, $this->time_out);

            if (!($this->sock && $this->smtp_ok())) {
                $this->log_write("Warning: Cannot connect to mx host " . $host . "\n");

                $this->log_write("Error: " . $errstr . " (" . $errno . ")\n");

                continue;
            }

            $this->log_write("Connected to mx host " . $host . "\n");

            return true;
        }

        $this->log_write("Error: Cannot connect to any mx hosts (" . implode(", ", $MXHOSTS) . ")\n");

        return false;
    }

	function smtp_message($header)
    {
		fputs($this->sock, $header);

        $this->smtp_debug("> " . str_replace("\r\n", "\n" . "> ", $header . "\n> "));

        return true;
    }

    function smtp_eom()
    {
        fputs($this->sock, "\r\n.\r\n");

        $this->smtp_debug(". [EOM]");

        return $this->smtp_ok();
    }

    function smtp_ok()
    {
        $response = str_replace("\r\n", "", fgets($this->sock, 512));

        $this->smtp_debug($response);

        if (!ereg("^[23]", $response)) {
            fputs($this->sock, "QUIT\r\n");

            fgets($this->sock, 512);

            $this->log_write("Error: Remote host returned \"" . $response . "\"");

            return false;
        }

        return true;
    }

    function smtp_putcmd($cmd, $arg = "")
    {
        if ($arg != "") {
            if ($cmd == "") $cmd = $arg;
            else $cmd = $cmd . " " . $arg;
        }

        fputs($this->sock, $cmd . "\r\n");

        $this->smtp_debug("> " . $cmd);

        return $this->smtp_ok();
    }

    function smtp_error($string)
    {
        $this->log_write("Error: Error occurred while " . $string);

        return false;
    }

    function log_write($message)
    {
        $this->smtp_debug($message);

        if ($this->log_file == "") {
            return true;
        }

        file_put_contents($this->log_file, date('Y-m-d H:i:s') . ' ' . $message . "\n", FILE_APPEND);

		return $this->rollLog();
    }
	
		/**
		 * 分割日志文件
		 * 
		 */
		private function rollLog()
		{
			if(file_exists($this->log_file)){
				//10M一个文件
				if(filesize($this->log_file)>10*1024*1024){
					$pos = strrpos($this->log_file, '.');
					$new_file = substr($this->log_file,0,$pos) . '_' . time() . '.' . substr($this->log_file, $pos+1);
					$this->rename($this->log_file,$new_file);
				}
			}
		}
		
		private function rename( $old_filename, $new_filename){
			if (empty($old_filename) || empty($new_filename)) return false;
			$success = false;
			if(file_exists($new_filename)) {
				unlink($new_filename);
				$success = rename($old_filename, $new_filename);
			}
			else {
				$success = rename($old_filename, $new_filename);
			}
		
			return $success;
		}

    function strip_comment($address)
    {
        $comment = "\([^()]*\)";

        while (ereg($comment, $address)) {
            $address = ereg_replace($comment, "", $address);
        }

        return $address;
    }

    function get_address($address)
    {
        $address = ereg_replace("([ \t\r\n])+", "", $address);

        $address = ereg_replace("^.*<(.+)>.*$", "\1", $address);

        return $address;
    }

    function smtp_debug($message)
    {
        if ($this->debug) {
            echo $message . "\n";
        }
    }
	
	function setAttachments($inAttachments)
	{
		if(strlen(trim($inAttachments)) > 0)
		{
			$this->mailAttachments = $inAttachments;
			return true; 
		}
		return false; 
	}
	
	/*****************************************
	函数 getRandomBoundary($offset) 返回一个随机的边界值
	参数 $offset 为整数 – 用于多管道的调用 返回一个md5()编码的字串
	****************************************/
	function getRandomBoundary($offset = 0)
	{
		//--随机数生成
		srand(time()+$offset);
		//--返回 md5 编码的32位 字符长度的字串
		return ( "----".(md5(rand()))); 
	}
	
	/**********************************************
	函数formatTextHeader把文本内容加上text的文件头
	*****************************************************/
	function formatTextHeader($body)
	{
		$outTextHeader = "";
		$outTextHeader .= "Content-Type: text/plain; charset=\"UTF-8\"\r\n";
		$outTextHeader .= "Content-Transfer-Encoding: base64\r\n\r\n";
		$outTextHeader .= base64_encode($body). "\r\n";
		return $outTextHeader;
	}
	
	/************************************************
	函数formatHTMLHeader()把邮件主体内容加上html的文件头
	******************************************/
	function formatHTMLHeader($body)
	{
		$outHTMLHeader = "";
		$outHTMLHeader .= "Content-Type:text/html; charset=\"UTF-8\"\r\n";
		$outHTMLHeader .= "Content-Transfer-Encoding: base64\r\n\r\n";
		$outHTMLHeader .= base64_encode($body). "\r\n";
		return $outHTMLHeader;
	}
	
	/**********************************
	函数 formatAttachmentHeader($inFileLocation) 把邮件中的附件标识出来
	********************************/
	function formatAttachmentHeader($inFileLocation)
	{
		$outAttachmentHeader = "";
		//--用上面的函数getContentType($inFileLocation)得出附件类型
		$contentType = $this->getContentType($inFileLocation);
		//--如果附件是文本型则用标准的7位编码
		$outAttachmentHeader .= "Content-Type: ".$contentType. ";";
		$outAttachmentHeader .= ' name="'.basename($inFileLocation). '"'. "\r\n";
		$outAttachmentHeader .= "Content-Transfer-Encoding: base64\r\n";
		$outAttachmentHeader .= "Content-Disposition: attachment;";
		$outAttachmentHeader .= ' filename="'.basename($inFileLocation). '"'. "\r\n\r\n";
		//--调用外部命令uuencode进行编码
		exec( "uuencode -m $inFileLocation nothing_out",$returnArray);
		//exec( "base64 $inFileLocation",$returnArray);
		
		for ($i = 1; $i<(count($returnArray)-1); $i++)
		{
			$outAttachmentHeader .= $returnArray[$i]. "\r\n";
		}
		return $outAttachmentHeader;
	}
	
	/********************************************
	函数: getContentType($inFileName)用于判断附件的类型
	**********************************************/
	function getContentType($inFileName)
	{
		//--去除路径
		$inFileName = basename($inFileName);
		//--去除没有扩展名的文件
		if(strrchr($inFileName, ".") == false){
			return "application/octet-stream";
		}
		//--提区扩展名并进行判断
		$extension = strrchr($inFileName, ".");
		switch($extension)
		{
			case ".gif": return "image/gif";
			case ".gz": return "application/x-gzip";
			case ".htm": return "text/html";
			case ".html": return "text/html";
			case ".jpg": return "image/jpeg";
			case ".tar": return "application/x-tar";
			case ".txt": return "text/plain";
			case ".zip": return "application/zip";
			case ".xls": return "application/vnd.ms-excel";
			case ".xlsx": return "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
			default: return "application/octet-stream";
		}
		return "application/octet-stream";
	}
	
}
?>