<?php
if (!defined('Soccerama_dir')) {
    define('Soccerama_dir' , __DIR__ . DIRECTORY_SEPARATOR);
}
require_once Soccerama_dir . 'Requests/Bookmaker.php';
require_once Soccerama_dir . 'Requests/Commentary.php';
require_once Soccerama_dir . 'Requests/Competition.php';
require_once Soccerama_dir . 'Requests/Country.php';
require_once Soccerama_dir . 'Requests/Event.php';
require_once Soccerama_dir . 'Requests/livescore.php';
require_once Soccerama_dir . 'Requests/Match.php';
require_once Soccerama_dir . 'Requests/Odds.php';
require_once Soccerama_dir . 'Requests/Player.php';
require_once Soccerama_dir . 'Requests/Season.php';
require_once Soccerama_dir . 'Requests/Standings.php';
require_once Soccerama_dir . 'Requests/Statistics.php';
require_once Soccerama_dir . 'Requests/Team.php';
require_once Soccerama_dir . 'Requests/TopScorer.php';
require_once Soccerama_dir . 'Requests/Video.php';
class BaseSoccerama {
    protected $apiToken = '';
    public function __construct () {
        $this->CI = & get_instance();
        $token = '';
        if (isset($this->CI->load)) {
            @$this->CI->load->config('sports_api');
            $token = @$this->CI->config->item('api_token', 'sports_api/soccerama');
            if (empty($token)) {
                $token = @$this->CI->config->item('api_token');
            }
        }
        if (empty($token)) {
            $token = 'kSLGrxDaSXfeMh5sb1xSDviFqRNXXtYjjZrL2fpLd39dHf2ibewuzCbqsJSM';
        }
        $this->apiToken = $token;
    }
    public function bookmakers ( $include = array() ) {
        return new Bookmaker($this->apiToken , $include);
    }
    public function commentaries ( $include = array() ) {
        return new Commentary($this->apiToken , $include);
    }
    public function competitions ( $include = array() ) {
        return new Competition($this->apiToken , $include);
    }
    public function countries ( $include = array() ) {
        return new Country($this->apiToken , $include);
    }
    public function events ( $include = array() ) {
        return new Event($this->apiToken , $include);
    }
    public function livescore ( $include = array() ) {
        return new livescore($this->apiToken , $include);
    }
    public function matches ( $include = array() ) {
        return new SocceramaMatch($this->apiToken , $include);
    }
    public function odds () {
        return new Odds($this->apiToken);
    }
    public function players ( $include = array() ) {
        return new Player($this->apiToken , $include);
    }
    public function seasons ( $include = array() ) {
        return new Season($this->apiToken , $include);
    }
    public function statistics ( $include = array() ) {
        return new Statistics($this->apiToken , $include);
    }
    public function standings ( $include = array() ) {
        return new Standings($this->apiToken , $include);
    }
    public function teams ( $include = array() ) {
        return new Team($this->apiToken , $include);
    }
    public function topscorers ( $include = array() ) {
        return new TopScorer($this->apiToken , $include);
    }
    public function videos ( $include = array() ) {
        return new Video($this->apiToken , $include);
    }
}
?>
