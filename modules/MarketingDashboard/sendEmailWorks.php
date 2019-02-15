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

include_once "sendGrid/lib/swift_required.php";
include_once 'sendGrid/SmtpApiHeader.php';

$hdr = new SmtpApiHeader();
var_dump($toList);var_dump($nameList);var_dump($timeList); var_dump($subject);var_dump($from);

// The list of addresses this message will be sent to
// [This list is used for sending multiple emails using just ONE request to SendGrid]
$toList = array('lindjana.xhumari@gmail.com', '');

// Specify the names of the recipients
$nameList = array('Name 1', 'Name 2');

// Used as an example of variable substitution
$timeList = array('4 PM', '5 PM');

// Set all of the above variables
$hdr->addTo(array_values($toList));
$hdr->addSubVal('-name-', array_values($nameList));
$hdr->addSubVal('-time-', $timeList);

// Specify that this is an initial contact message
$hdr->setCategory("initial");

// You can optionally setup individual filters here, in this example, we have enabled the 
// footer filter
$hdr->addFilterSetting('footer', 'enable', 1);
$hdr->addFilterSetting('footer', "text/plain", "Thank you for your business");

// The subject of your email
$subject = 'Example SendGrid Email';

// Where is this message coming from. For example, this message can be from 
// support@yourcompany.com, info@yourcompany.com
$from = array('yourcompany@example.com' => 'Name Of Your Company');

// If you do not specify a sender list above, you can specifiy the user here. If a sender 
// list IS specified above
// This email address becomes irrelevant.
$to = array('lindjana.xhumari@gmail.com'=>'Personal Name Of Recipient');

# Create the body of the message (a plain-text and an HTML version).
# text is your plain-text email
# html is your html version of the email
# if the reciever is able to view html emails then only the html
# email will be displayed

/*
* Note the variable substitution here =)
*/
$text = " 
Hello -name-,

Thank you for your interest in our products. We have set up an appointment
to call you at -time- EST to discuss your needs in more detail.

Regards,
Fred
";

$html = "
<html>
  <head></head>
  <body>
    <p>Hello -name-,<br>
       Thank you for your interest in our products. We have set up an appointment
             to call you at -time- EST to discuss your needs in more detail.

                Regards,

                Fred, How are you?<br>
    </p>
  </body>
</html>
";

// Your SendGrid account credentials
$username = 'lindjana';
$password = 'sendgrid10';

// Create new swift connection and authenticate
$transport = Swift_SmtpTransport::newInstance('smtp.sendgrid.net', 25);
$transport ->setUsername($username);
$transport ->setPassword($password);
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
$message->setBody($html, 'text/html');
$message->setTo($to);
$message->addPart($text, 'text/plain');
$logger = new Swift_Plugins_Loggers_EchoLogger(); 
$swift->registerPlugin(new Swift_Plugins_LoggerPlugin($logger)); 
$result=$swift->send($message);
var_dump($result);
// send message
/* if ($swift->send($message))
{
// This will let us know how many users received this message
// If we specify the names in the X-SMTPAPI header, then this will always be 1.
echo 'Message sent out to  users';
}
// something went wrong =(
else
{
echo "Something went wrong - ";
print_r($failures);
}*/
?>
