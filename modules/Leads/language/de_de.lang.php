<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

if ((isset($_COOKIE['LeadConv']) && $_COOKIE['LeadConv'] == 'true')) {
	$toggle_historicos = 'See Non Converted Leads';
	$toggle_name = 'Converted Leads';
} else {
	$toggle_historicos = 'See Converted Leads';
	$toggle_name = 'Leads';
}

$mod_strings = array(
	'LBL_TGL_HISTORICOS' => $toggle_historicos,
	'LBL_MODULE_NAME'=>$toggle_name,
	'Leads' => $toggle_name,
	'LBL_DIRECT_REPORTS_FORM_NAME'=>'Vorgesetzter',
	'LBL_MODULE_TITLE'=>'Leads: Home',
	'LBL_SEARCH_FORM_TITLE'=>'Lead suchen',
	'LBL_LIST_FORM_TITLE'=>'Leads',
	'LBL_NEW_FORM_TITLE'=>'neuer Lead',
	'LBL_LEAD_OPP_FORM_TITLE'=>'Kontakt-Verkaufspotential:',
	'LBL_LEAD'=>'Lead:',
	'LBL_ADDRESS_INFORMATION'=>'Adresse',
	'LBL_CUSTOM_INFORMATION'=>'zusätzliche Information',

	'LBL_LIST_NAME'=>'Name',
	'LBL_LIST_LAST_NAME'=>'Nachname',
	'LBL_LIST_COMPANY'=>'Organisation',
	'LBL_LIST_WEBSITE'=>'Webseite',
	'LBL_LIST_LEAD_NAME'=>'Lead',
	'LBL_LIST_EMAIL'=>'E-Mail',
	'LBL_LIST_PHONE'=>'Telefon',
	'LBL_LIST_LEAD_ROLE'=>'Rolle',

	'LBL_NAME'=>'Name:',
	'LBL_LEAD_NAME'=>'Lead Name:',
	'LBL_LEAD_INFORMATION'=>'Lead',
	'LBL_FIRST_NAME'=>'Vorname:',
	'LBL_COMPANY'=>'Organisation:',
	'LBL_DESIGNATION'=>'Kennzeichnung:',
	'LBL_PHONE'=>'Telefon:',
	'LBL_LAST_NAME'=>'Nachname:',
	'LBL_MOBILE'=>'Handy:',
	'LBL_EMAIL'=>'E-Mail:',
	'LBL_LEAD_SOURCE'=>'Leadquelle:',
	'LBL_LEAD_STATUS'=>'Leadstatus:',
	'LBL_WEBSITE'=>'Webseite:',
	'LBL_FAX'=>'Fax:',
	'LBL_INDUSTRY'=>'Branche:',
	'LBL_ANNUAL_REVENUE'=>'Jahresumsatz:',
	'LBL_RATING'=>'Bewertung:',
	'LBL_LICENSE_KEY'=>'Lizenzschlüssel:',
	'LBL_NO_OF_EMPLOYEES'=>'Anzahl Angestellte:',
	'LBL_YAHOO_ID'=>'Yahoo E-Mail:',

	'LBL_ADDRESS_STREET'=>'Strasse:',
	'LBL_ADDRESS_POSTAL_CODE'=>'PLZ:',
	'LBL_ADDRESS_CITY'=>'Ort:',
	'LBL_ADDRESS_COUNTRY'=>'Land:',
	'LBL_ADDRESS_STATE'=>'Bundesland:',
	'LBL_ADDRESS'=>'Adresse:',
	'LBL_DESCRIPTION_INFORMATION'=>'weitere Informationen',
	'LBL_DESCRIPTION'=>'Beschreibung:',

	'LBL_CONVERT_LEAD'=>'Lead umwandeln:',
	'LBL_CONVERT_LEAD_INFORMATION'=>'Lead Informationen umwandeln',
	'LBL_ACCOUNT_NAME'=>'Organisation',
	'LBL_POTENTIAL_NAME'=>'Verkaufspotential',
	'LBL_POTENTIAL_CLOSE_DATE'=>'Abschlussdatum',
	'LBL_POTENTIAL_AMOUNT'=>'erwarteter Umsatz für das Potential',
	'LBL_POTENTIAL_SALES_STAGE'=>'Verkaufsstufe des Potentials',

	'NTC_DELETE_CONFIRMATION'=>'Möchten Sie diesen Eintrag löschen?',
	'NTC_REMOVE_CONFIRMATION'=>'Möchten Sie diesen Eintrag wirklich löschen?',
	'NTC_REMOVE_DIRECT_REPORT_CONFIRMATION'=>'Möchten Sie den Vorgesetzten von diesem Eintrag löschen?',
	'NTC_REMOVE_OPP_CONFIRMATION'=>'Möchten Sie diesen Eintrag vom Verkaufspotential löschen?',
	'ERR_DELETE_RECORD'=>"Zum Löschen muss mindestens ein Eintrag markiert sein.",

	'LBL_COLON'=>' : ',
	'LBL_IMPORT_LEADS'=>'importiere Leads',
	'LBL_LEADS_FILE_LIST'=>'Dateiliste Leads',
	'LBL_INSTRUCTIONS'=>'Anleitung',
	'LBL_KINDLY_PROVIDE_AN_XLS_FILE'=>'Bitte eine XLS-Datei angeben',
	'LBL_PROVIDE_ATLEAST_ONE_FILE'=>'Mindestens eine Importdatei angeben',

	'LBL_NONE'=>'keine',
	'LBL_ASSIGNED_TO'=>'zuständig:',
	'LBL_SELECT_LEAD'=>'Lead wählen',
	'LBL_GENERAL_INFORMATION'=>'allgemeine Information',
	'LBL_DO_NOT_CREATE_NEW_POTENTIAL'=>'kein Verkaufspotential bei Umwandlung erstellen',

	'LBL_NEW_POTENTIAL'=>'Neues Verkaufspotential',
	'LBL_POTENTIAL_TITLE'=>'Verkaufspotentiale',

	'LBL_NEW_TASK'=>'neue Aufgabe',
	'LBL_TASK_TITLE'=>'Aufgaben',
	'LBL_NEW_CALL'=>'neuer Anruf',
	'LBL_CALL_TITLE'=>'Anrufe',
	'LBL_NEW_MEETING'=>'neue Besprechung',
	'LBL_MEETING_TITLE'=>'Besprechungen',
	'LBL_NEW_EMAIL'=>'neue E-Mail',
	'LBL_EMAIL_TITLE'=>'E-Mails',
	'LBL_NEW_NOTE'=>'neue Notiz',
	'LBL_NOTE_TITLE'=>'Notizen',
	'LBL_NEW_ATTACHMENT'=>'neuer Anhang',
	'LBL_ATTACHMENT_TITLE'=>'Anhänge',

	'LBL_ALL'=>'alle',
	'LBL_CONTACTED'=>'kontaktiert',
	'LBL_LOST'=>'verloren',
	'LBL_HOT'=>'heiss',
	'LBL_COLD'=>'kalt',

	'LBL_TOOL_FORM_TITLE'=>'Lead: Werkzeuge',

	'Salutation'=>'Anrede',
	'First Name'=>'Vorname',
	'Phone'=>'Telefon',
	'Last Name'=>'Nachname',
	'Mobile'=>'Handy',
	'Company'=>'Organisation',
	'Fax'=>'Fax',
	'Email'=>'E-Mail',
	'Secondary Email'=>'weitere Email',
	'Lead Source'=>'Leadquelle',
	'Website'=>'Webseite',
	'Annual Revenue'=>'Jahresumsatz',
	'Lead Status'=>'Leadstatus',
	'Industry'=>'Branche',
	'Rating'=>'Bewertung',
	'No Of Employees'=>'Anzahl Mitarbeiter',
	'Assigned To'=>'zuständig ',
	'Yahoo Id'=>'Yahoo E-Mail',
	'Created Time'=>'Erstellt',
	'Modified Time'=>'Geändert',
	'Street'=>'Strasse',
	'Postal Code'=>'PLZ',
	'City'=>'Ort',
	'Country'=>'Land',
	'State'=>'Bundesland',
	'Description'=>'Beschreibung',
	'Po Box'=>'Postfach',
	'Campaign Source'=>'Kampagnequelle',
	'Name'=>'Name',
	'LBL_NEW_LEADS'=>'meine neuen Leads',

	//Added for Existing Picklist Entries
	'--None--'=>'--ohne--',
	'Mr.'=>'Sehr geehrter Herr',
	'Ms.'=>'Sehr geehrte Frau',
	'Mrs.'=>'Sehr geehrte Frau',
	'Dr.'=>'Sehr geehrter Herr Dr.',
	'Prof.'=>'Sehr geehrter Herr Prof.',

	'Acquired'=>'erworben',
	'Active'=>'aktiv',
	'Market Failed'=>'Markt verfehlt',
	'Project Cancelled'=>'Projekt abgebrochen',
	'Shutdown'=>'Stillstand',

	'Apparel'=>'Bekleidungsindustrie',
	'Banking'=>'Banken',
	'Biotechnology'=>'Biotechnologie',
	'Chemicals'=>'Chemie',
	'Communications'=>'Kommunikation',
	'Construction'=>'Anlagenbau',
	'Consulting'=>'Beratung',
	'Education'=>'Bildung',
	'Electronics'=>'Elektronik',
	'Energy'=>'Energie',
	'Engineering'=>'Ingenieurwesen',
	'Entertainment'=>'Unterhaltung',
	'Environmental'=>'Umwelt',
	'Finance'=>'Finanzen',
	'Food & Beverage'=>'Nahrungsmittel',
	'Government'=>'Behörde',
	'Healthcare'=>'Gesundheitswesen',
	'Hospitality'=>'Beherbergung',
	'Insurance'=>'Versicherung',
	'Machinery'=>'Maschinen',
	'Manufacturing'=>'Fertigung',
	'Media'=>'Medien',
	'Not For Profit'=>'gemeinnützig',
	'Recreation'=>'Freizeit und Erholung',
	'Retail'=>'Einzelhandel',
	'Shipping'=>'Spedition',
	'Technology'=>'Technologie',
	'Telecommunications'=>'Telekommunikation',
	'Transportation'=>'Transport',
	'Utilities'=>'Versorgungseinrichtung',
	'Other'=>'andere',

	'Cold Call'=>'kalter Anruf',
	'Existing Customer'=>'existierender Kunde',
	'Self Generated'=>'selbst erzeugt',
	'Employee'=>'Mitarbeiter',
	'Partner'=>'Partner',
	'Public Relations'=>'Public Relations',
	'Direct Mail'=>'per Brief',
	'Conference'=>'Konferenz',
	'Trade Show'=>'Messe',
	'Web Site'=>'Web Seite',
	'Word of mouth'=>'Empfehlung',

	'Attempted to Contact'=>'Kontaktierung versucht',
	'Cold'=>'kalt',
	'Contact in Future'=>'in der Zukunft kontaktieren',
	'Contacted'=>'kontaktiert',
	'Hot'=>'heiss',
	'Junk Lead'=>'wertloser Lead',
	'Lost Lead'=>'Lead verloren',
	'Not Contacted'=>'Nicht kontaktiert',
	'Pre Qualified'=>'vorqualifiziert',
	'Qualified'=>'qualifiziert',
	'Warm'=>'warm',

	'Designation'=>'Funktion',

	//Module Sequence Numbering
	'Lead No'=>'Lead Nr.',

	'LBL_TRANSFER_RELATED_RECORDS_TO' => 'Übertrage zugeordnete Datensätze zu',

	'LBL_FOLLOWING_ARE_POSSIBLE_REASONS' => 'Folgende könnten mögliche Gründe sein',
	'LBL_LEADS_FIELD_MAPPING_INCOMPLETE' => 'Alle Pflichtfelder sind nicht zugeordnet',
	'LBL_MANDATORY_FIELDS_ARE_EMPTY' => 'Einige Pflichtfelder sind leer',
	'LBL_LEADS_FIELD_MAPPING' => 'Zuordnung der benutzerdefinierten Lead Felder',
	'LBL_FIELD_SETTINGS' => 'Feldeinstellungen',
	'Leads ID' => 'Leads-ID',
	'LeadAlreadyConverted' => 'Lead cannot be converted. Either it has already been converted or you lack permission on one or more of the destination modules.',
);
?>