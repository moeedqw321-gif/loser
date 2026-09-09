<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Sports API Client Library
 *
 * Centralized HTTP client for sports data APIs.
 *
 * Features:
 * - SportMonks support
 * - Soccerama support
 * - HTTP retry logic
 * - Rate limiting
 * - File caching
 * - Safe JSON decoding
 * - Request logging without exposing API tokens
 * - Generic match normalization
 * - Backward-compatible cache helpers
 */
class Sports_api_client
{
    protected $CI;
    protected $config;
    protected $request_count = 0;
    protected $request_start_time;
    protected $log_handle = null;

    public function __construct()
    {
        $this->CI =& get_instance();

        /*
         * Load configuration safely.
         */
        $this->CI->config->load('sports_api', TRUE);

        $this->config = $this->CI->config->item('sports_api');

        if (!is_array($this->config)) {
            $this->config = [];
        }

        $this->request_start_time = time();
    }

    /**
     * Make an HTTP GET request to SportMonks API.
     */
    public function sportmonks_get($endpoint, $params = [], $use_cache = TRUE, $cache_ttl = 300)
    {
        if (!is_array($params)) {
            $params = [];
        }

        if (
            !isset($this->config['sportmonks']) ||
            !is_array($this->config['sportmonks'])
        ) {
            $this->_log('CONFIG_ERROR', 'sportmonks', 'SportMonks configuration is missing.');
            return FALSE;
        }

        $base_url = isset($this->config['sportmonks']['base_url'])
            ? $this->config['sportmonks']['base_url']
            : '';

        $token = isset($this->config['sportmonks']['api_token'])
            ? $this->config['sportmonks']['api_token']
            : '';

        if ($base_url === '') {
            $this->_log('CONFIG_ERROR', 'sportmonks', 'SportMonks base URL is empty.');
            return FALSE;
        }

        /*
         * Keep existing behavior.
         */
        if ($token !== '') {
            $params['api_token'] = $token;
        }

        $cache_key = $this->_build_cache_key(
            'sportmonks',
            $endpoint,
            $params
        );

        if ($use_cache) {
            $cached = $this->_get_cache($cache_key);

            if ($cached !== FALSE) {
                return $cached;
            }
        }

        $url = rtrim($base_url, '/') . '/' . ltrim($endpoint, '/');

        $result = $this->_make_request(
            $url,
            $params,
            'sportmonks'
        );

        if ($result !== FALSE && $use_cache) {
            $this->_set_cache(
                $cache_key,
                $result,
                max(0, (int)$cache_ttl)
            );
        }

        return $result;
    }

    /**
     * Make an HTTP GET request to Soccerama API.
     */
    public function soccerama_get($endpoint, $params = [], $use_cache = TRUE, $cache_ttl = 300)
    {
        if (!is_array($params)) {
            $params = [];
        }

        if (
            !isset($this->config['soccerama']) ||
            !is_array($this->config['soccerama'])
        ) {
            $this->_log('CONFIG_ERROR', 'soccerama', 'Soccerama configuration is missing.');
            return FALSE;
        }

        $base_url = isset($this->config['soccerama']['base_url'])
            ? $this->config['soccerama']['base_url']
            : '';

        $token = isset($this->config['soccerama']['api_token'])
            ? $this->config['soccerama']['api_token']
            : '';

        if ($base_url === '') {
            $this->_log('CONFIG_ERROR', 'soccerama', 'Soccerama base URL is empty.');
            return FALSE;
        }

        if ($token !== '') {
            $params['api_token'] = $token;
        }

        $cache_key = $this->_build_cache_key(
            'soccerama',
            $endpoint,
            $params
        );

        if ($use_cache) {
            $cached = $this->_get_cache($cache_key);

            if ($cached !== FALSE) {
                return $cached;
            }
        }

        $url = rtrim($base_url, '/') . '/' . ltrim($endpoint, '/');

        $result = $this->_make_request(
            $url,
            $params,
            'soccerama'
        );

        if ($result !== FALSE && $use_cache) {
            $this->_set_cache(
                $cache_key,
                $result,
                max(0, (int)$cache_ttl)
            );
        }

        return $result;
    }

    /**
     * Fetch currently live scores.
     */
    public function get_live_scores($include = ['localTeam', 'visitorTeam', 'odds', 'league', 'inplay'])
    {
        if (is_array($include)) {
            $include_str = implode(',', $include);
        } else {
            $include_str = (string)$include;
        }

        $endpoint = 'livescores/now';

        $result = $this->sportmonks_get(
            $endpoint,
            ['include' => $include_str],
            TRUE,
            30
        );

        return $this->_safe_decode($result);
    }

    /**
     * Fetch a single fixture by ID.
     */
    public function get_fixture_by_id($match_id, $include = ['localTeam', 'visitorTeam', 'odds', 'league'])
    {
        $match_id = rawurlencode((string)$match_id);

        $include_str = is_array($include)
            ? implode(',', $include)
            : (string)$include;

        $endpoint = 'fixtures/' . $match_id;

        $result = $this->sportmonks_get(
            $endpoint,
            ['include' => $include_str],
            TRUE,
            60
        );

        return $this->_safe_decode($result);
    }

    /**
     * Fetch inplay odds for a fixture.
     */
    public function get_inplay_odds($match_id, $include = ['odds', 'localTeam', 'visitorTeam', 'league'])
    {
        $match_id = rawurlencode((string)$match_id);

        $include_str = is_array($include)
            ? implode(',', $include)
            : (string)$include;

        $endpoint = 'odds/inplay/fixture/' . $match_id;

        $result = $this->sportmonks_get(
            $endpoint,
            ['include' => $include_str],
            TRUE,
            30
        );

        return $this->_safe_decode($result);
    }

    /**
     * Fetch matches by date range via Soccerama.
     */
    public function get_matches_by_date(
        $from_date,
        $to_date,
        $include = ['competition', 'homeTeam', 'awayTeam', 'odds']
    ) {
        $include_str = is_array($include)
            ? implode(',', $include)
            : (string)$include;

        $endpoint = 'matches/' .
            rawurlencode((string)$from_date) .
            '/' .
            rawurlencode((string)$to_date);

        $result = $this->soccerama_get(
            $endpoint,
            ['include' => $include_str],
            TRUE,
            300
        );

        return $this->_safe_decode($result);
    }

    /**
     * Fetch livescore by date via Soccerama.
     */
    public function get_livescore_by_date(
        $date,
        $include = ['homeTeam', 'awayTeam', 'odds']
    ) {
        $include_str = is_array($include)
            ? implode(',', $include)
            : (string)$include;

        $endpoint = 'livescore/date/' .
            rawurlencode((string)$date);

        $result = $this->soccerama_get(
            $endpoint,
            ['include' => $include_str],
            TRUE,
            300
        );

        return $this->_safe_decode($result);
    }

    /**
     * Fetch today's livescore via Soccerama.
     */
    public function get_livescore_today(
        $include = ['homeTeam', 'awayTeam', 'odds']
    ) {
        $include_str = is_array($include)
            ? implode(',', $include)
            : (string)$include;

        $result = $this->soccerama_get(
            'livescore',
            ['include' => $include_str],
            TRUE,
            300
        );

        return $this->_safe_decode($result);
    }

    /**
     * Fetch currently live livescore via Soccerama.
     */
    public function get_livescore_now(
        $include = ['homeTeam', 'awayTeam', 'odds']
    ) {
        $include_str = is_array($include)
            ? implode(',', $include)
            : (string)$include;

        $result = $this->soccerama_get(
            'livescore/now',
            ['include' => $include_str],
            TRUE,
            30
        );

        return $this->_safe_decode($result);
    }

    /**
     * Fetch match by ID via Soccerama.
     */
    public function get_match_by_id(
        $match_id,
        $include = ['homeTeam', 'awayTeam', 'odds']
    ) {
        $match_id = rawurlencode((string)$match_id);

        $include_str = is_array($include)
            ? implode(',', $include)
            : (string)$include;

        $endpoint = 'matches/' . $match_id;

        $result = $this->soccerama_get(
            $endpoint,
            ['include' => $include_str],
            TRUE,
            60
        );

        return $this->_safe_decode($result);
    }

    /**
     * Normalize match data.
     */
    public function normalize_match($match, $source = 'sportmonks')
    {
        if (empty($match)) {
            return NULL;
        }

        if (is_array($match)) {
            $match = (object)$match;
        }

        if (!is_object($match)) {
            return NULL;
        }

        $source = strtolower(trim((string)$source));

        if ($source === 'sportmonks') {
            return $this->_normalize_sportmonks_match($match);
        }

        if ($source === 'soccerama') {
            return $this->_normalize_soccerama_match($match);
        }

        return NULL;
    }

    /**
     * Normalize an array/object collection of matches.
     */
    public function normalize_matches($matches, $source = 'sportmonks')
    {
        if (empty($matches)) {
            return [];
        }

        if (is_string($matches)) {
            $matches = $this->_safe_decode($matches);

            if ($matches === NULL) {
                return [];
            }
        }

        $data = isset($matches->data)
            ? $matches->data
            : $matches;

        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        if (!is_array($data)) {
            return [];
        }

        $normalized = [];

        foreach ($data as $match) {
            $norm = $this->normalize_match(
                $match,
                $source
            );

            if (!empty($norm)) {
                $id = isset($norm->id)
                    ? $norm->id
                    : 0;

                if ($id !== 0 && $id !== '0') {
                    $normalized[$id] = $norm;
                } else {
                    $normalized[] = $norm;
                }
            }
        }

        return $normalized;
    }

    /**
     * Check whether a match is live.
     */
    public function is_match_live($match)
    {
        if (empty($match)) {
            return FALSE;
        }

        if (is_array($match)) {
            $match = (object)$match;
        }

        if (!is_object($match)) {
            return FALSE;
        }

        $status = $this->get_match_status($match);
        $minute = $this->get_match_minute($match);

        $live_statuses = [
            'LIVE',
            'HT',
            'ET',
            'BT',
            'P',
            'INT',
            'INPLAY',
            'IN_PLAY',
            '1H',
            '2H',
            'Q1',
            'Q2',
            'Q3',
            'Q4',
            'OT'
        ];

        if (in_array($status, $live_statuses, TRUE)) {
            return TRUE;
        }

        /*
         * Some providers report NS while minute has already started.
         */
        if ($status === 'NS' && $minute > 0 && $minute < 180) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * Check whether a match is finished.
     */
    public function is_match_finished($match)
    {
        if (empty($match)) {
            return FALSE;
        }

        if (is_array($match)) {
            $match = (object)$match;
        }

        if (!is_object($match)) {
            return FALSE;
        }

        $status = $this->get_match_status($match);

        $finished_statuses = [
            'FT',
            'AET',
            'FT_PEN',
            'FINISHED',
            'FINAL',
            'ENDED',
            'COMPLETE',
            'COMPLETED'
        ];

        return in_array($status, $finished_statuses, TRUE);
    }

    /**
     * Check if a match is postponed/cancelled.
     *
     * IMPORTANT:
     * define() calls were intentionally removed from inside
     * the class because they cause the ParseError reported by PHP.
     */
    public function is_match_cancelled($match)
    {
        if (empty($match)) {
            return FALSE;
        }

        if (is_array($match)) {
            $match = (object)$match;
        }

        if (!is_object($match)) {
            return FALSE;
        }

        $status = $this->get_match_status($match);

        $cancelled_statuses = [
            'POSTP',
            'POSTPONED',
            'CANCL',
            'CANCELLED',
            'CANCELED',
            'DELETED',
            'ABAN',
            'ABANDONED',
            'DELAYED',
            'AWARDED'
        ];

        return in_array($status, $cancelled_statuses, TRUE);
    }

    /**
     * Get match scores.
     */
    public function get_match_scores($match)
    {
        if (empty($match)) {
            return [
                'home' => 0,
                'away' => 0
            ];
        }

        if (is_array($match)) {
            $match = (object)$match;
        }

        if (!is_object($match)) {
            return [
                'home' => 0,
                'away' => 0
            ];
        }

        $home = 0;
        $away = 0;

        if (isset($match->scores)) {

            if (is_array($match->scores)) {
                $match->scores = (object)$match->scores;
            }

            if (is_object($match->scores)) {

                if (isset($match->scores->localteam_score)) {
                    $home = (int)$match->scores->localteam_score;
                } elseif (isset($match->scores->home_score)) {
                    $home = (int)$match->scores->home_score;
                }

                if (isset($match->scores->visitorteam_score)) {
                    $away = (int)$match->scores->visitorteam_score;
                } elseif (isset($match->scores->away_score)) {
                    $away = (int)$match->scores->away_score;
                }
            }
        }

        if (
            $home === 0 &&
            $away === 0 &&
            isset($match->localteam_goals)
        ) {
            $home = (int)$match->localteam_goals;

            if (isset($match->visitorteam_goals)) {
                $away = (int)$match->visitorteam_goals;
            }
        }

        if (
            isset($match->home_score) ||
            isset($match->away_score)
        ) {
            if (isset($match->home_score)) {
                $home = (int)$match->home_score;
            }

            if (isset($match->away_score)) {
                $away = (int)$match->away_score;
            }
        }

        return [
            'home' => $home,
            'away' => $away
        ];
    }

    /**
     * Get team names.
     */
    public function get_team_names($match)
    {
        if (empty($match)) {
            return [
                'home' => '',
                'away' => ''
            ];
        }

        if (is_array($match)) {
            $match = (object)$match;
        }

        if (!is_object($match)) {
            return [
                'home' => '',
                'away' => ''
            ];
        }

        $home = '';
        $away = '';

        /*
         * SportMonks.
         */
        if (
            isset($match->localTeam) &&
            is_object($match->localTeam)
        ) {
            if (
                isset($match->localTeam->data) &&
                is_object($match->localTeam->data) &&
                isset($match->localTeam->data->name)
            ) {
                $home = (string)$match->localTeam->data->name;
            } elseif (isset($match->localTeam->name)) {
                $home = (string)$match->localTeam->name;
            }
        }

        if (
            isset($match->visitorTeam) &&
            is_object($match->visitorTeam)
        ) {
            if (
                isset($match->visitorTeam->data) &&
                is_object($match->visitorTeam->data) &&
                isset($match->visitorTeam->data->name)
            ) {
                $away = (string)$match->visitorTeam->data->name;
            } elseif (isset($match->visitorTeam->name)) {
                $away = (string)$match->visitorTeam->name;
            }
        }

        /*
         * Soccerama.
         */
        if ($home === '' && isset($match->homeTeam)) {

            if (
                is_object($match->homeTeam) &&
                isset($match->homeTeam->name)
            ) {
                $home = (string)$match->homeTeam->name;
            } elseif (
                is_object($match->homeTeam) &&
                isset($match->homeTeam->data) &&
                is_object($match->homeTeam->data) &&
                isset($match->homeTeam->data->name)
            ) {
                $home = (string)$match->homeTeam->data->name;
            }
        }

        if ($away === '' && isset($match->awayTeam)) {

            if (
                is_object($match->awayTeam) &&
                isset($match->awayTeam->name)
            ) {
                $away = (string)$match->awayTeam->name;
            } elseif (
                is_object($match->awayTeam) &&
                isset($match->awayTeam->data) &&
                is_object($match->awayTeam->data) &&
                isset($match->awayTeam->data->name)
            ) {
                $away = (string)$match->awayTeam->data->name;
            }
        }

        /*
         * Generic formats.
         */
        if ($home === '' && isset($match->home_name)) {
            $home = (string)$match->home_name;
        }

        if ($away === '' && isset($match->away_name)) {
            $away = (string)$match->away_name;
        }

        if ($home === '' && isset($match->home)) {
            $home = is_object($match->home) && isset($match->home->name)
                ? (string)$match->home->name
                : (string)$match->home;
        }

        if ($away === '' && isset($match->away)) {
            $away = is_object($match->away) && isset($match->away->name)
                ? (string)$match->away->name
                : (string)$match->away;
        }

        return [
            'home' => $home,
            'away' => $away
        ];
    }

    /**
     * Get match status.
     */
    public function get_match_status($match)
    {
        if (empty($match)) {
            return 'UNKNOWN';
        }

        if (is_array($match)) {
            $match = (object)$match;
        }

        if (!is_object($match)) {
            return 'UNKNOWN';
        }

        if (
            isset($match->time) &&
            is_object($match->time) &&
            isset($match->time->status)
        ) {
            return strtoupper(trim((string)$match->time->status));
        }

        if (isset($match->status)) {

            if (is_object($match->status)) {
                if (isset($match->status->type)) {
                    return strtoupper(trim((string)$match->status->type));
                }

                if (isset($match->status->short)) {
                    return strtoupper(trim((string)$match->status->short));
                }

                if (isset($match->status->name)) {
                    return strtoupper(trim((string)$match->status->name));
                }
            }

            return strtoupper(trim((string)$match->status));
        }

        return 'UNKNOWN';
    }

    /**
     * Get match minute.
     */
    public function get_match_minute($match)
    {
        if (empty($match)) {
            return 0;
        }

        if (is_array($match)) {
            $match = (object)$match;
        }

        if (!is_object($match)) {
            return 0;
        }

        if (
            isset($match->time) &&
            is_object($match->time) &&
            isset($match->time->minute)
        ) {
            return (int)$match->time->minute;
        }

        if (isset($match->minute)) {
            return (int)$match->minute;
        }

        if (isset($match->clock)) {

            if (is_numeric($match->clock)) {
                return (int)$match->clock;
            }

            if (is_string($match->clock)) {
                $parts = explode(':', $match->clock);

                if (isset($parts[0]) && is_numeric($parts[0])) {
                    return (int)$parts[0];
                }
            }
        }

        return 0;
    }

    /**
     * Get match second.
     */
    public function get_match_second($match)
    {
        if (empty($match)) {
            return 0;
        }

        if (is_array($match)) {
            $match = (object)$match;
        }

        if (!is_object($match)) {
            return 0;
        }

        if (
            isset($match->time) &&
            is_object($match->time) &&
            isset($match->time->second)
        ) {
            return (int)$match->time->second;
        }

        if (isset($match->second)) {
            return (int)$match->second;
        }

        if (isset($match->clock) && is_string($match->clock)) {
            $parts = explode(':', $match->clock);

            if (
                isset($parts[1]) &&
                is_numeric($parts[1])
            ) {
                return (int)$parts[1];
            }
        }

        return 0;
    }

    /**
     * Get half-time score.
     */
    public function get_ht_score($match)
    {
        if (empty($match)) {
            return [
                'home' => 0,
                'away' => 0
            ];
        }

        if (is_array($match)) {
            $match = (object)$match;
        }

        if (!is_object($match)) {
            return [
                'home' => 0,
                'away' => 0
            ];
        }

        if (
            isset($match->scores) &&
            is_object($match->scores) &&
            isset($match->scores->ht_score)
        ) {
            return $this->_parse_score(
                $match->scores->ht_score
            );
        }

        if (isset($match->ht_score)) {
            return $this->_parse_score(
                $match->ht_score
            );
        }

        return [
            'home' => 0,
            'away' => 0
        ];
    }

    /**
     * Get full-time score.
     */
    public function get_ft_score($match)
    {
        if (empty($match)) {
            return [
                'home' => 0,
                'away' => 0
            ];
        }

        if (is_array($match)) {
            $match = (object)$match;
        }

        if (!is_object($match)) {
            return [
                'home' => 0,
                'away' => 0
            ];
        }

        if (
            isset($match->scores) &&
            is_object($match->scores) &&
            isset($match->scores->ft_score)
        ) {
            return $this->_parse_score(
                $match->scores->ft_score
            );
        }

        if (isset($match->ft_score)) {
            return $this->_parse_score(
                $match->ft_score
            );
        }

        return $this->get_match_scores($match);
    }

    /**
     * Get league name.
     */
    public function get_league_name($match)
    {
        if (empty($match)) {
            return 'Unknown';
        }

        if (is_array($match)) {
            $match = (object)$match;
        }

        if (!is_object($match)) {
            return 'Unknown';
        }

        if (
            isset($match->league) &&
            is_object($match->league)
        ) {
            if (
                isset($match->league->data) &&
                is_object($match->league->data) &&
                isset($match->league->data->name)
            ) {
                return (string)$match->league->data->name;
            }

            if (isset($match->league->name)) {
                return (string)$match->league->name;
            }
        }

        if (isset($match->league_name)) {
            return (string)$match->league_name;
        }

        if (
            isset($match->competition) &&
            is_object($match->competition) &&
            isset($match->competition->name)
        ) {
            return (string)$match->competition->name;
        }

        return 'Unknown';
    }

    /**
     * Get competition name.
     */
    public function get_competition_name($match)
    {
        if (empty($match)) {
            return 'Unknown';
        }

        if (is_array($match)) {
            $match = (object)$match;
        }

        if (!is_object($match)) {
            return 'Unknown';
        }

        if (
            isset($match->competition) &&
            is_object($match->competition) &&
            isset($match->competition->name)
        ) {
            return (string)$match->competition->name;
        }

        return $this->get_league_name($match);
    }

    /**
     * Get match start time.
     */
    public function get_start_time($match)
    {
        if (empty($match)) {
            return '';
        }

        if (is_array($match)) {
            $match = (object)$match;
        }

        if (!is_object($match)) {
            return '';
        }

        if (
            isset($match->time) &&
            is_object($match->time)
        ) {
            if (
                isset($match->time->starting_at) &&
                is_object($match->time->starting_at)
            ) {
                if (isset($match->time->starting_at->date_time)) {
                    return (string)$match->time->starting_at->date_time;
                }

                if (isset($match->time->starting_at->date)) {
                    return (string)$match->time->starting_at->date;
                }
            }

            if (isset($match->time->starting_at)) {
                return (string)$match->time->starting_at;
            }
        }

        if (isset($match->starting_at)) {
            return (string)$match->starting_at;
        }

        if (isset($match->start_time)) {
            return (string)$match->start_time;
        }

        return '';
    }

    /**
     * Extract odds for a bookmaker and type.
     */
    public function get_odds_for_match(
        $match,
        $bookmaker_id = null,
        $type = '1x2'
    ) {
        if (empty($match)) {
            return [];
        }

        if (is_array($match)) {
            $match = (object)$match;
        }

        if (!is_object($match)) {
            return [];
        }

        $odds = [];

        if (
            !isset($match->odds) ||
            !is_object($match->odds)
        ) {
            return [];
        }

        if (
            !isset($match->odds->data) ||
            !is_array($match->odds->data)
        ) {
            return [];
        }

        foreach ($match->odds->data as $bookmaker) {

            if (is_array($bookmaker)) {
                $bookmaker = (object)$bookmaker;
            }

            if (!is_object($bookmaker)) {
                continue;
            }

            if (
                $bookmaker_id !== null &&
                isset($bookmaker->bookmaker_id) &&
                (string)$bookmaker->bookmaker_id !== (string)$bookmaker_id
            ) {
                continue;
            }

            if (
                !isset($bookmaker->types) ||
                !is_object($bookmaker->types) ||
                !isset($bookmaker->types->data) ||
                !is_array($bookmaker->types->data)
            ) {
                continue;
            }

            foreach ($bookmaker->types->data as $type_data) {

                if (is_array($type_data)) {
                    $type_data = (object)$type_data;
                }

                if (!is_object($type_data)) {
                    continue;
                }

                if (
                    $type !== null &&
                    isset($type_data->type) &&
                    (string)$type_data->type !== (string)$type
                ) {
                    continue;
                }

                if (
                    isset($type_data->odds) &&
                    is_object($type_data->odds) &&
                    isset($type_data->odds->data)
                ) {
                    $type_odds = $type_data->odds->data;

                    if (is_array($type_odds)) {
                        foreach ($type_odds as $odd) {
                            $odds[] = $odd;
                        }
                    }
                }
            }
        }

        return $odds;
    }

    /**
     * Get inplay odds.
     */
    public function get_inplay_odds_data($match)
    {
        if (empty($match)) {
            return NULL;
        }

        if (is_array($match)) {
            $match = (object)$match;
        }

        if (!is_object($match)) {
            return NULL;
        }

        if (
            isset($match->inplay) &&
            is_object($match->inplay) &&
            isset($match->inplay->data) &&
            !empty($match->inplay->data)
        ) {
            return $match->inplay->data;
        }

        if (isset($match->inplay) && !empty($match->inplay)) {
            return $match->inplay;
        }

        return NULL;
    }

    /**
     * Normalize SportMonks match.
     */
    protected function _normalize_sportmonks_match($match)
    {
        $norm = new stdClass();

        $norm->id = isset($match->id)
            ? $match->id
            : 0;

        $norm->sport_id = isset($match->sport_id)
            ? $match->sport_id
            : (
                isset($match->sport_id)
                    ? $match->sport_id
                    : 1
            );

        $teams = $this->get_team_names($match);

        $norm->home_team = $teams['home'];
        $norm->away_team = $teams['away'];

        $scores = $this->get_match_scores($match);

        $norm->home_score = $scores['home'];
        $norm->away_score = $scores['away'];

        $ht = $this->get_ht_score($match);

        $norm->ht_home_score = $ht['home'];
        $norm->ht_away_score = $ht['away'];

        $ft = $this->get_ft_score($match);

        $norm->ft_home_score = $ft['home'];
        $norm->ft_away_score = $ft['away'];

        $norm->status = $this->get_match_status($match);
        $norm->minute = $this->get_match_minute($match);
        $norm->second = $this->get_match_second($match);
        $norm->league = $this->get_league_name($match);
        $norm->start_time = $this->get_start_time($match);

        $norm->home_score_ft = $norm->ft_home_score;
        $norm->away_score_ft = $norm->ft_away_score;

        $norm->ft_score =
            $norm->ft_home_score . '-' .
            $norm->ft_away_score;

        $norm->ht_score =
            $norm->ht_home_score . '-' .
            $norm->ht_away_score;

        $norm->is_live = $this->is_match_live($match);
        $norm->is_finished = $this->is_match_finished($match);
        $norm->is_cancelled = $this->is_match_cancelled($match);

        $norm->raw = $match;

        return $norm;
    }

    /**
     * Normalize Soccerama match.
     */
    protected function _normalize_soccerama_match($match)
    {
        $norm = new stdClass();

        $norm->id = isset($match->id)
            ? $match->id
            : 0;

        $norm->sport_id = isset($match->sport_id)
            ? $match->sport_id
            : 1;

        $teams = $this->get_team_names($match);

        $norm->home_team = $teams['home'];
        $norm->away_team = $teams['away'];

        $norm->home_score = isset($match->home_score)
            ? (int)$match->home_score
            : 0;

        $norm->away_score = isset($match->away_score)
            ? (int)$match->away_score
            : 0;

        /*
         * Alternative Soccerama score fields.
         */
        if (
            $norm->home_score === 0 &&
            isset($match->localteam_goals)
        ) {
            $norm->home_score = (int)$match->localteam_goals;
        }

        if (
            $norm->away_score === 0 &&
            isset($match->visitorteam_goals)
        ) {
            $norm->away_score = (int)$match->visitorteam_goals;
        }

        $norm->status = isset($match->status)
            ? strtoupper((string)$match->status)
            : 'UNKNOWN';

        $norm->minute = isset($match->minute)
            ? (int)$match->minute
            : 0;

        $norm->second = isset($match->second)
            ? (int)$match->second
            : 0;

        $norm->league = $this->get_competition_name($match);

        $norm->start_time = isset($match->time)
            ? (
                is_scalar($match->time)
                    ? (string)$match->time
                    : ''
            )
            : '';

        if (isset($match->ft_score)) {
            $norm->ft_score = (string)$match->ft_score;
        } else {
            $norm->ft_score =
                $norm->home_score . '-' .
                $norm->away_score;
        }

        if (isset($match->ht_score)) {
            $norm->ht_score = (string)$match->ht_score;
        } else {
            $norm->ht_score = '0-0';
        }

        $ft_parts = $this->_parse_score($norm->ft_score);

        $norm->ft_home_score = $ft_parts['home'];
        $norm->ft_away_score = $ft_parts['away'];

        $norm->home_score_ft = $norm->ft_home_score;
        $norm->away_score_ft = $norm->ft_away_score;

        $ht_parts = $this->_parse_score($norm->ht_score);

        $norm->ht_home_score = $ht_parts['home'];
        $norm->ht_away_score = $ht_parts['away'];

        $norm->is_live = $this->is_match_live($match);
        $norm->is_finished = $this->is_match_finished($match);
        $norm->is_cancelled = $this->is_match_cancelled($match);

        $norm->raw = $match;

        return $norm;
    }

    /**
     * Make HTTP request with retry logic.
     */
    protected function _make_request($url, $params, $provider)
    {
        if (!$this->_check_rate_limit()) {
            $this->_log(
                'RATE_LIMIT',
                $provider,
                'Rate limit exceeded.'
            );

            return FALSE;
        }

        $this->request_count++;

        $provider_config = isset($this->config[$provider]) &&
            is_array($this->config[$provider])
            ? $this->config[$provider]
            : [];

        $retry_count = isset($provider_config['retry_count'])
            ? (int)$provider_config['retry_count']
            : 2;

        $timeout = isset($provider_config['timeout'])
            ? (int)$provider_config['timeout']
            : 15;

        $retry_delay = isset($provider_config['retry_delay'])
            ? (int)$provider_config['retry_delay']
            : 1000;

        $retry_count = max(0, $retry_count);
        $timeout = max(1, $timeout);
        $retry_delay = max(0, $retry_delay);

        if (!function_exists('curl_init')) {
            $this->_log(
                'CURL_ERROR',
                $provider,
                'cURL extension is not enabled.'
            );

            return FALSE;
        }

        if (!is_array($params)) {
            $params = [];
        }

        /*
         * Avoid malformed duplicate ?.
         */
        $query = http_build_query(
            $params,
            '',
            '&',
            PHP_QUERY_RFC3986
        );

        $full_url = $url;

        if ($query !== '') {
            $full_url .=
                (strpos($url, '?') === FALSE ? '?' : '&') .
                $query;
        }

        for (
            $attempt = 0;
            $attempt <= $retry_count;
            $attempt++
        ) {
            $start = microtime(TRUE);

            $ch = curl_init();

            curl_setopt_array(
                $ch,
                [
                    CURLOPT_URL => $full_url,
                    CURLOPT_RETURNTRANSFER => TRUE,
                    CURLOPT_TIMEOUT => $timeout,
                    CURLOPT_CONNECTTIMEOUT => min(5, $timeout),
                    CURLOPT_FOLLOWLOCATION => TRUE,
                    CURLOPT_MAXREDIRS => 3,
                    CURLOPT_HTTPHEADER => [
                        'Accept: application/json',
                        'User-Agent: TkStar/1.0'
                    ]
                ]
            );

            /*
             * Preserve compatibility with local XAMPP
             * environments while allowing config override.
             */
            $verify_ssl = TRUE;

            if (isset($provider_config['verify_ssl'])) {
                $verify_ssl = (bool)$provider_config['verify_ssl'];
            }

            curl_setopt(
                $ch,
                CURLOPT_SSL_VERIFYPEER,
                $verify_ssl
            );

            curl_setopt(
                $ch,
                CURLOPT_SSL_VERIFYHOST,
                $verify_ssl ? 2 : 0
            );

            $response = curl_exec($ch);

            $http_code = (int)curl_getinfo(
                $ch,
                CURLINFO_HTTP_CODE
            );

            $error = curl_error($ch);

            $elapsed = round(
                (microtime(TRUE) - $start) * 1000
            );

            curl_close($ch);

            if ($error !== '') {
                $this->_log(
                    'CURL_ERROR',
                    $provider,
                    'Attempt ' .
                    $attempt .
                    ': ' .
                    $error .
                    ' | URL: ' .
                    $this->_sanitize_url($full_url)
                );

                if ($attempt < $retry_count) {
                    if ($retry_delay > 0) {
                        usleep(
                            $retry_delay *
                            1000 *
                            ($attempt + 1)
                        );
                    }

                    continue;
                }

                return FALSE;
            }

            if (
                $http_code >= 200 &&
                $http_code < 300
            ) {
                $this->_log(
                    'SUCCESS',
                    $provider,
                    'HTTP ' .
                    $http_code .
                    ' | ' .
                    $elapsed .
                    'ms | ' .
                    $this->_sanitize_url($full_url)
                );

                return $response;
            }

            /*
             * Rate limit.
             */
            if ($http_code === 429) {

                $this->_log(
                    'RATE_LIMITED',
                    $provider,
                    'HTTP 429 | ' .
                    $this->_sanitize_url($full_url)
                );

                if ($attempt < $retry_count) {
                    sleep(2 * ($attempt + 1));
                    continue;
                }

                return FALSE;
            }

            /*
             * Client-side errors.
             */
            if (
                $http_code >= 400 &&
                $http_code < 500
            ) {
                $this->_log(
                    'CLIENT_ERROR',
                    $provider,
                    'HTTP ' .
                    $http_code .
                    ' | ' .
                    $this->_sanitize_url($full_url)
                );

                return FALSE;
            }

            /*
             * Server-side errors.
             */
            if ($http_code >= 500) {

                $this->_log(
                    'SERVER_ERROR',
                    $provider,
                    'HTTP ' .
                    $http_code .
                    ' | Attempt ' .
                    $attempt .
                    ' | ' .
                    $this->_sanitize_url($full_url)
                );

                if ($attempt < $retry_count) {
                    sleep($attempt + 1);
                    continue;
                }

                return FALSE;
            }

            /*
             * Empty HTTP status.
             */
            if ($http_code === 0) {

                $this->_log(
                    'HTTP_ERROR',
                    $provider,
                    'No HTTP status received | ' .
                    $this->_sanitize_url($full_url)
                );

                if ($attempt < $retry_count) {
                    if ($retry_delay > 0) {
                        usleep(
                            $retry_delay *
                            1000 *
                            ($attempt + 1)
                        );
                    }

                    continue;
                }

                return FALSE;
            }

            $this->_log(
                'UNKNOWN_STATUS',
                $provider,
                'HTTP ' .
                $http_code .
                ' | ' .
                $this->_sanitize_url($full_url)
            );

            return FALSE;
        }

        return FALSE;
    }

    /**
     * Safe JSON decode.
     */
    protected function _safe_decode($json_string)
    {
        if (
            $json_string === NULL ||
            $json_string === FALSE ||
            $json_string === ''
        ) {
            return NULL;
        }

        if (
            is_object($json_string) ||
            is_array($json_string)
        ) {
            return $json_string;
        }

        if (!is_string($json_string)) {
            return NULL;
        }

        $decoded = json_decode($json_string);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $error_message = function_exists('json_last_error_msg')
                ? json_last_error_msg()
                : 'JSON decode error';

            $this->_log(
                'JSON_ERROR',
                'parser',
                $error_message
            );

            return NULL;
        }

        return $decoded;
    }

    /**
     * Build cache key.
     */
    protected function _build_cache_key(
        $provider,
        $endpoint,
        $params
    ) {
        if (!is_array($params)) {
            $params = [];
        }

        /*
         * Sort parameters to make equivalent requests
         * generate the same cache key.
         */
        ksort($params);

        $query = http_build_query(
            $params,
            '',
            '&',
            PHP_QUERY_RFC3986
        );

        return $provider .
            '_' .
            md5(
                (string)$endpoint .
                '|' .
                $query
            );
    }

    /**
     * Get from cache.
     */
    protected function _get_cache($key)
    {
        $cache_base = isset($this->config['cache_dir'])
            ? $this->config['cache_dir']
            : '';

        if ($cache_base === '') {
            return FALSE;
        }

        $cache_dir =
            rtrim($cache_base, '/\\') .
            DIRECTORY_SEPARATOR .
            'cache';

        $cache_file =
            $cache_dir .
            DIRECTORY_SEPARATOR .
            $key .
            '.json';

        if (!is_file($cache_file)) {
            return FALSE;
        }

        $content = @file_get_contents($cache_file);

        if ($content === FALSE || $content === '') {
            return FALSE;
        }

        $cached = json_decode($content);

        if (
            !is_object($cached) ||
            !isset($cached->expires)
        ) {
            @unlink($cache_file);
            return FALSE;
        }

        if ((int)$cached->expires < time()) {
            @unlink($cache_file);
            return FALSE;
        }

        return isset($cached->data)
            ? $cached->data
            : FALSE;
    }

    /**
     * Set cache.
     */
    protected function _set_cache(
        $key,
        $data,
        $ttl
    ) {
        $cache_base = isset($this->config['cache_dir'])
            ? $this->config['cache_dir']
            : '';

        if ($cache_base === '') {
            return FALSE;
        }

        $cache_dir =
            rtrim($cache_base, '/\\') .
            DIRECTORY_SEPARATOR .
            'cache';

        if (!is_dir($cache_dir)) {
            if (!@mkdir($cache_dir, 0755, TRUE)) {
                return FALSE;
            }
        }

        $cache_file =
            $cache_dir .
            DIRECTORY_SEPARATOR .
            $key .
            '.json';

        $cache_obj = new stdClass();

        $cache_obj->expires =
            time() +
            max(0, (int)$ttl);

        if (is_string($data)) {
            $decoded = json_decode($data);

            if (
                json_last_error() === JSON_ERROR_NONE
            ) {
                $cache_obj->data = $decoded;
            } else {
                $cache_obj->data = $data;
            }
        } else {
            $cache_obj->data = $data;
        }

        $encoded = json_encode(
            $cache_obj,
            JSON_UNESCAPED_UNICODE
        );

        if ($encoded === FALSE) {
            return FALSE;
        }

        return @file_put_contents(
            $cache_file,
            $encoded,
            LOCK_EX
        );
    }

    /**
     * Clear all cache.
     */
    public function clear_cache()
    {
        $cache_base = isset($this->config['cache_dir'])
            ? $this->config['cache_dir']
            : '';

        if ($cache_base === '') {
            return;
        }

        $cache_dir =
            rtrim($cache_base, '/\\') .
            DIRECTORY_SEPARATOR .
            'cache';

        if (!is_dir($cache_dir)) {
            return;
        }

        $files = glob(
            $cache_dir .
            DIRECTORY_SEPARATOR .
            '*.json'
        );

        if (!is_array($files)) {
            return;
        }

        foreach ($files as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }
    }

    /**
     * Clear cache for a provider.
     */
    public function clear_cache_provider($provider)
    {
        $cache_base = isset($this->config['cache_dir'])
            ? $this->config['cache_dir']
            : '';

        if ($cache_base === '') {
            return;
        }

        $cache_dir =
            rtrim($cache_base, '/\\') .
            DIRECTORY_SEPARATOR .
            'cache';

        if (!is_dir($cache_dir)) {
            return;
        }

        $provider = preg_replace(
            '/[^a-zA-Z0-9_-]/',
            '',
            (string)$provider
        );

        $files = glob(
            $cache_dir .
            DIRECTORY_SEPARATOR .
            $provider .
            '_*.json'
        );

        if (!is_array($files)) {
            return;
        }

        foreach ($files as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }
    }

    /**
     * Check rate limit.
     */
    protected function _check_rate_limit()
    {
        $rate_config =
            isset($this->config['rate_limit']) &&
            is_array($this->config['rate_limit'])
                ? $this->config['rate_limit']
                : [];

        $per_seconds = isset(
            $rate_config['per_seconds']
        )
            ? (int)$rate_config['per_seconds']
            : 60;

        $max_requests = isset(
            $rate_config['max_requests']
        )
            ? (int)$rate_config['max_requests']
            : 60;

        $per_seconds = max(1, $per_seconds);
        $max_requests = max(1, $max_requests);

        $elapsed =
            time() -
            $this->request_start_time;

        if ($elapsed >= $per_seconds) {
            $this->request_count = 0;
            $this->request_start_time = time();

            return TRUE;
        }

        return $this->request_count < $max_requests;
    }

    /**
     * Sanitize URL for logging.
     *
     * API tokens are never written to logs.
     */
    protected function _sanitize_url($url)
    {
        if (!is_string($url)) {
            return '';
        }

        /*
         * Query parameter token names.
         */
        $patterns = [
            '/([?&]api_token=)[^&]*/i',
            '/([?&]token=)[^&]*/i',
            '/([?&]access_token=)[^&]*/i',
            '/([?&]key=)[^&]*/i',
            '/([?&]api_key=)[^&]*/i'
        ];

        foreach ($patterns as $pattern) {
            $url = preg_replace(
                $pattern,
                '$1***',
                $url
            );
        }

        return $url;
    }

    /**
     * Log API request.
     */
    protected function _log(
        $level,
        $provider,
        $message
    ) {
        if (
            !isset($this->config['log_enabled']) ||
            !$this->config['log_enabled']
        ) {
            return;
        }

        $log_file = isset(
            $this->config['log_file']
        )
            ? $this->config['log_file']
            : '';

        if ($log_file === '') {
            return;
        }

        $log_dir = dirname($log_file);

        if (!is_dir($log_dir)) {
            @mkdir(
                $log_dir,
                0755,
                TRUE
            );
        }

        $max_log_size = isset(
            $this->config['max_log_size']
        )
            ? (int)$this->config['max_log_size']
            : 5242880;

        if (
            is_file($log_file) &&
            filesize($log_file) > $max_log_size
        ) {
            @unlink($log_file);
        }

        $timestamp = date(
            'Y-m-d H:i:s'
        );

        $safe_message = $this->_sanitize_url(
            (string)$message
        );

        $line =
            '[' .
            $timestamp .
            '] [' .
            strtoupper((string)$level) .
            '] [' .
            (string)$provider .
            '] ' .
            $safe_message .
            PHP_EOL;

        @file_put_contents(
            $log_file,
            $line,
            FILE_APPEND | LOCK_EX
        );
    }

    /**
     * Write match data to file cache.
     *
     * Backward compatible.
     */
    public function write_match_cache(
        $file_path,
        $data
    ) {
        $cache_base = isset(
            $this->config['cache_dir']
        )
            ? $this->config['cache_dir']
            : '';

        if ($cache_base === '') {
            return FALSE;
        }

        /*
         * Normalize separators.
         */
        $file_path = str_replace(
            ['/', '\\'],
            DIRECTORY_SEPARATOR,
            (string)$file_path
        );

        $file_path = ltrim(
            $file_path,
            DIRECTORY_SEPARATOR
        );

        $full_path =
            rtrim(
                $cache_base,
                '/\\'
            ) .
            DIRECTORY_SEPARATOR .
            $file_path;

        $dir = dirname($full_path);

        if (!is_dir($dir)) {
            @mkdir(
                $dir,
                0755,
                TRUE
            );
        }

        if (is_string($data)) {
            $content = $data;
        } else {
            $content = json_encode(
                $data,
                JSON_UNESCAPED_UNICODE
            );
        }

        if ($content === FALSE) {
            return FALSE;
        }

        return @file_put_contents(
            $full_path,
            $content,
            LOCK_EX
        );
    }

    /**
     * Read match data from file cache.
     *
     * Backward compatible.
     */
    public function read_match_cache(
        $file_path
    ) {
        $cache_base = isset(
            $this->config['cache_dir']
        )
            ? $this->config['cache_dir']
            : '';

        if ($cache_base === '') {
            return FALSE;
        }

        $file_path = str_replace(
            ['/', '\\'],
            DIRECTORY_SEPARATOR,
            (string)$file_path
        );

        $file_path = ltrim(
            $file_path,
            DIRECTORY_SEPARATOR
        );

        $full_path =
            rtrim(
                $cache_base,
                '/\\'
            ) .
            DIRECTORY_SEPARATOR .
            $file_path;

        if (!is_file($full_path)) {
            return FALSE;
        }

        return @file_get_contents(
            $full_path
        );
    }

    /**
     * Validate match data.
     */
    public function validate_match_data($match)
    {
        if (empty($match)) {
            return FALSE;
        }

        if (is_array($match)) {
            $match = (object)$match;
        }

        if (!is_object($match)) {
            return FALSE;
        }

        if (
            !isset($match->id) ||
            $match->id === '' ||
            $match->id === NULL
        ) {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Get odd value safely.
     */
    public function get_odd_value(
        $odds,
        $index,
        $default = 0
    ) {
        if (empty($odds)) {
            return $default;
        }

        if (is_object($odds)) {
            $odds = get_object_vars($odds);
        }

        if (!is_array($odds)) {
            return $default;
        }

        if (!isset($odds[$index])) {
            return $default;
        }

        $odd = $odds[$index];

        if (is_array($odd)) {
            $odd = (object)$odd;
        }

        if (
            is_object($odd) &&
            isset($odd->value) &&
            is_numeric($odd->value)
        ) {
            return (float)$odd->value;
        }

        if (is_numeric($odd)) {
            return (float)$odd;
        }

        if (
            is_object($odd) &&
            isset($odd->odd) &&
            is_numeric($odd->odd)
        ) {
            return (float)$odd->odd;
        }

        return $default;
    }

    /**
     * Cap odd value at maximum.
     */
    public function cap_odd(
        $value,
        $max = 75.00
    ) {
        $value = (float)$value;
        $max = (float)$max;

        if ($max < 0) {
            $max = 0;
        }

        if ($value > $max) {
            return $max;
        }

        if ($value < 0) {
            return 0;
        }

        return $value;
    }

    /**
     * Parse a score such as:
     * 2-1
     * 2 : 1
     * 2–1
     */
    protected function _parse_score($score)
    {
        $result = [
            'home' => 0,
            'away' => 0
        ];

        if (
            $score === NULL ||
            $score === ''
        ) {
            return $result;
        }

        if (is_array($score)) {
            if (isset($score['home'])) {
                $result['home'] = (int)$score['home'];
            }

            if (isset($score['away'])) {
                $result['away'] = (int)$score['away'];
            }

            return $result;
        }

        if (is_object($score)) {
            if (isset($score->home)) {
                $result['home'] = (int)$score->home;
            }

            if (isset($score->away)) {
                $result['away'] = (int)$score->away;
            }

            return $result;
        }

        $score = trim((string)$score);

        if ($score === '') {
            return $result;
        }

        $score = str_replace(
            [':', '–', '—', ' '],
            '-',
            $score
        );

        $parts = explode(
            '-',
            $score
        );

        if (
            isset($parts[0]) &&
            is_numeric($parts[0])
        ) {
            $result['home'] =
                (int)$parts[0];
        }

        if (
            isset($parts[1]) &&
            is_numeric($parts[1])
        ) {
            $result['away'] =
                (int)$parts[1];
        }

        return $result;
    }
}