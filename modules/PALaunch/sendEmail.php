<?php
/*************************************************************************************************
 * Copyright 2012-2013 OpenCubed  --  This file is a part of vtMktDashboard.
* You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
* Vizsage Public License (the "License"). You may not use this file except in compliance with the
* License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
* and share improvements. However, for proper details please read the full License, available at
* http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
* the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
* applicable law or agreed to in writing, any software distributed under the License is distributed
* on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
* See the License for the specific language governing permissions and limitations under the
* License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
*************************************************************************************************
*  Module       : MarketingDashboard
*  Version      : 1.9
*  Author       : OpenCubed
*************************************************************************************************/
/*function getEmailSendgrid($type)
{
	global $log,$adb;
	
	$data_server = Array();
	
	if($type == 'Marketing')
	{
		$rsinfo = $adb->query("SELECT usesg_marketing,srv_marketing,user_marketing,pass_marketing FROM sendgrid_config");
		$usemkt = $adb->query_result($rsinfo, 0, 'usesg_marketing');
		if($usemkt)
		{
			$data_server['server'] = $adb->query_result($rsinfo, 0, 'srv_marketing');
			$data_server['username'] = $adb->query_result($rsinfo, 0, 'user_marketing');
			$data_server['password'] = $adb->query_result($rsinfo, 0, 'pass_marketing');
			
		}
		else
		{
			$rsinfo = $adb->query("SELECT usesg_transactional,srv_transactional,user_transactional,pass_transactional FROM sendgrid_config");
			$usemkt = $adb->query_result($rsinfo, 0, 'usesg_transactional');
			if($usemkt)
			{
				$data_server['server'] = $adb->query_result($rsinfo, 0, 'srv_transactional');
				$data_server['username'] = $adb->query_result($rsinfo, 0, 'user_transactional');
				$data_server['password'] = $adb->query_result($rsinfo, 0, 'pass_transactional');
				
			}
			else
			{
				$res = $adb->pquery("select * from vtiger_systems where server_type=?", array('email'));
				$data_server['server'] = $adb->query_result($res,0,'server');
				$data_server['username'] = $adb->query_result($res,0,'server_username');
				$data_server['password'] = $adb->query_result($res,0,'server_password');
			}
		}
	}
	else
	{
		$rsinfo = $adb->query("SELECT usesg_transactional,srv_transactional,user_transactional,pass_transactional FROM sendgrid_config");
		$usemkt = $adb->query_result($rsinfo, 0, 'usesg_transactional');
		if($usemkt)
		{
			$data_server['server'] = $adb->query_result($rsinfo, 0, 'srv_transactional');
			$data_server['username'] = $adb->query_result($rsinfo, 0, 'user_transactional');
			$data_server['password'] = $adb->query_result($rsinfo, 0, 'pass_transactional');
			
		}
		else
		{
			$res = $adb->pquery("select * from vtiger_systems where server_type=?", array('email'));
			$data_server['server'] = $adb->query_result($res,0,'server');
			$data_server['username'] = $adb->query_result($res,0,'server_username');
			$data_server['password'] = $adb->query_result($res,0,'server_password');
		}		
	}
	return $data_server;
}
function sendGridMail($toList,$content,$subject,$from,$campaignid,$type,$crmid,$attachments=array()) {
	include_once "sendGrid/lib/swift_required.php";
	include_once 'sendGrid/SmtpApiHeader.php';

	global $log,$adb;
	// Your SendGrid account credentials
	$log->debug("Sending email with sendgrid as ".$campaignid);
	$hdr = new SmtpApiHeader();
	// Specify the category as the campaign
	$hdr->setCategory("$campaignid");
	// Specify the crmid of the email/message for reception control
	$hdr->setUniqueArgs(array('crmid'=>$crmid));

	// You can optionally setup individual filters here, in this example, we have enabled the footer filter
	//$hdr->addFilterSetting('footer', 'enable', 1);
	//$hdr->addFilterSetting('footer', "text/plain", "Thank you for your business");

	// Create new swift connection and authenticate
	$data_server = getEmailSendgrid($type);
	$transport = Swift_SmtpTransport::newInstance($data_server['server'], 25);
	$transport ->setUsername($data_server['username']);
	$transport ->setPassword($data_server['password']);
	$swift = Swift_Mailer::newInstance($transport);

	// Create a message (subject)
	$message = new Swift_Message($subject);
	// add SMTPAPI header to the message
	// *****IMPORTANT NOTE*****
	// SendGrid's asJSON function escapes characters. If you are using Swift Mailer's
	// PHP Mailer functions, the getTextHeader function will also escape characters.
	// This can cause the filter to be dropped.
	$headers = $message->getHeaders();
	$headers->addTextHeader('X-SMTPAPI', $hdr->asJSON());

	// attach the body of the email
	$message->setFrom($from);
	//$contents=html_entity_decode($content);
	$message->setBody($content, 'text/html');
	$rstplotxt = $adb->query("select templateonlytext from vtiger_actions where actionsid=$crmid");
	if ($rstplotxt and $adb->num_rows($rstplotxt)==1) {
		$tplnothml = $adb->query_result($rstplotxt,0,0);
		if (empty($tplnothml)) {
			$tplnothml=strip_tags(preg_replace(array("/<p>/i","/<br>/i","/<br \/>/i"),array("\n","\n","\n"),$content));
		}
		$message->addPart($tplnothml, 'text/plain');
	}
	$message->setTo($toList);
	//$message->addPart($content, 'text/plain');
	//$logger = new Swift_Plugins_Loggers_EchoLogger();
	//$swift->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));
	if (is_array($attachments) and count($attachments)>0) {
		foreach ($attachments as $attach) {
			$dstfile = 'cache/'.$attach['fname'];
			@copy($attach['fpath'],$dstfile);
			$message->attach(\Swift_Attachment::fromPath($dstfile));
		}
	}

	// send message
	if ($recipients = $swift->send($message,$failures)) {
		// This will let us know how many users received this message
		// If we specify the names in the X-SMTPAPI header, then this will always be 1.
		$log->debug( 'Message sent out to '.$recipients.' users');
		$result = 1;
	} else { // something went wrong =(
		$log->debug("Something went wrong - ");
		$result = $failures;
	}
	// Keep cache clean
	if (is_array($attachments) and count($attachments)>0) {
		foreach ($attachments as $attach) {
			$dstfile = 'cache/'.$attach['fname'];
			@unlink($dstfile);
		}
	}
	return $result;
}*/

?>
