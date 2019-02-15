<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source in cooperation with a-g-c (Andreas Goebel)
 * The Initial Developer of the Original Code is vtiger and a-g-c (Andreas Goebel)
 * Portions created by vtiger are Copyright (C) vtiger, portions created by a-g-c are Copyright (C) a-g-c.
 * All Rights Reserved.
 * www.a-g-c.de
 ************************************************************************************/
ini_set('max_execution_time','1800');
require_once("modules/SQLReports/ReportRunSQL.php");
require_once("modules/SQLReports/ReportsSQL.php");
//require('include/fpdf/fpdf.php');
require('include/tcpdf/tcpdf.php');
$language = $_SESSION['authenticated_user_language'].'.lang.php';
require_once("include/language/$language");

$reportid = vtlib_purify($_REQUEST["record"]);

    $oReport = new ReportsSQL($reportid);

    $oReportRun = new ReportRunSQL($reportid);
    $arr_val = $oReportRun->GenerateReport("PDF",$filterlist);
	
	$col_width = array();

    if(isset($arr_val))
    {

            foreach($arr_val as $wkey=>$warray_value)
            {
                    foreach($warray_value as $whd=>$wvalue)
                    {
                            if(strlen($wvalue) < strlen($whd))
                            {
                                    $w_inner_array[] = strlen($whd);
                            }else
                            {
                                    $w_inner_array[] = strlen($wvalue);
                            }
                    }
                    $warr_val[] = $w_inner_array;
                    unset($w_inner_array);
            }

            foreach($warr_val[0] as $fkey=>$fvalue)
            {
                    foreach($warr_val as $wkey=>$wvalue)
                    {

                            $f_inner_array[] = $warr_val[$wkey][$fkey];
                    }
                    sort($f_inner_array,1);
                    $farr_val[] = $f_inner_array;
                    unset($f_inner_array);
            }

            foreach($farr_val as $skkey=>$skvalue)
            {
                    if($skvalue[count($arr_val)-1] == 1)
                    {
                            $col_width[] = ($skvalue[count($arr_val)-1] * 100);
                    } else
                    {
                            $col_width[] = ($skvalue[count($arr_val)-1] * 10) + 10 ;
                    }
            }
            $count = 0;
            foreach($arr_val[0] as $key=>$value)
            {
                    $headerHTML .= '<td width="'.$col_width[$count].'" bgcolor="#DDDDDD"><b>'.$oReportRun->getLstringforReportHeaders($key).'</b></td>';
                    $count = $count + 1;
            }

            foreach($arr_val as $key=>$array_value)
            {
                    $valueHTML = "";
                    $count = 0;
                    foreach($array_value as $hd=>$value)
                    {
                            $valueHTML .= '<td width="'.$col_width[$count].'">'.$value.'</td>';
                            $count = $count + 1;
                    }
                    $dataHTML .= '<tr>'.$valueHTML.'</tr>';
            }

    }

    $html='<table border="1"><tr>'.$headerHTML.'</tr>'.$dataHTML.'</table>';
    $columnlength = array_sum($col_width);
    if($columnlength > 14400)
    {
            die("<br><br><center>".$app_strings['LBL_PDF']." <a href='javascript:window.history.back()'>".$app_strings['LBL_GO_BACK'].".</a></center>");
    }
    if($columnlength <= 420 )
    {
            $pdf = new TCPDF('P','mm','A5',true);

    }elseif($columnlength >= 421 && $columnlength <= 1120)
    {
            $pdf = new TCPDF('L','mm','A3',true);

    }elseif($columnlength >=1121 && $columnlength <= 1600)
    {
            $pdf = new TCPDF('L','mm','A2',true);

    }elseif($columnlength >=1601 && $columnlength <= 2200)
    {
            $pdf = new TCPDF('L','mm','A1',true);
    }
    elseif($columnlength >=2201 && $columnlength <= 3370)
    {
            $pdf = new TCPDF('L','mm','A0',true);
    }
    elseif($columnlength >=3371 && $columnlength <= 4690)
    {
            $pdf = new TCPDF('L','mm','2A0',true);
    }
    elseif($columnlength >=4691 && $columnlength <= 6490)
    {
            $pdf = new TCPDF('L','mm','4A0',true);
    }
    else
    {
            $columnhight = count($arr_val)*15;
            $format = array($columnhight,$columnlength);
            $pdf = new TCPDF('L','mm',$format,true);
    }
    $pdf->SetMargins(10, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    $pdf->setLanguageArray($l);
    //echo '<pre>';print_r($columnlength);echo '</pre>';
    $pdf->AddPage();

    $pdf->SetFillColor(224,235,255);
    $pdf->SetTextColor(0);
    $pdf->SetFont('FreeSerif','B',14);
    $pdf->Cell(($pdf->columnlength*50),10,getTranslatedString($oReport->reportname),0,0,'C',0);
    //$pdf->writeHTML($oReport->reportname);
    $pdf->Ln();

    $pdf->SetFont('FreeSerif','',10);

    $pdf->writeHTML($html);
    $pdf->Output('Reports.pdf','D');
    exit();
?>
