<?php
use CRM_Mydashboardisking_ExtensionUtil as E;

function _civicrm_api3_dashboard_Setfromking_spec(&$spec) {
  $spec['king_id']['api.required'] = 1;
  $spec['subject_ids']['api.required'] = 0;
}

function civicrm_api3_dashboard_Setfromking($params) {
  try {
    $helper = new CRM_Mydashboardisking_Helper();
    $helper->setDashboard($params['king_id'], $params['subject_ids']);

    return civicrm_api3_create_success('OK', $params, 'Dashboard', 'Setfromking');
  }
  catch (Exception $e) {
    throw new API_Exception($e->getMessage(), $e->getCode());
  }
}
