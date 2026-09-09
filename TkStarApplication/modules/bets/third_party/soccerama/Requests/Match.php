<?php
require_once Soccerama_dir . 'SocceramaClient.php';
class SocceramaMatch extends SocceramaClient {
    public function byDate ( $fromDate , $toDate = null ) {
        if ( is_object($fromDate) && method_exists($fromDate, 'format') ) {
            $fromDate = $fromDate->format('Y-m-d');
        }
        if ( $toDate !== null && is_object($toDate) && method_exists($toDate, 'format') ) {
            $toDate = $toDate->format('Y-m-d');
        }
        if ($toDate === null || $toDate === $fromDate) {
            return $this->callData('fixtures/date/' . $fromDate);
        }
        return $this->callData('fixtures/between/' . $fromDate . '/' . $toDate);
    }
    public function byId ( $matchId ) {
        return $this->call('fixtures/' . $matchId);
    }
}
?>
