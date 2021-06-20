<?php

class CRM_Mydashboardisking_Helper {
  const LEFT_COL_ID = 0;
  const RIGHT_COL_ID = 1;
  private $kingId;
  private $kingLeftDashboard = [];
  private $kingRightDashboard = [];

  public function setDashboard($kingId, $subjectIds) {
    $this->kingId = $kingId;
    $this->loadKingDashboard();

    if ($this->kingHasNoDashboard()) {
      return;
    }

    if ($subjectIds) {
      $targetIds = explode(',', $subjectIds);
    }
    else {
      $targetIds = $this->getAllUserContactIdsButTheKing();
    }

    foreach ($targetIds as $targetId) {
      if ($this->hasDifferentDashboard($targetId)) {
        $this->setContactDashboard($targetId);
      }
    }
  }

  private function loadKingDashboard() {
    $this->kingLeftDashboard = $this->loadDashboardBySide($this->kingId, self::LEFT_COL_ID);
    $this->kingRightDashboard = $this->loadDashboardBySide($this->kingId, self::RIGHT_COL_ID);
  }

  private function loadDashboardBySide($contactId, $side) {
    $dashboardArr = [];

    $result = civicrm_api3('DashboardContact', 'get', [
      'sequential' => 1,
      'contact_id' => $contactId,
      'is_active' => 1,
      'column_no' => $side,
      'options' => ['sort' => 'weight'],
    ]);

    foreach ($result['values'] as $dashboard) {
      $dashboardArr[] = $dashboard['dashboard_id'];
    }

    return $dashboardArr;
  }

  private function kingHasNoDashboard() {
    if (count($this->kingLeftDashboard) == 0 && count($this->kingRightDashboard) == 0) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  private function getAllUserContactIdsButTheKing() {
    $contactIds = [];

    $sql = "select contact_id from civicrm_uf_match where contact_id <> " . $this->kingId;
    $dao = CRM_Core_DAO::executeQuery($sql);
    while ($dao->fetch()) {
      $contactIds[] = $dao->contact_id;
    }

    return $contactIds;
  }

  private function hasDifferentDashboard($targetId) {
    $leftDashboard = $this->loadDashboardBySide($targetId, self::LEFT_COL_ID);
    $rightDashboard = $this->loadDashboardBySide($targetId, self::RIGHT_COL_ID);

    if (!$this->isIdenticalArray($leftDashboard, $this->kingLeftDashboard)) {
      return TRUE;
    }

    if (!$this->isIdenticalArray($rightDashboard, $this->kingRightDashboard)) {
      return TRUE;
    }

    return FALSE;
  }

  private function isIdenticalArray($array1, $array2) {
    // compare the number of elements
    if (count($array1) != count($array2)) {
      return FALSE;
    }

    // compare value by value
    foreach ($array1 as $k => $v) {
      if ($array2[$k] != $v) {
        return FALSE;
      }
    }

    return TRUE;
  }

  private function setContactDashboard($targetId) {
    $this->deleteAllDashboardItems($targetId);
    $this->setContactDashboardBySide($this->kingLeftDashboard, $targetId, self::LEFT_COL_ID);
    $this->setContactDashboardBySide($this->kingRightDashboard, $targetId, self::RIGHT_COL_ID);
  }

  private function deleteAllDashboardItems($contactId) {
    $sql = "delete from civicrm_dashboard_contact where contact_id = $contactId";
    CRM_Core_DAO::executeQuery($sql);
  }

  private function setContactDashboardBySide($sourceDashboard, $targetContactId, $side) {
    for ($i = 0; $i < count($sourceDashboard); $i++) {
      civicrm_api3('DashboardContact', 'create', [
        'sequential' => 1,
        'contact_id' => $targetContactId,
        'dashboard_id' => $sourceDashboard[$i],
        'is_active' => 1,
        'column_no' => $side,
        'weight' => $i + 1,
      ]);
    }
  }
}
