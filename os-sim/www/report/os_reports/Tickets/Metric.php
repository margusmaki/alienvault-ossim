<?php
/**
*
* License:
*
* Copyright (c) 2003-2006 ossim.net
* Copyright (c) 2007-2013 AlienVault
* All rights reserved.
*
* This package is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; version 2 dated June, 1991.
* You may not use, modify or distribute this program under any other version
* of the GNU General Public License.
*
* This package is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this package; if not, write to the Free Software
* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston,
* MA  02110-1301  USA
*
*
* On Debian GNU/Linux systems, the complete text of the GNU General
* Public License can be found in `/usr/share/common-licenses/GPL-2'.
*
* Otherwise you can read it here: http://www.gnu.org/licenses/gpl-2.0.txt
*
*/


require_once ('ossim_db.inc');

$db   = new ossim_db();
$conn1 = $db->connect();

require('general.php');

$htmlPdfReport->pageBreak();
$htmlPdfReport->setBookmark($title);

$htmlPdfReport->set($htmlPdfReport->newTitle($title, $date_from, $date_to, ''));


if ( Session::menu_perms("analysis-menu", "EventsForensics") ) 
{
    $htmlPdfReport->set('<table class="w100" style="padding:15px 0px 0px 0px;" cellpadding="2" cellspacing="2">');
    
    $list = $pdf->IncidentSummaryNoPDF($title , "Metric", '', null, $args, null, $conn1, $user);
    unset($list['TipoIncidente']);
    
    $colors = array ("red" => "px_red.png", "orange" => "px_orange.png", "green" => "px_green.png");
    $c=0;
    
    $size = count($list);     
    if ( $size > 0 )
    {
        $htmlPdfReport->set('
                    <tr>
                        <th style="width:30mm;" class="center">'._("Date").'</th>
                        <th style="width:61mm;" class="center">'._("Metric").'</th>
                        <th style="width:30mm;" class="center">'._("Date").'</th>
                        <th style="width:61mm;" class="center">'._("Metric").'</th>
                    </tr>');
            
        foreach($list as $key => $value)
        {
            if( $c%2 == 0 ){
                $htmlPdfReport->set('<tr style="margin-bottom:5px">');
            }
        
            $imgColor = ( !empty($colors[$value[2]]) ) ? '<img src="Tickets/'.$colors[$value[2]].'" width="5" height="5"/>' : "<span style='display:block;width:5px;height:5px;float:left'></span>";
                           
            $htmlPdfReport->set('
                            <td style="width:30mm;" class="center fs8" valign="top"><span style="margin-top:3px">'.$imgColor.'</span> '.$value[0].'</td>
                            <td style="width:61mm;" class="center" valign="top">'.Util::wordwrap($value[1], 30, " ", true).'</td>');
            
            if( $c%2 != 0 ){
                $htmlPdfReport->set('</tr>');
            }

            $c++;
        }
        
        if( $c%2 != 0 )
        {
            $htmlPdfReport->set('
                <td></td>
                <td></td>
            </tr>');
        }
    }
    else
    {
        $htmlPdfReport->set("<tr><td class='nobborder w100' style='padding: 5px 0px; text-align:center;'>"._("No Metrics with this criteria")."</td></tr>");
    }
    
    $htmlPdfReport->set('</table>');
        
    $list = null;
    $ids  = $pdf->get_metric_ids(null, $args, $user, null, $conn1);     
    
    foreach($ids as $incident_id) {
        $list[]=$pdf->IncidentNoPDF($incident_id);
    }
        
    $size = count($list);
    $c   = 0;

    if ( $size > 0)
    {
        $htmlPdfReport->pageBreak();
         
        $htmlPdfReport->set('
            <table class="w100 nobborder">
              <tr>
                <th class="w100" style="text-align:center">'.gettext("Detail").'</th>
              </tr>
              </table>
            ');
       
    
        $htmlPdfReport->set('<table class="w100">');

        foreach ($list as $key => $value)
        {
            if( $c%2==0 ){
                $htmlPdfReport->set('<tr>');
            }
    
            $imgStatus = ( $value['Status'] == 'Closed' ) ? '<img src="Tickets/closed.png" width="16" height="16" align="top"/>' : '<img src="Tickets/open.png" width="16" height="16" align="top" />';
                
            $padding  = ( $c%2!=0 ) ? 'padding: 0px 0px 0px 2px; margin: 0px;' : "";
			$td_style = "text-align:left; padding: 0px; font-size: 11px;";
			
							
			$htmlPdfReport->set('
				<td style="width: 93mm; padding: 0px;" valign="top">
					<table style="'.$padding.'; width: 93mm;" align="center">
						<tr>
							<th colspan="4" style="width: 93mm;text-align:center">'.Util::wordwrap($value['Title'], 80, "<br/>", true).'</th>
						</tr>
						<tr>
							<td style="'.$td_style.' width: 23mm;" class="noborder"><strong>'._("Target").':</strong></td>
							<td style="'.$td_style.' width: 37mm;" class="noborder">'.$value['Target'].'</td>
							<td style="'.$td_style.' width: 15mm;" class="noborder"><strong>'._("Metric value").':</strong></td>
							<td style="'.$td_style.' width: 18mm;" class="noborder">'.$value['MetricValue'].'</td>
						</tr>
						<tr>
							<td style="'.$td_style.' width: 23mm;" class="noborder"><strong>'._("Metric type").':</strong></td>
							<td style="'.$td_style.' width: 37mm;" class="noborder">'.$value['MetricType'].'</td>
							<td style="'.$td_style.' width: 15mm;" class="noborder"><strong>'._("Priority").':</strong></td>
							<td style="'.$td_style.' width: 18mm;" class="noborder">'.$value['Priority'].'</td>
						</tr>
						<tr>
							<td style="'.$td_style.' width: 23mm;" class="noborder"><strong>'._("In charge").':</strong></td>
							<td style="'.$td_style.' width: 37mm;" class="noborder">'.Util::wordwrap($value['InCharge'], 35, "<br/>", true).'</td>
							<td style="'.$td_style.' width: 15mm;" class="noborder"><strong>'._("Status").':</strong></td>
							<td style="'.$td_style.' width: 18mm;" class="noborder">'.$imgStatus.' '._($value['Status']).'</td>
						</tr>
					</table>
				</td>');
								

            if( $c%2 != 0 ){
                $htmlPdfReport->set('</tr>');
            }

            $c++;
        }
    
        if( $c%2 != 0 )
        {
            $htmlPdfReport->set('
                <td></td>
            </tr>');
                
        }
        
        $htmlPdfReport->set('</table><br/><br/>');

    }
}

$db->close($conn1);
?>