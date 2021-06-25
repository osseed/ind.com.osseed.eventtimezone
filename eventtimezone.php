<?php

require_once 'eventtimezone.civix.php';
use CRM_Eventtimezone_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function eventtimezone_civicrm_config(&$config) {
  _eventtimezone_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function eventtimezone_civicrm_xmlMenu(&$files) {
  _eventtimezone_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function eventtimezone_civicrm_install() {
  _eventtimezone_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function eventtimezone_civicrm_postInstall() {
  _eventtimezone_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function eventtimezone_civicrm_uninstall() {
  _eventtimezone_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function eventtimezone_civicrm_enable() {
  _eventtimezone_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function eventtimezone_civicrm_disable() {
  _eventtimezone_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function eventtimezone_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _eventtimezone_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function eventtimezone_civicrm_managed(&$entities) {
  _eventtimezone_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function eventtimezone_civicrm_caseTypes(&$caseTypes) {
  _eventtimezone_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function eventtimezone_civicrm_angularModules(&$angularModules) {
  _eventtimezone_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function eventtimezone_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _eventtimezone_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_postProcess().
 */
function eventtimezone_civicrm_postProcess($formName, &$form) {
  if ($formName == 'CRM_Event_Form_ManageEvent_EventInfo') {
    $submit =  $form->getVar('_submitValues');
    $timezone = $submit['timezone'];
    if (empty($form->_id) && !empty($submit['timezone'])) {
      $result = civicrm_api3('Event', 'get', array(
        'sequential' => 1,
        'return' => array("id"),
        'title' => $submit['title'],
        'event_type_id' => $submit['event_type_id'],
        'default_role_id' => $submit['default_role_id'],
      ));
      if ($result['count'] == 1) {
        $event_id = $result['values'][0]['id'];
        $query = "
        UPDATE civicrm_event
        SET timezone = '$timezone'
        WHERE id = $event_id
        ";
        CRM_Core_DAO::executeQuery($query);
      }
    }
    else {
      $event_id = $form->_id;
      $query = "
      UPDATE civicrm_event
      SET timezone = '$timezone'
      WHERE id = $event_id
      ";
      CRM_Core_DAO::executeQuery($query);
    }
  }
}

/**
 * Implements hook_civicrm_apiWrappers().
 */
function eventtimezone_civicrm_entityTypes(&$entityTypes) {
  $entityTypes['CRM_Event_DAO_Event']['fields_callback'][]
    = function ($class, &$fields) {
      $fields['timezone'] = array(
         'name' => 'timezone',
         'type' => CRM_Utils_Type::T_INT,
         'title' => ts('Timezone') ,
         'description' => 'Event Timezone',
         'table_name' => 'civicrm_event',
         'entity' => 'Event',
         'bao' => 'CRM_Event_BAO_Event',
         'localizable' => 0,
       );
    };
}

/**
 * Implements hook_civicrm_alterContent().
 */
function eventtimezone_civicrm_alterContent(&$content, $context, $tplName, &$object) {
  $eventInfoFormContext = ($context == 'form' && $tplName == 'CRM/Event/Form/ManageEvent/EventInfo.tpl');
  $eventInfoPageContext = ($context == 'page' && $tplName == 'CRM/Event/Page/EventInfo.tpl');
  $eventConfirmFormContext = ($context == 'form' && $tplName == 'CRM/Event/Form/Registration/Confirm.tpl');
  $eventConfirmPageContext = ($context == 'form' && $tplName == 'CRM/Event/Form/Registration/ThankYou.tpl');

  if ($eventInfoFormContext || $eventInfoPageContext) {
    $result = civicrm_api3('Event', 'get', array(
      'sequential' => 1,
      'return' => array("timezone"),
      'id' => $object->_id,
    ));
    $timezone = '';
    if (isset($result['values']) && array_key_exists('timezone', $result['values'][0])) {
      $timezone = $result['values'][0]['timezone'];

    if ($eventInfoPageContext && $timezone != '_none' && !empty($timezone)) {
      // Add timezone besides the date data
      $timezone_val = explode(" ",$timezone,-1);
      if(strpos($content, 'AM') !== false){
        $content = str_replace("AM", " AM " .$timezone_val[0], $content);
      }
      if (strpos($content, 'PM') !== false) {
        $content = str_replace("PM", " PM " .$timezone_val[0], $content);
      }
    }
    elseif ($eventInfoFormContext) {
      $result = civicrm_api3('Event', 'get', array(
        'sequential' => 1,
        'return' => array("timezone"),
        'id' => $object->_id,
      ));
      $timezone_identifiers = DateTimeZone::listIdentifiers();
      $timezone_field = '<tr class="crm-event-manage-eventinfo-form-block-timezone">
      <td class="label"><label for="timezone">Timezone</label></td>
      <td>
      <select name="timezone" id="timezone" class="crm-select2">
      <option value="_none">Select Timezone</option>';
      $defaultTz = $result['values'][0]['timezone'];

      foreach ($timezone_identifiers as $key => $value) {
        $dateTime = new DateTime();
        $dateTime->setTimeZone(new DateTimeZone($value));
        $timezone_db = $dateTime->format('T');
        $tzform = $timezone_db." ".$value;
        if($defaultTz == $tzform) {
          $timezone_field .= '<option value="' . $timezone_db . ' '.$value.'" selected>' . $value . '</option>';
        }
        else {
          $timezone_field .= '<option value="' . $timezone_db . ' '.$value.'">' . $value . '</option>';
        }
      }
      $timezone_field .= '</select>
      </td>
      </tr>
      <tr class="crm-event-manage-eventinfo-form-block-max_participants">';
      $content = str_replace('<tr class="crm-event-manage-eventinfo-form-block-max_participants">', $timezone_field, $content);
    }
  }
  elseif ($eventConfirmFormContext || $eventConfirmPageContext) {
    $result = $result = civicrm_api3('Event', 'get', array(
      'sequential' => 1,
      'return' => array("start_date","end_date", "timezone"),
      'id' => $object->_eventId,
    ));
    $event_start_date = $result['values'][0]['event_start_date'];
    $event_end_date = $result['values'][0]['event_end_date'];
    $timezone = $result['values'][0]['timezone'];
    $start_date_con = new DateTime($event_start_date);
    $start_date_st = date_format($start_date_con, 'F jS, Y g:iA');
    $start_date = date_format($start_date_con, 'F jS');

    $end_date_con = new DateTime($event_end_date);
    $end_date_st = date_format($end_date_con, 'F jS, Y g:iA');
    $end_date = date_format($end_date_con, 'F jS');

    $end_date_time = new DateTime($event_end_date);
    $end_time = date_format($end_date_time, 'g:iA');

    if ($timezone != '_none' && !empty($timezone && !empty($event_end_date))) {
      // Add timezone besides the date data
      $timezone_val = explode(" ",$timezone,-1);
      if ($start_date == $end_date) {
        $replacement = "<td width='90%'>" . $start_date_st . " " .  $timezone_val[0] . " through " . $end_time . " " . $timezone_val[0] . "</td>";
        $content = preg_replace('#(<td width="90%">)(.*?)(</td>)#si', $replacement, $content);
      }
      else {
        $replacement = "<td width='90%'>" . $start_date_st . " " .  $timezone_val[0] . " through " . $end_date_st . " " . $timezone_val[0] . "</td>";
        $content = preg_replace('#(<td width="90%">)(.*?)(</td>)#si', $replacement, $content);
      }
    }
    elseif ($timezone != '_none' && !empty($timezone && empty($event_end_date))) {
      $replacement = "<td width='90%'>" . $start_date_st . " " .  $timezone_val[0] . "</td>";
      $content = preg_replace('#(<td width="90%">)(.*?)(</td>)#si', $replacement, $content);
    }
  }
 }
}

/**
 * Implements hook_civicrm_tokens().
 */
function eventtimezone_civicrm_tokens( &$tokens ) {
  $tokens['timezone'] = array(
    'timezone.start_date_timezone' => ts('Event Start Date with timezone'),
    'timezone.end_date_timezone' => ts('Event End Date with timezone'),
  );
}

function eventtimezone_civicrm_tokenValues(&$values, &$cids, $job = null, $tokens = array(), $context = null) {
  if (empty($tokens['timezone'])) {
    return;
  }

  if (!is_array($cids)) {
    return;
  }

  foreach ($cids as $cidkey => $cidvalue) {
    $result = civicrm_api3('Participant', 'get', [
      'sequential' => 1,
      'return' => ["event_id"],
      'contact_id' => $cidvalue,
    ]);

    foreach ($result['values'] as $resultvalue) {
      $event_result = civicrm_api3('Event', 'get', [
        'sequential' => 1,
        'return' => ["timezone", "start_date", "end_date"],
        'id' => $resultvalue['event_id'],
      ]);
    }

    if (!empty($event_result['values'])) {
      $timeZone = $event_result['values'][0]['timezone'];
      // Set default site timezone if event timezone field is not set.
      if ($event_result['values'][0]['timezone'] == '_none') {
        $timeZone = date_default_timezone_get();
      }
      $startDateTimestamp = new DateTime($event_result['values'][0]['event_start_date'], new DateTimeZone($timeZone));
      $startDateTimezoneFormat = date_format($startDateTimestamp, 'M jS Y g:iA T');
      $endDateTimestamp = new DateTime($event_result['values'][0]['event_end_date'], new DateTimeZone($timeZone));
      $endDateTimezoneFormat = date_format($endDateTimestamp, 'M jS Y g:iA T');

      // Set format for start & end date timezone tokens.
      $values[$cidvalue]['timezone.start_date_timezone'] = $startDateTimezoneFormat;
      $values[$cidvalue]['timezone.end_date_timezone'] = $endDateTimezoneFormat;
    }
  }
}
