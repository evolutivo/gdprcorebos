<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
/********************************************************************************
*  Module       : Dashboard
*  Language     : Español
*  Version      : 504
*  Created Date : 2007-03-30 Last change : 2007-10-10
*  Author       : Rafael Soler
*  Author       : Francisco Hernandez Odin Consultores www.odin.mx
 ********************************************************************************/

$mod_strings = array(
'LBL_SALES_STAGE_FORM_TITLE'=>'Oportunidades por fase de venta',
'LBL_SALES_STAGE_FORM_DESC'=>'Muestra suma acumulada de oportunidades de negocio por etapa para los usuarios seleccionados con fecha estimada de cierre dentro del tiempo especificado.',
'LBL_MONTH_BY_OUTCOME'=>'Oportunidades por resultados mensuales',
'LBL_MONTH_BY_OUTCOME_DESC'=>'Muestra la suma acumulada por resultados mensuales para los usuarios seleccionados dentro del rango de tiempo especificado. Resultados en base a oportunidades de negocio en estado "Cerrado".',
'LBL_LEAD_SOURCE_FORM_TITLE'=>'Todas las oportunidades por origen de contacto',
'LBL_LEAD_SOURCE_FORM_DESC'=>'Muestra la suma acumulada por origen de contacto para los usuarios seleccionados.',
'LBL_LEAD_SOURCE_BY_OUTCOME'=>'Todas la oportunidades por origen de contacto y resultados',
'LBL_LEAD_SOURCE_BY_OUTCOME_DESC'=>'Muestra la suma acumulada de resultados por origen de contacto, para los usuarios seleccionados, del rango especificado. Resultados en base a oportunidades de negocio en estado "Cerrado".',
'LBL_PIPELINE_FORM_TITLE_DESC'=>'Muestra la suma acumulada por estado de venta de sus oportunidades con fecha estimada de cierre dentro del rango de tiempo especificado.',
'LBL_DATE_RANGE'=>'Rango de tiempo es',
'LBL_DATE_RANGE_TO'=>'a',
'ERR_NO_OPPS'=>'Por favor, genere alguna oportunidad para ver sus gráficos.',
'LBL_TOTAL_PIPELINE'=>'El total de oportunidades es ',
'LBL_ALL_OPPORTUNITIES'=>'La suma total de todas las oportunides es  ',
'LBL_OPP_SIZE'=>'Sumas expresadas en Miles',
'LBL_OPP_SIZE_VALUE'=>'1K',
'NTC_NO_LEGENDS'=>'Ninguna',
'LBL_LEAD_SOURCE_OTHER'=>'Otras',
'LBL_EDIT'=>'Editar',
'LBL_REFRESH'=>'Actualizar',
'LBL_CREATED_ON'=>'Ejecutado por última vez el ',
'LBL_OPPS_IN_STAGE'=>'Oportunidades por el estado de venta',
'LBL_OPPS_IN_LEAD_SOURCE'=>'Oportunidades por origen de los Prospectos',
'LBL_OPPS_OUTCOME'=>'Oportunidades por resultados',
'LBL_USERS'=>'Usuarios:',
'LBL_SALES_STAGES'=>'Fase de venta:',
'LBL_LEAD_SOURCES'=>'Origen de los Prospectos:',
'LBL_DATE_START'=>'Fecha de inicio:',
'LBL_DATE_END'=>'Fecha de fin:',
'LBL_NO_PERMISSION'=>'Su perfil no permite ver la gráfica de este módulo',
'LBL_NO_PERMISSION_FIELD'=>'Su perfil no permite ver la gráfica de este módulo o campo',

'leadsource' => 'Prospectos por Origen',
'leadstatus' => 'Prospectos por Estado',
'leadindustry' => 'Prospectos por Actividad',
'salesbyleadsource' => 'Ventas por Origen de Prospecto',
'salesbyaccount' => 'Ventas por Cuenta',
'salesbyuser' => 'Ventas por Usuario',
'salesbyteam' => 'Ventas por Equipo',
'accountindustry' => 'Cuentas por Actividad',
'productcategory' => 'Productos por Categoría',
'productbyqtyinstock' => 'Productos por Cantidad en stock',
'productbypo' => 'Productos por Órdenes de Compra',
'productbyquotes' => 'Productos por Cotizaciones',
'productbyinvoice' => 'Productos por Facturas',
'sobyaccounts' => 'Pedidos por Cuenta',
'sobystatus' => 'Pedidos por Estado',
'pobystatus' => 'Órdenes de Compra por Cuenta',
'quotesbyaccounts' => 'Cotizaciones por Cuenta',
'quotesbystage' => 'Cotizaciones por Estado',
'invoicebyacnts' => 'Facturas por Cuenta',
'invoicebystatus' => 'Facturas por Estado',
'ticketsbystatus' => 'Tickets por Estado',
'ticketsbypriority' => 'Tickets por Prioridad',
'ticketsbycategory' => 'Tickets por Categoría',
'ticketsbyuser'=>'Tickets por Usuario',
'ticketsbyteam'=>'Tickets por Equipo',
'ticketsbyproduct'=>'Tickets por Producto',
'contactbycampaign'=>'Contactos por Campaña',
'ticketsbyaccount'=>'Tickets por Cuenta',
'ticketsbycontact'=>'Tickets por Contacto',

'LBL_DASHBRD_HOME'=>'Estadísticas',
'LBL_HORZ_BAR_CHART'=>'Gráfico de barras horizontal',
'LBL_VERT_BAR_CHART'=>'Gráfico de barras vertical',
'LBL_PIE_CHART'=>'Gráfico de tarta',
'LBL_NO_DATA'=>'Sin Datos Disponibles',
'DashboardHome'=>'Estadísticas',
'GRIDVIEW'=>'Vista Cuadrícula',
'NORMALVIEW'=>'Vista normal',
'VIEWCHART'=>'Ver Gráfica',
'LBL_DASHBOARD'=>'Estadísticas',

"Approved"=>"Aprobado",
"Created"=>"Creado",
"Cancelled"=>"Cancelado",
"Delivered"=>"Entregado",
"Received Shipment"=>"Envios Recibidos",
"Sent"=>"Enviado",
"Credit Invoice"=>"Factura Crédito",
"Paid"=>"Pagado",
"Un Assigned"=>"Sin Asignar",
"Cold Call"=>"Llamada en Frío",
"Existing Customer"=>"Cliente",
"Self Generated"=>"Autogenerado",
"Employee"=>"Empleado",
"Partner"=>"Socio",
"Public Relations"=>"Relaciones Públicas",
"Direct Mail"=>"Correo Directo",
"Conference"=>"Conferencia",
"Trade Show"=>"Feria",
"Web Site"=>"Página Web",
"Word of mouth"=>"Boca a Boca",
"Other"=>"Otro",
"--None--"=>"Ninguno",
"Attempted to Contact"=>"Intentado Contactar",
"Cold"=>"Frio",
"Contact in Future"=>"Contactar más Adelante",
"Contacted"=>"Contactado",
"Hot"=>"Caliente",
"Junk Lead"=>"Contacto Basura",
"Lost Lead"=>"Contacto Fallido",
"Not Contacted"=>"No Contactado",
"Pre Qualified"=>"Pre Clasificado",
"Qualified"=>"Clasificado",
"Warm"=>"Tibio",
"Apparel"=>"Ropa",
"Banking"=>"Bancos",
"Biotechnology"=>"Biotecnología",
"Chemicals"=>"Química",
"Communications"=>"Comunicaciones",
"Construction"=>"Constructión",
"Consulting"=>"Consultoría",
"Education"=>"Educación",
"Electronics"=>"Electrónica",
"Energy"=>"Energía",
"Engineering"=>"Ingeniería",
"Entertainment"=>"Entretenimiento",
"Environmental"=>"Medio Ambiente",
"Finance"=>"Financiero",
"Food & Beverage"=>"Alimentación",
"Government"=>"Gobierno",
"Healthcare"=>"Salud",
"Hospitality"=>"Hospitalidad",
"Insurance"=>"Seguros",
"Machinery"=>"Maquinaria",
"Manufacturing"=>"Fabricación",
"Media"=>"Media",
"Not For Profit"=>"No Lucrativo",
"Recreation"=>"Recreo",
"Retail"=>"Retail",
"Shipping"=>"Envío",
"Technology"=>"Tecnología",
"Telecommunications"=>"Telecomunicaciones",
"Transportation"=>"Transporte",
"Utilities"=>"Utilidades",
"Hardware"=>"Hardware",
"Software"=>"Software",
"CRM Applications"=>"Aplicaciones de CRM",
"Open"=>"Abierto",
"In Progress"=>"En Progreso",
"Wait For Response"=>"Esperando Respuesta",
"Closed"=>"Cerrado",
"Low"=>"Baja",
"Normal"=>"Normal",
"High"=>"Alta",
"Urgent"=>"Urgente",
"Big Problem"=>"Problema Grave",
"Small Problem"=>"Problema Simple",
"Other Problem"=>"Otro Problema",
"Accepted"=>"Aceptado",
"Rejected"=>"Rechazado",
"Prospecting"=>"Buscando",
"Qualification"=>"Valorando",
"Needs Analysis"=>"Necesita Análisis",
"Value Proposition"=>"Evaluando Propuesta",
"Id. Decision Makers"=>"Identificando Responsable",
"Perception Analysis"=>"Análisis de Percepción",
"Proposal/Price Quote"=>"Propuesta/Cotización",
"Negotiation/Review"=>"Negociando/Revisando",
"Closed Won"=>"Cerrado Ganado",
"Closed Lost"=>"Cerrado Perdido",
);
?>
