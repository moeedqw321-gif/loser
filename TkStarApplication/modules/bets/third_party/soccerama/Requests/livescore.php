<?php
require_once Soccerama_dir . 'SocceramaClient.php';
class livescore extends SocceramaClient {
    public function byDate ( $date ) {
        if ( is_object($date) && method_exists($date, 'format') ) {
            $date = $date->format('Y-m-d');
        }
        return $this->callData('fixtures/date/' . $date);
    }
    public function byMatchId ( $matchId ) {
        return $this->call('fixtures/' . $matchId);
    }
    public function today () {
        return $this->callData('livescores');
    }
    public function now () {
        return $this->callData('livescores/inplay');
    }
}
?>
