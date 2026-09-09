<?php
require_once Soccerama_dir . 'Exceptions/ApiRequestException.php';
class SocceramaClient {
    protected $apiToken;
    protected $withoutData;
    protected $include = array();
    protected $baseUri = 'https://api.sportmonks.com/v3/football/';

    public function __construct ( $apiToken , $include = array() , $withoutData = false ) {
        $this->apiToken = $apiToken;
        if ( empty($this->apiToken) ) {
            $this->apiToken = 'kSLGrxDaSXfeMh5sb1xSDviFqRNXXtYjjZrL2fpLd39dHf2ibewuzCbqsJSM';
        }
        if ( is_array($include) && count($include) ) {
            $this->setInclude($include);
        }
        $this->withoutData = $withoutData;
    }

    protected function httpGet($url) {
        $fullUrl = $this->baseUri . ltrim($url, '/');
        $query = array('api_token' => $this->apiToken);
        if ( !empty($this->include) ) {
            $inc = $this->include;
            $map = array(
                'homeTeam' => 'participants',
                'awayTeam' => 'participants',
                'localTeam' => 'participants',
                'visitorTeam' => 'participants',
                'competition' => 'league',
                'odds' => 'odds',
                'inplay' => 'inplayOdds',
            );
            $parts = is_array($inc) ? $inc : explode(',', (string)$inc);
            $newParts = array();
            foreach ($parts as $p) {
                $p = trim($p);
                if (isset($map[$p])) {
                    $newParts[] = $map[$p];
                } else {
                    $newParts[] = $p;
                }
            }
            $newParts = array_unique($newParts);
            $defaults = array('participants', 'scores', 'state', 'league', 'periods');
            $newParts = array_unique(array_merge($newParts, $defaults));
            $query['include'] = implode(';', $newParts);
        } else {
            $query['include'] = 'participants;scores;state;league;periods;odds;inplayOdds';
        }
        $fullUrl .= (strpos($fullUrl, '?') === false ? '?' : '&') . http_build_query($query);

        $response = false;
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $fullUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 25,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_HTTPHEADER => array('Accept: application/json', 'User-Agent: TkStar/1.0'),
            ));
            $response = curl_exec($ch);
            curl_close($ch);
        }
        if ($response === false || $response === '') {
            $ctx = stream_context_create(array(
                'http' => array('method' => 'GET', 'timeout' => 25, 'header' => "Accept: application/json\r\n", 'ignore_errors' => true),
                'ssl' => array('verify_peer' => false, 'verify_peer_name' => false),
            ));
            $response = @file_get_contents($fullUrl, false, $ctx);
        }
        if ($response === false || $response === '') {
            return '{"data":[]}';
        }
        return $response;
    }

    protected function call($url, $hasData = false){
        $raw = $this->httpGet($url);
        $body = json_decode($raw);
        if ( !is_object($body) ) {
            return json_encode(array('data' => array()));
        }
        if ( property_exists($body , 'error') ) {
            return json_encode(array('data' => array()));
        }
        if (isset($body->data)) {
            $body->data = $this->transformFixtures($body->data);
        }
        if($hasData && $this->withoutData){
            return isset($body->data) ? $body->data : $body;
        }
        return json_encode($body);
    }

    protected function callData ( $url ) {
        return $this->call($url , true);
    }

    public function setInclude ( $include ) {
        if ( is_array($include) ) {
            $include = implode(',' , $include);
        }
        $this->include = $include;
        return $this;
    }

    protected function transformFixtures($fixtures) {
        if (!is_array($fixtures)) {
            $fixtures = array($fixtures);
        }
        $out = array();
        foreach ($fixtures as $f) {
            if (!is_object($f)) continue;
            $out[] = $this->transformOneFixture($f);
        }
        return $out;
    }

    protected function transformOneFixture($f) {
        $home = null;
        $away = null;
        if (isset($f->participants) && is_array($f->participants)) {
            foreach ($f->participants as $p) {
                $meta = isset($p->meta) ? $p->meta : null;
                $loc = is_object($meta) && isset($meta->location) ? strtolower($meta->location) : '';
                if ($loc === 'home') $home = $p;
                elseif ($loc === 'away') $away = $p;
            }
            if (!$home && count($f->participants) > 0) $home = $f->participants[0];
            if (!$away && count($f->participants) > 1) $away = $f->participants[1];
        }

        $localScore = 0;
        $visitorScore = 0;
        $htLocal = null;
        $htVisitor = null;
        if (isset($f->scores) && is_array($f->scores)) {
            foreach ($f->scores as $sc) {
                $desc = isset($sc->description) ? strtoupper($sc->description) : '';
                $part = isset($sc->score) && is_object($sc->score) ? $sc->score : null;
                if (!$part) continue;
                if (in_array($desc, array('CURRENT','2ND_HALF','ET','FT','EXTRA_TIME','PENALTIES'))) {
                    if (isset($part->participant) && strtolower($part->participant) === 'home') $localScore = (int)$part->goals;
                    if (isset($part->participant) && strtolower($part->participant) === 'away') $visitorScore = (int)$part->goals;
                }
                if (in_array($desc, array('1ST_HALF','HT'))) {
                    if (isset($part->participant) && strtolower($part->participant) === 'home') $htLocal = (int)$part->goals;
                    if (isset($part->participant) && strtolower($part->participant) === 'away') $htVisitor = (int)$part->goals;
                }
            }
        }

        $status = 'NS';
        $minute = 0;
        $second = 0;
        if (isset($f->state) && is_object($f->state)) {
            $stateName = isset($f->state->short_name) ? $f->state->short_name : (isset($f->state->name) ? $f->state->name : '');
            $stateName = strtoupper($stateName);
            $mapStatus = array(
                'NS' => 'NS', 'NOT STARTED' => 'NS',
                'INPLAY' => 'LIVE', 'LIVE' => 'LIVE', '1ST HALF' => 'LIVE', '2ND HALF' => 'LIVE',
                'HT' => 'HT', 'HALF TIME' => 'HT',
                'FT' => 'FT', 'FULL TIME' => 'FT', 'AET' => 'FT', 'FT_PEN' => 'FT',
                'POSTPONED' => 'POSTP', 'CANCELLED' => 'CANC', 'SUSPENDED' => 'SUSP',
            );
            $status = isset($mapStatus[$stateName]) ? $mapStatus[$stateName] : $stateName;
        }
        if (isset($f->periods) && is_array($f->periods)) {
            foreach ($f->periods as $per) {
                if (isset($per->ticking) && $per->ticking) {
                    $minute = isset($per->minutes) ? (int)$per->minutes : 0;
                    $second = isset($per->seconds) ? (int)$per->seconds : 0;
                }
            }
        }

        $leagueObj = new stdClass();
        $leagueObj->data = new stdClass();
        if (isset($f->league) && is_object($f->league)) {
            $leagueObj->data->id = isset($f->league->id) ? $f->league->id : null;
            $leagueObj->data->name = isset($f->league->name) ? $f->league->name : '';
        } else {
            $leagueObj->data->id = isset($f->league_id) ? $f->league_id : null;
            $leagueObj->data->name = '';
        }

        $localTeam = new stdClass();
        $localTeam->data = new stdClass();
        if ($home) {
            $localTeam->data->id = isset($home->id) ? $home->id : null;
            $localTeam->data->name = isset($home->name) ? $home->name : '';
            $localTeam->data->logo_path = isset($home->image_path) ? $home->image_path : '';
        }
        $visitorTeam = new stdClass();
        $visitorTeam->data = new stdClass();
        if ($away) {
            $visitorTeam->data->id = isset($away->id) ? $away->id : null;
            $visitorTeam->data->name = isset($away->name) ? $away->name : '';
            $visitorTeam->data->logo_path = isset($away->image_path) ? $away->image_path : '';
        }

        $scores = new stdClass();
        $scores->localteam_score = $localScore;
        $scores->visitorteam_score = $visitorScore;
        $scores->localteam_pen_score = null;
        $scores->visitorteam_pen_score = null;
        $scores->ht_score = ($htLocal !== null && $htVisitor !== null) ? ($htLocal . '-' . $htVisitor) : null;
        $scores->ft_score = $localScore . '-' . $visitorScore;

        $time = new stdClass();
        $time->status = $status;
        $time->starting_at = new stdClass();
        $time->starting_at->date_time = isset($f->starting_at) ? $f->starting_at : null;
        $time->starting_at->date = isset($f->starting_at) ? substr($f->starting_at, 0, 10) : null;
        $time->starting_at->time = isset($f->starting_at) ? substr($f->starting_at, 11, 8) : null;
        $time->starting_at->timestamp = isset($f->starting_at_timestamp) ? $f->starting_at_timestamp : null;
        $time->minute = $minute;
        $time->second = $second;
        $time->added_time = null;
        $time->extra_minute = null;
        $time->injury_time = null;

        $obj = new stdClass();
        $obj->id = isset($f->id) ? $f->id : null;
        $obj->league_id = isset($f->league_id) ? $f->league_id : null;
        $obj->season_id = isset($f->season_id) ? $f->season_id : null;
        $obj->localteam_id = ($home && isset($home->id)) ? $home->id : null;
        $obj->visitorteam_id = ($away && isset($away->id)) ? $away->id : null;
        $obj->scores = $scores;
        $obj->time = $time;
        $obj->localTeam = $localTeam;
        $obj->visitorTeam = $visitorTeam;
        $obj->league = $leagueObj;
        $obj->homeTeam = $localTeam;
        $obj->awayTeam = $visitorTeam;
        $obj->competition = $leagueObj;
        $obj->odds = new stdClass();
        $obj->odds->data = array();
        if (isset($f->odds)) {
            $obj->odds->data = is_array($f->odds) ? $f->odds : (isset($f->odds->data) ? $f->odds->data : array());
        }
        $obj->inplay = new stdClass();
        $obj->inplay->data = array();
        if (isset($f->inplayOdds)) {
            $obj->inplay->data = is_array($f->inplayOdds) ? $f->inplayOdds : (isset($f->inplayOdds->data) ? $f->inplayOdds->data : array());
        }
        return $obj;
    }
}
?>
