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


require_once 'av_init.php';

if (Session::menu_perms('dashboard-menu', 'ControlPanelMetrics')) 
{
	$report_name    = $report_data['report_name'];
    $subreport_name = $report_data['subreports'][$subreport_id]['name'];
    $title          = $report_name.' - '.$subreport_name;
    include_once 'general.php';
	
	$pdf        = new Pdf('OSSIM Metrics Report');	
	$query_temp = new ArrayObject();
	
	$htmlPdfReport->pageBreak();
	$htmlPdfReport->setBookmark($title);
	
    $htmlPdfReport->set($htmlPdfReport->newTitle($subreport_name.' - '. _('Last Day (Compromise)'), $date_from, $date_to, NULL));
    
    //User
    $htmlPdfReport->set('<table class="tableTitle w100" style="margin-top:3px;"><tr><td class="w100">'._('Global').'</td></tr></table>');
    $htmlPdfReport->set('<table class="w100">');
            
    $query_temp['dayCompromiseGlobal'] = $pdf->MetricsNoPDF('day', 'compromise', 'global', '', $dates_filter['max_c_date'], $param['user']);
		
    if($query_temp['dayCompromiseGlobal'][1][1] == '') 
    {
        $query_temp['dayCompromiseGlobal'] = array($query_temp['dayCompromiseGlobal'][0]);
    }
     
    $data['asset']  = Session::get_session_user();
    $data['date']   = ' - ' ;
    $data['data']   = $query_temp['dayCompromiseGlobal'];
        
    createTable($data, &$htmlPdfReport);
    
    $htmlPdfReport->set('</table>');    
              
    //Hosts
    if (count($param['hosts']) > 0)
    {
        $htmlPdfReport->set('<table class="tableTitle w100" style="margin-top:5px;"><tr><td class="w100">'._('Hosts').'</td></tr></table>');
        $htmlPdfReport->set('<table class="w100">');
        
        foreach ($param['hosts'] as $h_key => $host_data)
        {        
            $host_ip = $host_data[2];
            
            $data['asset'] = $host_ip;        
            $data['date']  = ' - ' ;                        
           
            $filter = "AND id = '$host_id'";
            $query_temp['dayCompromiseHost'] = $pdf->MetricsNoPDF('day', 'compromise', 'host', $filter, $dates_filter['max_c_date']);
            $data['data'] = $query_temp['dayCompromiseHost'];
            
            createTable($data, &$htmlPdfReport);
        }
        $htmlPdfReport->set('</table>');
    }  
    
    
    //Nets
    if (count($param['nets']) > 0)
    {
        $htmlPdfReport->set('<table class="tableTitle w100" style="margin-top:5px;"><tr><td class="w100">'._('Nets').'</td></tr></table>');
        $htmlPdfReport->set('<table class="w100">');
        
        foreach ($param['nets'] as $net_id => $net_data)
        {
            $data['asset'] = $net_data['ips'];
            $data['date']  = ' - ' ;
             
            $filter = "AND id = '$net_id'";
            $query_temp['dayCompromiseNet'] = $pdf->MetricsNoPDF('day', 'compromise', 'net', $filter, $dates_filter['max_c_date']);
            $data['data'] = $query_temp['dayCompromiseNet'];			
            
            createTable($data, &$htmlPdfReport);
        }
        
        $htmlPdfReport->set('</table>');
    }
        
    
    $htmlPdfReport->pageBreak();   
    
    $htmlPdfReport->set($htmlPdfReport->newTitle($subreport_name.' - '. _('Last Day (Attack)'),$date_from, $date_to, NULL));
    
    
    //User
    $htmlPdfReport->set('<table class="tableTitle w100" style="margin-top:3px;"><tr><td class="w100">'._('Global').'</td></tr></table>');
    $htmlPdfReport->set('<table class="w100">');
    
    $query_temp['dayAttackGlobal'] = $pdf->MetricsNoPDF('day', 'attack', 'global', '', $dates_filter['max_a_date'], $param['user']);
		
    if($query_temp['dayAttackGlobal'][1][1] == '') 
    {
        $query_temp['dayAttackGlobal'] = array($query_temp['dayAttackGlobal'][0]);
    }
    
    $data['asset']  = Session::get_session_user();
    $data['date']   = ' - ' ;
    $data['data']   = $query_temp['dayAttackGlobal'];
    
    createTable($data, &$htmlPdfReport);
        
    $htmlPdfReport->set('</table>');
    
    //Hosts
    if (count($param['hosts']) > 0)
    {	    
        $htmlPdfReport->set('<table class="tableTitle w100" style="margin-top:5px;"><tr><td class="w100">'._('Hosts').'</td></tr></table>');
        $htmlPdfReport->set('<table class="w100">');
          
        	foreach ($param['hosts'] as $host_id => $host_data)
        {       
            $host_ip = $host_data[2];
            
            $data['asset']  = $host_ip;
            $data['date']   = ' - ' ;
                        
            $filter = "AND id = '$host_id'";
            $query_temp['dayAttackHost'] = $pdf->MetricsNoPDF('day', 'attack', 'host', $filter, $dates_filter['max_a_date']);
            $data['data'] = $query_temp['dayAttackHost'];
            
            createTable($data, &$htmlPdfReport);
        }
        
        $htmlPdfReport->set('</table>');
    }
    
    //Nets
    if (count($param['nets']) > 0)
    {
        $htmlPdfReport->set('<table class="tableTitle w100" style="margin-top:5px;"><tr><td class="w100">'._('Nets').'</td></tr></table>');
        $htmlPdfReport->set('<table class="w100">');
        
        foreach ($param['nets'] as $net_id => $net_data)
        {
            $data['asset']  = $net_data['ips'];
            $data['date']   = ' - ' ;
                        
            $filter = "AND id='$net_id'";
            $query_temp['dayAttackNet'] = $pdf->MetricsNoPDF('day', 'attack', 'net', $filter, $dates_filter['max_a_date']);
            $data['data'] = $query_temp['dayAttackNet'];
            
            createTable($data, &$htmlPdfReport);
        }
    	
    	    $htmlPdfReport->set('</table>');
    }
} 
?>