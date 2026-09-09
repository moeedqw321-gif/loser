<?php
(defined('BASEPATH')) OR exit('No direct script access allowed');
require_once('FoldTrait.php');
class Api extends CI_Controller{
    public $CI;
    public function __construct(){
        parent::__construct();
        $this->CI = & get_instance();
        $this->CI->load->library('soccerama');
		$this->getDateRangeMatches();
		$this->GetUpcomingOdds();
    }
    public function GetUpcomingOdds(){
        $include = array(
            'competition' ,
            'homeTeam' ,
            'awayTeam' ,
            'odds' ,
        );  
		$date_range = date('Y-m-d' , strtotime("+5 day"));
        $matchesToday = $this->CI->soccerama->matches(array('competition', 'homeTeam', 'awayTeam', 'odds'))->byDate(date('Y-m-d') , $date_range);
		if(file_put_contents(API_DIR . 'home/matches.json', $matchesToday)){
			$resultsToday = $this->CI->soccerama->livescore(array('homeTeam', 'awayTeam', 'odds'))->today();
			if(file_put_contents(API_DIR . 'home/resultsToday.json', $resultsToday)){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
    }
    public function GetInplayOdds(){
        $resultsToday = $this->CI->soccerama->livescore($include)->now();
		if(file_put_contents(API_DIR . 'inPlay/matches.json', $resultsToday)){
			return true;
		}else{
			return false;
		}
    }
    public function getDateRangeMatches ( $day = 0 ) {
        $include = array(
            'competition' ,
            'homeTeam' ,
            'awayTeam' ,
            'odds' ,
        );
        for ($d=0;$d<5; $d++) {
            $date_range = date('Y-m-d' , strtotime("+" .($d+0)." day" , strtotime(date('Y-m-d'))));

            $matches = $this->CI->soccerama->matches($include)->byDate($date_range , $date_range);
            $resultInclude = array(
                'homeTeam' , 'awayTeam' , 'odds'
            );
            $resultsToday = $this->CI->soccerama->livescore($resultInclude)->byDate(date('Y-m-d'));
            if ( file_put_contents(API_DIR . "upcoming/day_$d.json" , ($matches)) &&
                file_put_contents(API_DIR . "upcoming/results$d.json" , ($resultsToday)) ):
            endif;
        }
    }
    public function checkResultUpComing ( $matches ) {

        $this->output->delete_cache();
        
        $resultInclude = array(
            'homeTeam' , 'awayTeam' , 'odds'
        );

        $sortedByMatchID = array();
        foreach ( $matches->data as $item ) {
            $sortedByMatchID[$item->id] = $item;
        }
        ksort($sortedByMatchID , SORT_NUMERIC);

        

        $this->load->eloquent('Bet');
        $this->load->eloquent('Bet_form');

        $whereID = array();
        foreach ( $sortedByMatchID as $match_id => $match ):
            if ( $match->status == 'FT' ) {
                Bet_form::where('match_id' , $match_id)->update([
                    'status' => $match->status ,
                    'home_score_ft' => $match->home_score ,
                    'away_score_ft' => $match->away_score ,
                ]);
                $whereID[] = $match_id;
            }
            elseif ( $match->status == 'AET' OR $match->status == 'FT_PEN' OR $match->status == 'ET' ) {

                $ftt = explode("-" , $match->ft_score);

                Bet_form::where('match_id' , $match_id)->update([
                    'status' => 'FT' ,
                    'home_score_ft' => $ftt[0] ,
                    'away_score_ft' => $ftt[1] ,
                ]);
                $whereID[] = $match_id;
            }
            elseif ( $match->status == 'POSTP' OR $match->status == 'CANCL' OR $match->status == 'DELETED' OR $match->status == 'ABAN' ) {
                $whereID[] = $match_id;
            }
        endforeach;
        $Bet_forms = Bet_form::whereIn('match_id' , $whereID)->get();
        foreach ( $Bet_forms as $form ):
        if ( $form->bet->status != 0 ):
                continue;
            endif;
            if ( $form->bet->type == 1 ) {
                if ( $form->status == 'CANCL' OR $match->status == 'POSTP' OR $form->status == 'DELETED' OR $form->status == 'INT' OR $match->status == 'ABAN' ) {

                    $form->update(array(
                        'result_status' => 1 ,
                    ));
                    $form->bet->update(array(
                        'effective_odd' => 1 ,
                        'status' => 1
                    ));
                    $this->depositStake($form->bet);
                }
                else {

                    $win = $this->RowDeterminationResult($form , $sortedByMatchID);

                    if ( $win ) {
                        $form->update(array(
                            'result_status' => 1 ,
                        ));
                        $form->bet->update(array(
                            'status' => 1
                        ));
                        $this->depositStake($form->bet);
                    }
                    else {
                        $form->update(array(
                            'result_status' => 2 ,
                        ));
                        $form->bet->update(array(
                            'status' => 2
                        ));
                        $this->affiliate($form);
                    }
                }
            }
            else {
                $forms = $form->bet->bet_form;

                if ( $form->status == 'CANCL' OR $match->status == 'ABAN' OR $match->status == 'POSTP' OR $form->status == 'DELETED' OR $form->status == 'INT' OR $match->status == 'DELAYED' OR $match->status == 'AWARDED' ) {

                    $form->bet->update(
                            array(
                                'effective_odd' => $form->bet->effective_odd / $form->odd ,
                            )
                    );
                    $form->update(array(
                        'result_status' => 1 ,
                        'odd' => 1 ,
                        'status' => 'FT'
                    ));
                    if ( $this->isMixMatchesFinalTime($forms , $sortedByMatchID) ) {
                        if ( $this->DeterminationResult($forms , $sortedByMatchID) ) {
                            $this->depositStake($form->bet);
                            $form->bet->update(array( 'status' => 1 ));
                        }
                        else {
                            $form->bet->update(array( 'status' => 2 ));
                            $this->affiliate($form);
                        }
                    }
                }
                else {
                    if ( $form->status == 'FT' OR $form->status == 'FT_PEN' ) {

// determine the result of Mix bets
                        $win = $this->RowDeterminationResult($form , $sortedByMatchID);
                        if ( $win == true ) {
                            $form->update([
                                'result_status' => 1
                            ]);
                        }
                        else {
                            $form->update([
                                'result_status' => 2
                            ]);
                        }

                        if ( $this->isMixMatchesFinalTime($forms , $sortedByMatchID) ) {
                            if ( $this->DeterminationResult($forms , $sortedByMatchID) ) {
// Deposite the stake of bet to the user's account
                                $this->depositStake($form->bet);
// status = 1 : wining and settled
                                $form->bet->update(array( 'status' => 1 ));
                            }
                            else {
                                $form->bet->update(array( 'status' => 2 ));
                                $this->affiliate($form);
                            }
                        }
                        else {
// not all finaled
                            continue;
                        }
                    }
                    else {
                        continue;
                    }
                }
            }
        endforeach;
    }

    public function checkResultMatch ( $match_id , $user_id ) {

        $this->load->eloquent('Bet');
        $this->load->eloquent('Bet_form');

        $Bets = Bet_form::where('match_id' , $match_id)->get();
        foreach ( $Bets as $form ):
            if ( $form->bet->status == 1 ):
                continue;
            endif;
            $this->checkResultUpComingId($match_id , null , true);
        endforeach;
        redirect(site_url(ADMIN_PATH . '/bets/bets/view/' . $user_id));
    }

    /**
     * Check for final result of Bets 
     */
    public function checkResultUpComingId ( $id , $user_id = null , $bulk = false ) {

        $resultInclude = array(
            'homeTeam' , 'awayTeam' , 'odds'
        );
        $matches = $this->CI->soccerama->matches($resultInclude)->byId($id);
//dd($matches);
        $sortedByMatchID = array();
        /**
         * organize and sort the data structure of matches
         */
        $sortedByMatchID[$matches->id] = $matches;
        ksort($sortedByMatchID , SORT_NUMERIC);

        $this->load->eloquent('Bet');
        $this->load->eloquent('Bet_form');
        $whereID = array();
        foreach ( $sortedByMatchID as $match_id => $match ):
            if ( $match->status == 'FT' ) {
// we must know each matche's updated status for checking the bet's result specialy for mix types
                Bet_form::where('match_id' , $match_id)->update([
                    'status' => $match->status ,
                    'home_score_ft' => $match->home_score ,
                    'away_score_ft' => $match->away_score ,
                ]);
                $whereID[] = $match_id;
            }
            elseif ( $match->status == 'AET' OR $match->status == 'FT_PEN' OR $match->status == 'ET' ) {

                $ftt = explode("-" , $match->ft_score);

                Bet_form::where('match_id' , $match_id)->update([
                    'status' => 'FT' ,
                    'home_score_ft' => $ftt[0] ,
                    'away_score_ft' => $ftt[1] ,
                ]);
                $whereID[] = $match_id;
            }
            elseif ( $match->status == 'POSTP' OR $match->status == 'ABAN' OR $match->status == 'CANCL' OR $match->status == 'DELETED' ) {
//                Bet_form::where('match_id' , $match_id)->update(array( 'odd' => 1 , 'status' => $match->status ));
                $whereID[] = $match_id;
            }
        endforeach;

        $Bet_forms = Bet_form::whereIn('match_id' , $whereID)
// ->where('bet_type' , "1x2")
                ->get();
        foreach ( $Bet_forms as $form ):
// single bet
            if ( $form->bet->status == 1 ):
                continue;
            endif;
            if ( $form->bet->type == 1 ) {

                if ( $form->status == 'CANCL' OR $match->status == 'ABAN' OR $match->status == 'POSTP' OR $form->status == 'DELETED' OR $form->status == 'INT' ) {

                    $form->update(array(
                        'result_status' => 1 ,
                    ));
                    $form->bet->update(array(
                        'effective_odd' => 1 ,
                        'status' => 1
                    ));
                    $this->depositStake($form->bet);
                }
                else {
                    $win = $this->RowDeterminationResult($form , $sortedByMatchID);
                    if ( $win ) {
                        $form->update(array(
                            'result_status' => 1 ,
                        ));
                        $form->bet->update(array(
                            'status' => 1
                        ));
// Deposite the stake of bet to the user's account
                        $this->depositStake($form->bet);
                    }
                    else {
                        $form->update(array(
                            'result_status' => 2 ,
                        ));
                        $form->bet->update(array(
                            'status' => 2
                        ));
                    }
                }
            }
// For mix bets
            else {
                $forms = $form->bet->bet_form;

                if ( $form->status == 'CANCL' OR $match->status == 'POSTP' OR $form->status == 'DELETED' OR $form->status == 'INT' OR $match->status == 'DELAYED' OR $match->status == 'ABAN' OR $match->status == 'AWARDED' ) {

                    $form->bet->update(
                            array(
                                'effective_odd' => $form->bet->effective_odd / $form->odd ,
                            )
                    );

                    $form->update(array(
                        'result_status' => 1 ,
                        'odd' => 1 ,
                        'status' => 'FT'
                    ));
                }
                else {
                    if ( $form->status == 'FT' OR $form->status == 'FT_PEN' ) {


// determine the result of Mix bets
                        $win = $this->RowDeterminationResult($form , $sortedByMatchID);

                        if ( $win == true ) {
                            $form->update([
                                'result_status' => 1
                            ]);
                        }
                        else {
                            $form->update([
                                'result_status' => 2
                            ]);
                            continue;
                        }

                        if ( $this->isMixMatchesFinalTimeManual($forms , $sortedByMatchID) ) {
                            if ( $this->DeterminationResult($forms , $sortedByMatchID) ) {
// Deposite the stake of bet to the user's account
                                $this->depositStake($form->bet);
// status = 1 : wining and settled
                                $form->bet->update(array( 'status' => 1 ));
                            }
                            else {
                                $form->bet->update(array( 'status' => 2 ));
                            }
                        }
                        else {
// not all finaled
                            continue;
                        }
                    }
                    else {
                        continue;
                    }
                }
            }
        endforeach;
        if ( $user_id AND $bulk === false )
            redirect(site_url(ADMIN_PATH . '/bets/bets/view/' . $user_id));
        elseif ( $bulk )
            return true;
    }

    /**
     * Check for final result of Bets 
     */
    public function checkResultInplay () {
        $matches = $this->getInplayOddsOnline();
        $sortedByMatchID = array();

        /**
         * organize and sort the data structure of matches
         */
        foreach ( $matches as $item ) {
            if ( !property_exists($item , "start_time") OR ! property_exists($item , "state") ) {
                $item->start_time = "";
                $item->state = "";
            }
            $mkTimeMatch = mktime(date('H' , strtotime($item->start_time)) , date('i' , strtotime($item->start_time)) , date('s' , strtotime($item->start_time)) , date('m' , strtotime($item->start_time)) , date('d' , strtotime($item->start_time)) , date('Y' , strtotime($item->start_time)));

            $diff = time() - $mkTimeMatch;
            if ( ( $diff > 3400 && $diff < 70000 && $item->break_point > 0 && $item->state != 1015 && $item->break_point != 120 ) OR ( $item->state == 1017 OR $item->break_point == 90) ):
                $sortedByMatchID[$item->id] = $item;
            endif;
        }

        ksort($sortedByMatchID , SORT_NUMERIC);

        $this->load->eloquent('Bet');
        $this->load->eloquent('Bet_form');

        $whereID = array();

        foreach ( $sortedByMatchID as $match_id => $match ):
            $result = explode('-' , $sortedByMatchID[$match_id]->result);
            Bet_form::where('match_id' , $match_id)->update([
                'status' => 'FT' ,
                'home_score_ft' => $match->teams->home->goals ,
                'away_score_ft' => $match->teams->away->goals ,
            ]);
            $whereID[] = $match_id;
        endforeach;
        $Bet_forms = Bet_form::whereIn('match_id' , $whereID)->where('bookmaker_id' , 404)->get();
        foreach ( $Bet_forms as $form ):
            if ( $form->bet->status != 0 ):
                continue;
            endif;
            $result = [$sortedByMatchID[$form->match_id]->teams->home->goals , $sortedByMatchID[$form->match_id]->teams->away->goals ];

            if ( $form->bet->type == 1 ) {

                if ( $form->bet_type == '1x2' OR $form->bet_type == 'Fulltime Result' ) {
                    $whatsup = $this->inPlayRowDetermineResult($form , $result);
                }
                else {
                    $whatsup = $this->detectWinnerOtherOdds($form , $result);
                }
                if ( $whatsup == 1 ) {

                    $form->update(array(
                        'result_status' => 1 ,
                    ));
                    $form->bet->update(array(
                        'status' => 1
                    ));
// Deposite the stake of bet to the user's account
                    $this->depositStake($form->bet);
                }
// draw no bet
                elseif ( $whatsup == 2 ) {

                    $form->update(array(
                        'result_status' => 1 ,
                    ));
                    $form->bet->update(array(
                        'status' => 1 ,
                        'effective_odd' => 1
                    ));
// Deposite the stake of bet to the user's account
                    $this->depositStake($form->bet);
                }
                else {
                    $form->update(array(
                        'result_status' => 2 ,
                    ));
                    $form->bet->update(array(
                        'status' => 2
                    ));
                    $this->affiliate($form);
                }
            }
// For mix bets
            else {

                $forms = $form->bet->bet_form;

                $win = false;
// determine the result of Mix bets
//if The Fulltime Result is the type of bet
                if ( $form->bet_type == '1x2' OR $form->bet_type == 'Fulltime Result' ) {
                    $win = $this->inPlayRowDetermineResult($form , $result);
                }// other types of bets
                else {
                    $win = $this->detectWinnerOtherOdds($form , $result);
                }
                if ( $win == 1 ) {
                    $form->update([
                        'result_status' => 1
                    ]);
                }
                elseif ( $win == 2 ) {

                    $form->update(array(
                        'result_status' => 1 ,
                    ));
                    $form->bet->update(array(
                        'effective_odd' => $form->bet->effective_odd / $form->odd ,
                    ));
                }
                else {
                    $form->update([
                        'result_status' => 2
                    ]);
                }
                if ( $this->allInplaySolved($forms) ) {
                    if ( $this->isInplayMixMatchesWon($forms) ) {
// Deposite the stake of bet to the user's account
                        $this->depositStake($form->bet);
// status = 1 : wining and settled
                        $form->bet->update(array( 'status' => 1 ));
                    }
                    else {
                        $this->affiliate($form);
                        $form->bet->update(array( 'status' => 2 ));
                    }
                }
                else {
                    continue;
                }
            }
        endforeach;
    }

    /**
     * detect Winner Other Odds
     * @param type $form
     * @param type $result
     * @return boolean
     */
    public function detectWinnerOtherOddsbyID ( $id ) {
        $this->load->eloquent('Bet');
        $this->load->eloquent('Bet_form');
        $form = Bet_form::where('match_id' , $id)->first();
        $result = array( $form->home_score_ft , $form->away_score_ft );
        $win = false;

        if ( $result[0] > $result[1] ) {
            $winner = $form->home_team;
        }
        elseif ( $result[0] < $result[1] ) {
            $winner = $form->away_team;
        }
        else
            $winner = 'Draw';
//dump($winner);
//dump($form->pick);

        if ( $form->bet_type == 'Match Goals' ) {
            $goals = ( int ) $result[0] + ( int ) $result[1];
            $OverUnder = explode(' ' , $form->pick);
            if ( ( float ) $OverUnder[1] < ( float ) $goals OR ( float ) $OverUnder[1] == ( float ) $goals )
                $matchGoalResult = 'Over ' . $OverUnder[1];
            else
                $matchGoalResult = 'Under ' . $OverUnder[1];
            if ( $matchGoalResult == $form->pick )
                $win = 1;
            else
                $win = false;
        }elseif ( $form->bet_type == 'Double Chance' ) {
            if ( strpos($form->pick , $winner) === false )
                $win = false;
            else
                $win = 1;
        }elseif ( $form->bet_type == 'Draw No Bet' ) {
            if ( $winner == $form->pick )
                $win = 1;
            elseif ( $winner == 'Draw' )
                $win = 2;
            else
                $win = false;
        }
        elseif ( $form->bet_type == 'Goals Odd/Even' ) {
            $oddOrEven = (( int ) $result[0] + ( int ) $result[1]) % 2;
            if ( $oddOrEven == 0 AND $form->pick == 'Even' )
                $win = true;
            elseif ( $oddOrEven == 1 AND $form->pick == 'Odd' )
                $win = 1;
            else
                $win = false;
        }
        elseif ( $form->bet_type == 'Final Score' ) {
            $result_picked = explode('-' , $form->pick);
            if ( $result[0] == $result_picked[0] AND $result [1] == $result_picked[1] )
                $win = 1;
            else
                $win = false;
        }
        return $win;
    }

    public function detectWinnerOtherOdds ( $form , $result ) {
        $win = false;

        if ( $result[0] > $result[1] ) {
            $winner = $form->home_team;
        }
        elseif ( $result[0] < $result[1] ) {
            $winner = $form->away_team;
        }
        else
            $winner = 'Draw';

        if ( $form->bet_type == 'Match Goals' ) {
            $goals = ( int ) $result[0] + ( int ) $result[1];
            $OverUnder = explode(' ' , $form->pick);
            if ( ( float ) $OverUnder[1] < ( float ) $goals OR ( float ) $OverUnder[1] == ( float ) $goals )
                $matchGoalResult = 'Over ' . $OverUnder[1];
            else
                $matchGoalResult = 'Under ' . $OverUnder[1];
            if ( $matchGoalResult == $form->pick )
                $win = 1;
            else
                $win = false;
        }elseif ( $form->bet_type == 'Double Chance' ) {

            if ( strpos($form->pick , $winner) === false )
                $win = false;
            else
                $win = 1;
        } elseif ( $form->bet_type == 'Result / Both Teams To Score' ) {

            if ( strpos($form->pick , $winner) === false OR ( ($result[0] == 0 OR $result [1] == 0) AND strpos('& Yes' , $form->pick)) OR ( ($result[0] > 0 OR $result [1] > 0) AND strpos('& No' , $form->pick)) )
                $win = false;
            elseif ( strpos($form->pick , $winner) !== false AND ( ($result[0] == 0 OR $result [1] == 0) AND strpos('& No' , $form->pick)) )
                $win = 1;
            elseif ( strpos($form->pick , $winner) !== false AND ( ($result[0] > 0 AND $result [1] > 0) AND strpos('& Yes' , $form->pick)) )
                $win = 1;
            elseif ( strpos($form->pick , $winner) !== false AND ( ($result[0] == 0 AND $result [1] == 0) AND strpos('& No' , $form->pick)) )
                $win = 1;
        }elseif ( $form->bet_type == 'Draw No Bet' ) {
            if ( $winner == $form->pick )
                $win = 1;
            elseif ( $winner == 'Draw' )
                $win = 2;
            else
                $win = false;
        }
        elseif ( $form->bet_type == 'Goals Odd/Even' ) {
            $oddOrEven = (( int ) $result[0] + ( int ) $result[1]) % 2;
            if ( $oddOrEven == 0 AND $form->pick == 'Even' )
                $win = 1;
            elseif ( $oddOrEven == 1 AND $form->pick == 'Odd' )
                $win = 1;
            else
                $win = false;
        }
        elseif ( $form->bet_type == 'Final Score' ) {
            $result_picked = explode('-' , $form->pick);
            if ( $result[0] == $result_picked[0] AND $result [1] == $result_picked[1] )
                $win = 1;
            else
                $win = false;
        }
        return $win;
    }

    /**
     * detect that all matches is finaled or not.
     * @param type $forms
     * @return boolean
     */
    public function allInplaySolved ( $forms ) {
        $status = false;
        foreach ( $forms as $val ):
            if ( $val->result_status != 0 )
                $status = true;
            else
                return false;
        endforeach;
        return $status;
    }

    /**
     * Check for final time status for the given matches
     * @param type $form
     * @param array $result the home team and away team goals
     * @return boolean
     */
    public function inPlayRowDetermineResult ( $form , $result ) {

        if ( ( int ) $result[0] > ( int ) $result[1] ) {
            $winnerOddLabel = 1;
            $winnerTeam = $form->home_team;
        }
        elseif ( ( int ) $result[0] < ( int ) $result[1] ) {
            $winnerOddLabel = 2;
            $winnerTeam = $form->away_team;
        }
        else {
            $winnerOddLabel = 'X';
            $winnerTeam = '&#1605;&#1587;&#1575;&#1608;&#1740;';
        }
        if ( ( $form->bet_type == '1x2' AND $form->odd_label == $winnerOddLabel) OR ( $form->bet_type == 'Fulltime Result' AND $form->pick == $winnerTeam ) ) {
            return 1;
        }
        else {
            return false;
        }
    }

    /**
     * Check for final time status for the given matches
     * @param type $forms
     * @return boolean
     */
    public function isMixMatchesFinalTime ( $forms ) {
        $status = false;
        foreach ( $forms as $val ):
            if ( ( $val->bet->status == 0 AND $val->result_status != 0 ) AND ( $val->status == 'FT' OR $val->status == 'FT_PEN' OR $val->status == 'CANCL' ) ) {
                $status = true;
            }
            else {
                return false;
            }
        endforeach;
        return $status;
    }

    /**
     * Check for final time status for the given matches
     * @param type $forms
     * @return boolean
     */
    public function isMixMatchesFinalTimeManual ( $forms ) {
        $status = false;
        foreach ( $forms as $val ):
            if ( ( $val->bet->status != 1 AND $val->result_status != 0 ) AND ( $val->status == 'FT' OR $val->status == 'FT_PEN' OR $val->status == 'CANCL' ) ) {
                $status = true;
            }
            else {
                return false;
            }
        endforeach;
        return $status;
    }

    /**
     * Check for final time status for the given matches
     * @param type $forms
     * @return boolean
     */
    public function isInplayMixMatchesWon ( $forms ) {
        $status = false;
        foreach ( $forms as $val ):
            if ( $val->result_status == 1 ) {
                $status = true;
            }
            elseif ( $val->result_status == 2 OR $val->result_status == 0 ) {
                return false;
            }
        endforeach;
        return $status;
    }

    /**
     * The result of all odds
     * @param type $forms
     * @param type $sortedByMatchID
     * @return boolean
     */
    public function DeterminationResult ( $forms , $sortedByMatchID ) {

        $win = false;
        $this->load->eloquent('Bet_form');
        foreach ( $forms as $row ) {

            if ( $row->result_status == 1 )
                $win = true;
            elseif ( $row->result_status == 2 OR $row->result_status == 0 ) {
                return false;
            }
        }
        return $win;
    }
    public function RowDeterminationResult ( $form , $sortedByMatchID ) {

        $win = false;
        $this->load->eloquent('Bet_form');
		if($form->bookmaker_id!=404){
        $odd = $sortedByMatchID[$form->match_id]->odds->data[0]->types->data[0]->odds->data;
        $resultOddWinnerIndex = $this->searchArrayForKey('winning' , true , $odd);
		}

        if ( $form->bet_type == '1x2' ) {
            $label = $this->winingOdd($form);
            if ( $form->odd_label == $label )
                $win = true;
            else
                $win = false;
        }
// detect Double chance odds
        elseif ( $form->bet_type == 'Double Chance' ) {
            if ( $form->pick == 12 AND $this->winingOdd($form) != 'X' ) {
                $win = true;
            }
            elseif ( $form->pick == '1X' AND ( $this->winingOdd($form) == 'X' OR $this->winingOdd($form) == 1) )
                $win = true;
            elseif ( $form->pick == 'X2' AND ( $this->winingOdd($form) == 'X' OR $this->winingOdd($form) == 2) )
                $win = true;
            else
                $win = false;
        }

        elseif ( $form->bet_type == 'HT/FT Double' ) {

            $h_result = explode('-' , $sortedByMatchID[$form->match_id]->ht_score);
            $f_result = explode('-' , $sortedByMatchID[$form->match_id]->ft_score);

            $pickk = explode('/' , $form->pick);
            if ( $h_result[0] > $h_result[1] )
                $h_winner = $form->home_team;
            elseif ( $h_result[0] < $h_result[1] ) {
                $h_winner = $form->away_team;
            }
            else
                $h_winner = "Draw";


            if ( $f_result[0] > $f_result[1] )
                $f_winner = $form->home_team;
            elseif ( $f_result[0] < $f_result[1] ) {
                $f_winner = $form->away_team;
            }
            else
                $f_winner = "Draw";


            if ( ($pickk[0] == $h_winner) AND ( $pickk[1] == $f_winner) )
                $win = true;
            else
                $win = false;
        }
        elseif ( $form->bet_type == 'Both Teams to Score' ) {
            if ( $form->home_score_ft > 0 AND $form->away_score_ft > 0 AND $form->pick == 'Yes' )
                $win = true;
            elseif ( ( $form->home_score_ft == 0 OR $form->away_score_ft == 0) AND $form->pick == 'No' )
                $win = true;
            else
                $win = false;
        }
        elseif ( $form->bet_type == '1x2 1st Half' ) {
            $h_result = explode('-' , $sortedByMatchID[$form->match_id]->ht_score);
            if ( $h_result[0] > $h_result[1] AND $form->home_team == $form->pick )
                $win = true;
            elseif ( $h_result[0] < $h_result[1] AND $form->away_team == $form->pick )
                $win = true;
            elseif ( ($h_result[0] == $h_result[1]) AND ( ($form->pick == 'Draw') OR ( $form->pick == 'X')) )
                $win = true;
            else
                $win = false;
        }



        return $win;
    }

    public function winingOdd ( $form ) {
        $h_score = ( int ) $form->home_score_ft;
        $a_score = ( int ) $form->away_score_ft;
        if ( $h_score > $a_score ) {
            return 1;
        }
        elseif ( $a_score > $h_score ) {
            return 2;
        }
        elseif ( $a_score == $h_score ) {
            return 'X';
        }
    }

    public function affiliate ( $form ) {
        $this->CI->load->eloquent('users/affiliate');

        $aff_user = Affiliate::where('invited_user_id' , $form->bets_user_id)->first();
        if ( $aff_user ) {
            $this->CI->load->sentinel();
            $UserModel = $this->CI->sentinel->getUserRepository();
            $user = $UserModel->find($aff_user->user_id);
            $cash = $user->cash;
            $user->update(array(
                'cash' => $cash + ($form->bet->stake * 0.15)
            ));

            $this->CI->load->eloquent('payment/Transaction');
            Transaction::create([
                'trans_id' => $user->id . time() ,
                'price' => ($form->bet->stake * 0.15) ,
                'invoice_type' => 5 ,
                'status' => 1 ,
                'cash' => $cash + ($form->bet->stake * 0.15) ,
                'user_id' => $user->id ,
                'description' =>
                "&#1608;&#1575;&#1585;&#1740;&#1586; &#1705;&#1575;&#1585;&#1605;&#1586;&#1583; &#1576;&#1575;&#1582;&#1578; &#1588;&#1585;&#1591; &#1705;&#1575;&#1585;&#1576;&#1585; &#1586;&#1740;&#1585; &#1605;&#1580;&#1605;&#1608;&#1593;&#1607; &#1576;&#1607; &#1588;&#1606;&#1575;&#1587;&#1607; " . $form->bets_user_id . ' &#1608; &#1588;&#1585;&#1591; &#1576;&#1575; &#1588;&#1606;&#1575;&#1587;&#1607; ' . $form->bets_id ,
            ]);
        }

        return true;
    }

    /**
     * Utility function for search in array
     *  @param string $type
     * @param a rray $arra y
     * @return null || int
     */
    function searchArrayForKey ( $key , $value , &$array ) {
        foreach ( $array as $index => $val ) {
            if ( $val->$key == $value ) {
                return $index;
            }
        }
        return null;
    }

    /**
     * Deposite The Winning Stake into the user's account
     * @param type $Bet
     * @return boolean
     */
    public function depositStake ( $Bet ) {
        $this->CI->load->sentinel();
        $UserModel = $this->CI->sentinel->getUserRepository();
        $user = $UserModel->find($Bet->user_id);
        $cash = $user->cash;
        if ( $Bet->effective_odd > 100 )
            $Bet->effective_odd = 100;
        $user->update(array(
            'cash' => $cash + ($Bet->stake * $Bet->effective_odd)
        ));

        $this->CI->load->eloquent('payment/Transaction');
        Transaction::create([
            'trans_id' => $user->id . time() ,
            'price' => ($Bet->stake * $Bet->effective_odd) ,
            'invoice_type' => 2 ,
            'status' => 1 ,
            'cash' => $cash + ($Bet->stake * $Bet->effective_odd) ,
            'user_id' => $user->id ,
            'description' => '&#1608;&#1575;&#1585;&#1740;&#1586; &#1605;&#1576;&#1604;&#1594; &#1576;&#1585;&#1583; &#1588;&#1585;&#1591; &#1576;&#1607; &#1588;&#1606;&#1575;&#1587;&#1607; ' . $Bet->id ,
        ]);

        return true;
    }

//dellllllllllllllllllllllllll

    public function checkResultUpComingIddel ( $id , $user_id = null , $bulk = false ) {

        $resultInclude = array(
            'homeTeam' , 'awayTeam' , 'odds'
        );
        //$matches = $this->CI->soccerama->matches($resultInclude)->byId($id);
//dd($matches);
        $sortedByMatchID = array();
        /**
         * organize and sort the data structure of matches
         */
        // $sortedByMatchID[$id] = $matches;
        // ksort($sortedByMatchID , SORT_NUMERIC);

        $this->load->eloquent('Bet');
        $this->load->eloquent('Bet_form');
        $whereID = array();
        // foreach ( $sortedByMatchID as $match_id => $match ):
        //elseif ( $match->status == 'POSTP' OR $match->status == 'ABAN' OR $match->status == 'CANCL' OR $match->status == 'DELETED' ) {
//                Bet_form::where('match_id' , $match_id)->update(array( 'odd' => 1 , 'status' => $match->status ));
        $whereID[] = $id;
        //}
        //endforeach;

        $Bet_forms = Bet_form::whereIn('match_id' , $whereID)
// ->where('bet_type' , "1x2")
                ->get();
        foreach ( $Bet_forms as $form ):
// single bet
            if ( $form->bet->status == 1 ):
                continue;
            endif;
            if ( $form->bet->type == 1 ) {

                // if ( $form->status == 'CANCL' OR $match->status == 'ABAN' OR $match->status == 'POSTP' OR $form->status == 'DELETED' OR $form->status == 'INT' ) {

                $form->update(array(
                    'result_status' => 1 ,
                ));
                $form->bet->update(array(
                    'effective_odd' => 1 ,
                    'status' => 1
                ));
                $this->depositStake($form->bet);
                // }
            }
// For mix bets
            else {
                $forms = $form->bet->bet_form;

                //if ( $form->status == 'CANCL' OR $match->status == 'POSTP' OR $form->status == 'DELETED' OR $form->status == 'INT' OR $match->status == 'DELAYED' OR $match->status == 'ABAN' OR $match->status == 'AWARDED' ) {

                $form->bet->update(
                        array(
                            'effective_odd' => $form->bet->effective_odd / $form->odd ,
                        )
                );

                $form->update(array(
                    'result_status' => 1 ,
                    'odd' => 1 ,
                    'status' => 'FT'
                ));
                if ( $this->isMixMatchesFinalTimeManual($forms , $sortedByMatchID) ) {
                    if ( $this->dastibebin($forms) ) {
// Deposite the stake of bet to the user's account
                        $this->depositStake($form->bet);
// status = 1 : wining and settled
                        $form->bet->update(array( 'status' => 1 ));
                    }
                    else {
                        $form->bet->update(array( 'status' => 2 ));
                    }
                }
            }

        endforeach;
        if ( $user_id AND $bulk === false )
            redirect(site_url(ADMIN_PATH . '/bets/bets/view/' . $user_id));
        elseif ( $bulk )
            return true;
    }
    public function dastibebin ( $forms ) {
        $status = false;
        foreach ( $forms as $val ):
            if ( $val->result_status == 1 ) {
                $status = true;
            }
            else {
                return false;
            }
        endforeach;
        return $status;
    }
    public function checkResultUpComingIddasti ( $id , $home , $away ) {

        $resultInclude = array(
            'homeTeam' , 'awayTeam' , 'odds'
        );

        $sortedByMatchID = array();


        $this->load->eloquent('Bet');
        $this->load->eloquent('Bet_form');
        $whereID = array();



        Bet_form::where('match_id' , $id)->update([
            'status' => 'FT' ,
            'home_score_ft' => $home ,
            'away_score_ft' => $away ,
        ]);
        $whereID[] = $id;



        $Bet_forms = Bet_form::whereIn('match_id' , $whereID)
// ->where('bet_type' , "1x2")
                ->get();
        foreach ( $Bet_forms as $form ):
// single bet
            if ( $form->bet->status == 1 ):
                continue;
            endif;
            if ( $form->bet->type == 1 ) {

                if ( $form->status == 'CANCL' OR $match->status == 'ABAN' OR $match->status == 'POSTP' OR $form->status == 'DELETED' OR $form->status == 'INT' ) {

                    $form->update(array(
                        'result_status' => 1 ,
                    ));
                    $form->bet->update(array(
                        'effective_odd' => 1 ,
                        'status' => 1
                    ));
                    $this->depositStake($form->bet);
                }
                else {
                    $win = $this->RowDeterminationResult($form , $sortedByMatchID);
                    if ( $win ) {
                        $form->update(array(
                            'result_status' => 1 ,
                        ));
                        $form->bet->update(array(
                            'status' => 1
                        ));
// Deposite the stake of bet to the user's account
                        $this->depositStake($form->bet);
                    }
                    else {
                        $form->update(array(
                            'result_status' => 2 ,
                        ));
                        $form->bet->update(array(
                            'status' => 2
                        ));
                    }
                }
            }
// For mix bets
            else {
                $forms = $form->bet->bet_form;

                if ( $form->status == 'CANCL' OR $match->status == 'POSTP' OR $form->status == 'DELETED' OR $form->status == 'INT' OR $match->status == 'DELAYED' OR $match->status == 'ABAN' OR $match->status == 'AWARDED' ) {

                    $form->bet->update(
                            array(
                                'effective_odd' => $form->bet->effective_odd / $form->odd ,
                            )
                    );

                    $form->update(array(
                        'result_status' => 1 ,
                        'odd' => 1 ,
                        'status' => 'FT'
                    ));
                }
                else {
                    if ( $form->status == 'FT' OR $form->status == 'FT_PEN' ) {


// determine the result of Mix bets
                        $win = $this->RowDeterminationResult($form , $sortedByMatchID);

                        if ( $win == true ) {
                            $form->update([
                                'result_status' => 1
                            ]);
                        }
                        else {
                            $form->update([
                                'result_status' => 2
                            ]);
                            continue;
                        }

                        if ( $this->isMixMatchesFinalTimeManual($forms , $sortedByMatchID) ) {
                            if ( $this->DeterminationResult($forms , $sortedByMatchID) ) {
// Deposite the stake of bet to the user's account
                                $this->depositStake($form->bet);
// status = 1 : wining and settled
                                $form->bet->update(array( 'status' => 1 ));
                            }
                            else {
                                $form->bet->update(array( 'status' => 2 ));
                            }
                        }
                        else {
// not all finaled
                            continue;
                        }
                    }
                    else {
                        continue;
                    }
                }
            }
        endforeach;
        if ( $user_id AND $bulk === false )
            redirect(site_url(ADMIN_PATH . '/bets/bets/view/' . $user_id));
    }
	public function checkResultUpComingIddasti2 ( $id , $home , $away,$hometeam ) {

        $resultInclude = array(
            'homeTeam' , 'awayTeam' , 'odds'
        );

        $sortedByMatchID = array();


        $this->load->eloquent('Bet');
        $this->load->eloquent('Bet_form');
        $whereID = array();

$hometeam = str_replace('%20',' ',$hometeam);

        Bet_form::where('match_id' , $id)->where('home_team' , $hometeam) ->update([
            'status' => 'FT' ,
            'home_score_ft' => $home ,
            'away_score_ft' => $away ,
        ]);
        $whereID[] = $id;
        $Bet_forms = Bet_form::whereIn('match_id' , $whereID)->where('home_team' , $hometeam)->get();
        foreach ( $Bet_forms as $form ):
            if ( $form->bet->status == 1 ):
                continue;
            endif;
            if ( $form->bet->type == 1 ) {
                if (1==0 ){
                }
                else {
                    $win = $this->RowDeterminationResult($form , $sortedByMatchID);
                    if ( $win ) {
                        $form->update(array(
                            'result_status' => 1 ,
                        ));
                        $form->bet->update(array(
                            'status' => 1
                        ));
// Deposite the stake of bet to the user's account
                        $this->depositStake($form->bet);
                    }
                    else {
                        $form->update(array(
                            'result_status' => 2 ,
                        ));
                        $form->bet->update(array(
                            'status' => 2
                        ));
                    }
                }
            }
// For mix bets
            else {
                $forms = $form->bet->bet_form;

                if (1==2){}
                else {
                    if ( $form->status == 'FT' OR $form->status == 'FT_PEN' ) {
                        $win = $this->RowDeterminationResult($form , $sortedByMatchID);
                        if ( $win == true ) {
                            $form->update([
                                'result_status' => 1
                            ]);
                        }
                        else {
                            $form->update([
                                'result_status' => 2
                            ]);
                            continue;
                        }

                        if ( $this->isMixMatchesFinalTimeManual($forms , $sortedByMatchID) ) {
                            if ( $this->DeterminationResult($forms , $sortedByMatchID) ) {
                                $this->depositStake($form->bet);
                                $form->bet->update(array( 'status' => 1 ));
                            }
                            else {
                                $form->bet->update(array( 'status' => 2 ));
                            }
                        }
                        else {
                            continue;
                        }
                    }
                    else {
                        continue;
                    }
                }
            }
        endforeach;
    
    }
}
class Bets extends Public_Controller {
    use FoldTrait;
	function __construct(){
		parent::__construct();
		$this->load->sentinel();
		$this->load->helper('cookie');
		if(!$this->sentinel->check()){
			if(isset($_COOKIE['auto_login']) AND !empty($_COOKIE['auto_login'])){
				$auto_login = $_COOKIE['auto_login'];
				$auto_login = explode('{{TkStar_Cookie}}', $auto_login);
				if(isset($auto_login[0]) AND isset($auto_login[1]) AND !empty($auto_login[0]) AND !empty($auto_login[1])){
					$credentials = array('email' => $auto_login[0], 'password' => $auto_login[1]);
					$auth_user = $this->sentinel->authenticate($credentials);
					if($auth_user){
						header('Location: ' . base_url($_SERVER['REQUEST_URI']));exit();
					}
				}
			}
		}
	}
    public function index(){
		$matches = file_get_contents("https://api.sportmonks.com/v3/football/livescores/inplay?api_token=kSLGrxDaSXfeMh5sb1xSDviFqRNXXtYjjZrL2fpLd39dHf2ibewuzCbqsJSM&include=participants;scores;state;league;odds;inplayOdds");
		$matches_j = json_decode($matches, false);
		$bannInplays=file_get_contents(site_url('bets/getbannInplays/'));
        $bannInplaysJson=json_decode($bannInplays, true);
		$getleagues = file_get_contents(site_url('bets/getleagues/'));
        $getleagues = json_decode($getleagues, false);
		if(empty($getleagues) OR (!is_array($getleagues) AND !is_object($getleagues))){
			$getleagues = array();
			$getleagues = (object)$getleagues;
		}
		$leagues = array();
		foreach($matches_j->data as $match){
		    $localTeam = $match->localTeam->data;
		    $visitorTeam = $match->visitorTeam->data;
            if(!isset($getleagues->leagues) OR count((array)$getleagues->leagues) <= 0){
                $leagues[$match->league->data->name][$match->id] = $match;
            }else{
				foreach($getleagues->leagues as $leagua){
					if($leagua->local == trim($visitorTeam->name) AND $leagua->visitor == trim($localTeam->name)) {
						$leagues[$leagua->league][$match->id] = $match;
						unset($leagues[$match->league->data->name][$match->id]);
					}else if($leagua->visitor == trim($visitorTeam->name) AND $leagua->local == trim($localTeam->name)){
						$leagues[$leagua->league][$match->id] = $match;
						unset($leagues[$match->league->data->name][$match->id]);
					}else{
						$leagues[$match->league->data->name][$match->id] = $match;
					}
				}
			}
		}
		krsort($leagues);
        $this->smart->assign(array('_COOKIE' => $_COOKIE, 'matches' => $leagues, 'oddBanns' => $bannInplaysJson['banns']));

        $this->smart->view('index');
    }
    public function reloadInplayOdds () {
		$this->load->helper('cookie');
		$cookie_change_bg_times = time() + 900;
		$this->CI->load->library('soccerama');
		$matches = $this->CI->soccerama->livescore()->now();
		$matches_j = json_decode($matches, false);
		$output = array();
        $bannInplays = file_get_contents(site_url('bets/getbannInplays/'));
        $bannInplaysJson = json_decode($bannInplays, true);
		$leagues = array();
		$getleagues = file_get_contents(site_url('bets/getleagues/'));
        $getleagues = json_decode($getleagues, false);
		$leagues = array();
        if(isset($matches_j->data) AND !empty($matches_j->data) AND (is_array($matches_j->data) OR is_object($matches_j->data))){
			foreach($matches_j->data as $match){
				$localTeam = $match->localTeam->data;
				$visitorTeam = $match->visitorTeam->data;
				if(!isset($getleagues->leagues) OR (is_array($getleagues->leagues) AND is_object($getleagues->leagues))){
					$leagues[$match->league->data->name][$match->id] = $match;
				}else{
					foreach($getleagues->leagues as $leagua){
						if($leagua->local == trim($visitorTeam->name) AND $leagua->visitor == trim($localTeam->name)) {
							$leagues[$leagua->league][$match->id] = $match;
							unset($leagues[$match->league->data->name][$match->id]);
						}else if($leagua->visitor == trim($visitorTeam->name) AND $leagua->local == trim($localTeam->name)){
							$leagues[$leagua->league][$match->id] = $match;
							unset($leagues[$match->league->data->name][$match->id]);
						}else{
							$leagues[$match->league->data->name][$match->id] = $match;
						}
					}
				}
			}
		}
		krsort($leagues);
		$game = '';
        if(isset($leagues) AND !empty($leagues) AND (is_array($leagues) OR is_object($leagues))){
			foreach($leagues as $key => $val){
				$gotBreak = false;
				foreach($val as $match){
					if((isset($match->inplay->data[0]) AND (is_array($match->inplay->data[0]) OR is_object($match->inplay->data[0]))) OR (isset($match->odds->data[0]) AND (is_array($match->odds->data[0]) OR is_object($match->odds->data[0])))){
						if($match->time->minute <= 89 AND ($match->time->status == 'LIVE' OR $match->time->status == 'HT' OR $match->time->status == 'FT') AND (count($match->odds->data) >= 1 OR count($match->inplay->data) >= 1)){
							$gotBreak = true;
							break;
						}
					}else{
						continue;
					}
				}
				if($gotBreak === false){
					continue;
				}else{
					$game .= '<div class="event-row-parent-search sport-categories sport-categories-1"><div class="event-type"><div class="title"><div class="text"><span class="yellow">' . $this->faLanguage($key) . '</span></div><div class="odd-title">&#1605;&#1740;&#1586;&#1576;&#1575;&#1606;</div><div class="odd-title">&#1605;&#1587;&#1575;&#1608;&#1740;</div><div class="odd-title">&#1605;&#1740;&#1607;&#1605;&#1575;&#1606;</div><div class="clear"></div></div><div class="odd-container">';
					foreach($val as $match){
						if($match->time->status == 'LIVE' OR $match->time->minute <= 89){
							$last = isset($match->inplay->data[0]->bookmaker->data[0]->odds) ? $match->inplay->data[0]->bookmaker->data[0]->odds : array();
							$last = (empty($last) OR (!is_array($last) AND !is_object($last))) ? (isset($match->odds->data[0]->bookmaker->data[0]->odds) ? $match->odds->data[0]->bookmaker->data[0]->odds : array()) : $last;
							if((!is_array($last) AND !is_object($last)) OR count((array)$last) <= 0){
								continue;
							}else{
								$first_odd = $last->data[0]->value;
								$second_odd = $last->data[1]->value;
								$third_odd = $last->data[2]->value;
								$first_odd = $first_odd >= 75.01 ? '75.00' : $first_odd;
								$first_odd = ($first_odd < 1 AND $first_odd > 1) ? 1 : $first_odd;
								$first_odd = ($first_odd == 0 OR $first_odd == 0.0 OR $first_odd == 0.00) ? '' : $first_odd;
								$second_odd = $second_odd >= 75.01 ? '75.00' : $second_odd;
								$second_odd = ($second_odd < 1 AND $second_odd > 1) ? 1 : $second_odd;
								$second_odd = ($second_odd == 0 OR $second_odd == 0.0 OR $second_odd == 0.00) ? '' : $second_odd;
								$third_odd = $third_odd >= 75.01 ? '75.00' : $third_odd;
								$third_odd = ($third_odd < 1 AND $third_odd > 1) ? 1 : $third_odd;
								$third_odd = ($third_odd == 0 OR $third_odd == 0.0 OR $third_odd == 0.00) ? '' : $second_odd;
								if(isset($bannInplaysJson['banns']) AND (is_object($bannInplaysJson['banns']) OR is_array($bannInplaysJson['banns']))){
									foreach($bannInplaysJson['banns'] as $match_id => $oddO){
										if($oddO['id'] == $match->id){
											$first_odd = $oddO['oddO'] == 'local' ? 0 : $first_odd;
											$second_odd = $oddO['oddO'] == 'x' ? 0 : $second_odd;
											$third_odd = $oddO['oddO'] == 'visitor' ? 0 : $third_odd;
										}else{
											continue;
										}
									}
								}
								$disabled_first_odd = 'false';
								$disabled_second_odd = 'false';
								$disabled_third_odd = 'false';
								$disabled_first_odd = ($match->scores->localteam_score > $match->scores->visitorteam_score AND $first_odd > 3) ? 'true' : 'false';
								$disabled_second_odd = ($match->scores->localteam_score > $match->scores->visitorteam_score AND $first_odd > 3) ? 'true' : 'false';
								$disabled_third_odd = ($match->scores->localteam_score > $match->scores->visitorteam_score AND $first_odd > 3) ? 'true' : 'false';
								$cookie_name_replace_first = str_replace(array('.'), array('_'), $first_odd) . '_' . $match->id . '_' . $last->data[0]->label;
								$cookie_name_replace_second = str_replace(array('.'), array('_'), $second_odd) . '_' . $match->id . '_' . $last->data[1]->label;
								$cookie_name_replace_third = str_replace(array('.'), array('_'), $third_odd) . '_' . $match->id . '_' . $last->data[3]->label;
								$cookie_name_write_first = $match->id . '_last_localTeamOdd';
								$cookie_name_write_second = $match->id . '_last_drawOdd';
								$cookie_name_write_third = $match->id . '_last_visitorTeamOdd';
								if($disabled_first_odd == 'false' AND empty($_COOKIE[$cookie_name_replace_first])){
									setcookie($cookie_name_replace_first, $first_odd, $cookie_change_bg_times);
									if(isset($_COOKIE[$cookie_name_write_first]) AND $first_odd < $_COOKIE[$cookie_name_write_first]){
										$show_bg_change_first_odd = ' blink-red';
									}elseif(isset($_COOKIE[$cookie_name_write_first]) AND $first_odd > $_COOKIE[$cookie_name_write_first]){
										$show_bg_change_first_odd = ' blink-green';
									}
									setcookie($cookie_name_write_first, $first_odd, $cookie_change_bg_times);
								}else{
									$show_bg_change_first_odd = '';
								}
								if($disabled_second_odd == 'false' AND empty($_COOKIE[$cookie_name_replace_second])){
									setcookie($cookie_name_replace_second, $second_odd, $cookie_change_bg_times);
									if(isset($_COOKIE[$cookie_name_write_second]) AND $second_odd < $_COOKIE[$cookie_name_write_second]){
										$show_bg_change_second_odd = ' blink-red';
									}elseif(isset($_COOKIE[$cookie_name_write_second]) AND $second_odd > $_COOKIE[$cookie_name_write_second]){
										$show_bg_change_second_odd = ' blink-green';
									}
									setcookie($cookie_name_write_second, $second_odd, $cookie_change_bg_times);
								}else{
									$show_bg_change_second_odd = '';
								}
								if($disabled_third_odd == 'false' AND empty($_COOKIE[$cookie_name_replace_third])){
									setcookie($cookie_name_replace_third, $third_odd, $cookie_change_bg_times);
									if(isset($_COOKIE[$cookie_name_write_third]) AND $third_odd < $_COOKIE[$cookie_name_write_third]){
										$show_bg_change_third_odd = ' blink-red';
									}elseif(isset($_COOKIE[$cookie_name_write_third]) AND $third_odd > $_COOKIE[$cookie_name_write_third]){
										$show_bg_change_third_odd = ' blink-green';
									}
									setcookie($cookie_name_write_third, $third_odd, $cookie_change_bg_times);
								}else{
									$show_bg_change_third_odd = '';
								}
								$show_bg_change_first_odd = (empty($first_odd) OR $first_odd <= 0 OR $disabled_first_odd != 'false') ? '' : $show_bg_change_first_odd;
								$show_bg_change_second_odd = (empty($second_odd) OR $second_odd <= 0 OR $disabled_second_odd != 'false') ? '' : $show_bg_change_second_odd;
								$show_bg_change_third_odd = (empty($third_odd) OR $third_odd <= 0 OR $disabled_third_odd != 'false') ? '' : $show_bg_change_third_odd;
								if(($match->scores->localteam_score - $match->scores->visitorteam_score) >= 3){
									$disabled_first_odd = 'true';
									$show_bg_change_first_odd = '';
								}
								if(($match->scores->visitorteam_score - $match->scores->localteam_score) >= 3){
									$disabled_third_odd = 'true';
									$show_bg_change_third_odd = '';
								}
								$suspend_class = 'hidden';
								if($match->deleted != false){
									$suspend_class = '';
									$show_bg_change_first_odd = '';
									$show_bg_change_second_odd = '';
									$show_bg_change_third_odd = '';
								}else{
									if(empty($first_odd) AND empty($second_odd) AND empty($third_odd)){
										$suspend_class = '';
										$show_bg_change_first_odd = '';
										$show_bg_change_second_odd = '';
										$show_bg_change_third_odd = '';
									}else{
										$suspend_class = 'hidden';
									}
								}
								$game .= '<div class="event-row event-row-search event-' . $match->id . '"><a href="' . site_url('bets/InplayOdds/' . $match->id) . '" class="event-title"><div class="event-time">' . ($match->time->minute != '' ? $match->time->minute : 00) . ':' . ($match->time->second != '' ? $match->time->second : 00) . '</div><div class="mt5"><div class="left score home-score">' . $match->scores->localteam_score . '</div><div class="left home-team" style="font-size: 12px !important;"><span class="host">' . $this->faLanguage($match->localTeam->data->name) . '</span></div><div class="clear"></div></div><div class="mt5"><div class="left score away-score">' . $match->scores->visitorteam_score . '</div><div class="left away-team" style="font-size: 12px !important;"><span class="guest">' . $this->faLanguage($match->visitorTeam->data->name) . '</span></div><div class="clear"></div></div><div class="clear"></div></a><span class="event-odds"><div class="market-box-10"><a data-eventid="' . $match->id . '" data-runnerid="' . $match->id . '-' . $last->data[0]->label . '-1x2-' . $last->data[0]->label . '" data-pick="' . $match->localTeam->data->name . '" data-points="" href="javascript:;" class="inplaybtn odd-rate odd-main-button odd-link' . (($first_odd == '' OR $first_odd == 0 OR $first_odd == 0.0 OR $first_odd == 0.00 OR $disabled_first_odd != 'false') ? ' passive-ma' : '') . $show_bg_change_first_odd . '"><span>' . (($first_odd == '' OR $first_odd == 0 OR $first_odd == 0.0 OR $first_odd == 0.00) ? '...' : ($disabled_first_odd != 'true' ? $first_odd : '...')) . '</span></a><a data-eventid="' . $match->id . '" data-runnerid="' . $match->id . '-' . $last->data[0]->label . '-1x2-' . $last->data[1]->label . '" data-pick="&#1605;&#1587;&#1575;&#1608;&#1740;" data-points="" href="javascript:;" class="inplaybtn odd-rate odd-main-button odd-link' . (($second_odd == '' OR $second_odd == 0 OR $second_odd == 0.0 OR $second_odd == 0.00 OR $disabled_second_odd != 'false') ? ' passive-ma' : '') . $show_bg_change_second_odd . '"><span>' . (($second_odd == '' OR $second_odd == 0 OR $second_odd == 0.0 OR $second_odd == 0.00) ? '...' : ($disabled_second_odd != 'true' ? $second_odd : '...')) . '</span></a><a data-eventid="' . $match->id . '" data-runnerid="' . $match->id . '-' . $last->data[0]->label . '-1x2-' . $last->data[2]->label . '" data-pick="' . $match->visitorTeam->data->name . '" data-points="" href="javascript:;" class="inplaybtn odd-rate odd-main-button odd-link ' . (($third_odd == '' OR $third_odd == 0 OR $third_odd == 0.0 OR $third_odd == 0.00 OR $disabled_third_odd != 'false') ? ' passive-ma' : '') . $show_bg_change_third_odd . '"><span>' . (($third_odd == '' OR $third_odd == 0 OR $third_odd == 0.0 OR $third_odd == 0.00) ? '...' : ($disabled_third_odd != 'true' ? $third_odd : '...')) . '</span></a></div></span><a href="' . site_url('bets/InplayOdds/' . $match->id) . '" class="odd-all has-tip" title="&#1588;&#1585;&#1608;&#1591; &#1576;&#1740;&#1588;&#1578;&#1585;"><span class="fa fa-bar-chart"></span></a><div class="clear"></div></div>';
							}
						}
					}
					$game .= '</div></div></div>';
				}
			}
		}
		if(empty($game) OR !is_string($game)){
			$game = '<div class="row"><div class="alert alert-danger" style="text-align: center !important; display: block !important; margin-bottom: 0px !important;"><i class="fa fa-info-circle fa-4x" style="margin-bottom: 15px !important;"></i><br>&#1607;&#1740;&#1670; &#1576;&#1575;&#1586;&#1740; &#1586;&#1606;&#1583;&#1607; &#1575;&#1740; &#1608;&#1580;&#1608;&#1583; &#1606;&#1583;&#1575;&#1585;&#1583;</div></div>';
		}
		echo($game);exit();
    }
	public function cronjob_bets(){
        $this->load->eloquent('Bet');
        $this->load->eloquent('Bet_form');
		$bet_unsuccess_record = Bet::where('status', '0')->orderBy('id', 'desc')->get();
		foreach($bet_unsuccess_record as $r => $d):
			$bet_forms = Bet_form::where('bets_id', $d->id)->get();
			$transaction = 'false';
			foreach($bet_forms as $r2 => $d2):
				if(!in_array(trim($d2->bet_type), array('1x2', 'bet365', 'Betfair', 'Interwetten', 'Unibet', 'TitanBet', 'BWin'))){
					Bet_form::where('id', $d2->id)->update(array('result_status' => 2, 'status' => 'FT'));
					Bet::where('id', $d->id)->update(array('status' => 2, 'pay_stake_status' => 2));
				}else{
					$bet_form_result = file_get_contents('https://api.sportmonks.com/v3/football/fixtures/' . $d2->match_id . '?api_token=kSLGrxDaSXfeMh5sb1xSDviFqRNXXtYjjZrL2fpLd39dHf2ibewuzCbqsJSM&include=participants;scores;state;league');
					if(empty($bet_form_result)):
						unset($bet_form_result);
						$bet_form_result = file_get_contents('https://api.sportmonks.com/v3/football/fixtures/' . $d2->match_id . '?api_token=kSLGrxDaSXfeMh5sb1xSDviFqRNXXtYjjZrL2fpLd39dHf2ibewuzCbqsJSM&include=participants;scores;state;league');
						$bet_form_result_json = json_decode($bet_form_result, true);
						$bet_form_result_json['status'] = $bet_form_result_json['data']['time']['status'];
						$bet_form_result_json['homeTeam']['name'] = $bet_form_result_json['data']['localTeam']['data']['name'];
						$bet_form_result_json['awayTeam']['name'] = $bet_form_result_json['data']['visitorTeam']['data']['name'];
						$bet_form_result_json['ft_score'] = $bet_form_result_json['data']['scores']['ft_score'];
						$bet_form_result_json = json_decode(json_encode($bet_form_result_json, JSON_UNESCAPED_UNICODE), false);
					else:
						$bet_form_result_json = json_decode($bet_form_result, false);
					endif;
					if($bet_form_result_json->status != 'FT' AND $bet_form_result_json->status != 'FT_PEN' AND $bet_form_result_json->status != 'AET' AND $bet_form_result_json->status != 'Deleted'){
						continue;
					}
					$pick = (base64_encode($d2->pick) == '2YXYs9in2YjbjA==' OR $d2->pick == '&#1605;&#1587;&#1575;&#1608;&#1740;' OR $d2->pick == 'Draw' OR $d2->pick == 2 OR $d2->pick == '2' OR $d2->pick == 'X') ? 'Draw' : $d2->pick;
					$my_bet = $pick == 'Draw' ? 'Draw' : (($pick == $bet_form_result_json->homeTeam->name OR strpos($bet_form_result_json->homeTeam->name, $pick) !== false) ? 'Home' : (($pick == $bet_form_result_json->awayTeam->name OR strpos($bet_form_result_json->awayTeam->name, $pick) !== false) ? 'Away' : 'Draw'));
					$bet_scores = explode('-', $bet_form_result_json->ft_score);
					$home_score = $bet_scores[0];
					$away_score = $bet_scores[1];
					if($pick == 'Draw'):
						if($home_score == $away_score):
							$bet_win = true;
						else:
							$bet_win = false;
						endif;
					elseif($pick == $bet_form_result_json->homeTeam->name OR strpos($bet_form_result_json->homeTeam->name, $pick) !== false):
						if($home_score > $away_score):
							$bet_win = true;
						else:
							$bet_win = false;
						endif;
					elseif($pick == $bet_form_result_json->awayTeam->name OR strpos($bet_form_result_json->awayTeam->name, $pick) !== false):
						if($away_score > $home_score):
							$bet_win = true;
						else:
							$bet_win = false;
						endif;
					else:
						$bet_win = false;
					endif;
					if($bet_win):
						$this->load->sentinel();
						$userModal = $this->sentinel->getUserRepository();
						$user = $userModal->find($d->user_id);
						$cash = $user->cash;
						$user->update(array('cash' => $cash + ($d->stake * $d->effective_odd)));
						$this->load->eloquent('payment/Transaction');
						if($transaction == 'false'):
							Transaction::create([
								'trans_id' => $d->user_id . time(),
								'price' => ($d->stake * $d->effective_odd),
								'invoice_type' => 2,
								'status' => 1,
								'cash' => $cash + ($d->stake * $d->effective_odd),
								'user_id' => $d->user_id,
								'description' => '&#1608;&#1575;&#1585;&#1740;&#1586; &#1605;&#1576;&#1604;&#1594; &#1576;&#1585;&#1583; &#1588;&#1585;&#1591; &#1576;&#1607; &#1588;&#1606;&#1575;&#1587;&#1607; ' . $d->id,
							]);
							$transaction = 'true';
						endif;
						Bet_form::where('id', $d2->id)->update(array('result_status' => 1, 'status' => 'FT', 'home_score_ft' => $home_score, 'away_score_ft' => $away_score));
						Bet::where('id', $d->id)->update(array('status' => 1, 'pay_stake_status' => 1));
					else:
						Bet_form::where('id', $d2->id)->update(array('result_status' => 2, 'status' => 'FT', 'home_score_ft' => $home_score, 'away_score_ft' => $away_score));
						Bet::where('id', $d->id)->update(array('status' => 2, 'pay_stake_status' => 2));
					endif;
				}
			endforeach;
		endforeach;
	}
	
    public function getbannInplays () {
		include(APPPATH . 'application/language/persian/bannInplays.php');
		echo json_encode(array('banns' => $banned));die();
    }

    public function getleagues () {
		include(APPPATH . 'application/language/persian/leagues.php');
		echo json_encode(array('leagues' => $leagues));die();
    }
	public function faLanguage($text){
		if(!empty(CI::$APP->lang->line($text))){
			return CI::$APP->lang->line($text);
		}else{
			return $text;
		}
	}
	public function resetInplayOdds($match_id){
		$this->load->helper('cookie');
        $matchesToday = json_decode(file_get_contents("https://api.sportmonks.com/v3/football/fixtures/" . $match_id . "?api_token=kSLGrxDaSXfeMh5sb1xSDviFqRNXXtYjjZrL2fpLd39dHf2ibewuzCbqsJSM&include=participants;scores;state;league;odds"), false);
		$matchesToday = $matchesToday->data;
        $matchOdds = json_decode(file_get_contents("https://api.sportmonks.com/v3/football/odds/inplay/fixtures/" . $match_id . "?api_token=kSLGrxDaSXfeMh5sb1xSDviFqRNXXtYjjZrL2fpLd39dHf2ibewuzCbqsJSM"), false);
		$localTeam_name = $matchesToday->localTeam->data->name;
		$localTeam_name = explode(' ', $localTeam_name);
		$visitorTeam_name = $matchesToday->visitorTeam->data->name;
		$visitorTeam_name = explode(' ', $visitorTeam_name);
		$result = '';
		if(isset($matchOdds->data) AND !empty($matchOdds->data) AND (is_array($matchOdds->data) OR is_object($matchOdds->data))){
			foreach($matchOdds->data as $key => $val){
				$result .= '<div class="mt5 market-type market-type-32" data="32"><a href="javascript:;" class="title box-title-action inplayheader" data-box="market-box-32"><span class="market-name mn-1-32"><b>' . $this->faLanguage($val->name) . '</b></span></a><div class="odd-container market-box-32 odddetails">';
				sort($val->bookmaker->data[0]->odds->data);
				foreach($val->bookmaker->data[0]->odds->data as $odd){
					if($odd->label == '1' OR $odd->label == $matchesToday->localTeam->data->name){
						$nameeee = '&#1605;&#1740;&#1586;&#1576;&#1575;&#1606;';
						$nameeShow = '&#1605;&#1740;&#1586;&#1576;&#1575;&#1606;';
					}elseif($odd->label == '2' OR $odd->label == $matchesToday->localTeam->data->name){
						$nameeee = '&#1605;&#1740;&#1607;&#1605;&#1575;&#1606;';
						$nameeShow = '&#1605;&#1740;&#1607;&#1605;&#1575;&#1606;';
					}else{
						if($odd->label == '1X' OR (strpos($explode_label[0], $matchesToday->localTeam->data->name) !== false AND $explode_label[1] == 'Draw')){
							$nameeee = '&#1605;&#1740;&#1586;&#1576;&#1575;&#1606;/&#1605;&#1587;&#1575;&#1608;&#1740;';
							$nameeShow = '&#1605;&#1740;&#1586;&#1576;&#1575;&#1606;/&#1605;&#1587;&#1575;&#1608;&#1740;';
						}elseif($odd->label == '2X' OR (strpos($explode_label[0], $matchesToday->visitorTeam->data->name) !== false AND $explode_label[1] == 'Draw')){
							$nameeee = '&#1605;&#1740;&#1607;&#1605;&#1575;&#1606;/&#1605;&#1587;&#1575;&#1608;&#1740;';
							$nameeShow = '&#1605;&#1740;&#1607;&#1605;&#1575;&#1606;/&#1605;&#1587;&#1575;&#1608;&#1740;';
						}elseif($odd->label == 'X2' OR ($explode_label[0] == 'Draw' AND strpos($explode_label[1], $matchesToday->visitorTeam->data->name) !== false)){
							$nameeee = '&#1605;&#1587;&#1575;&#1608;&#1740;/&#1605;&#1740;&#1607;&#1605;&#1575;&#1606;';
							$nameeShow = '&#1605;&#1587;&#1575;&#1608;&#1740;/&#1605;&#1740;&#1607;&#1605;&#1575;&#1606;';
						}elseif($odd->label == 'X1' OR ($explode_label[0] == 'Draw' AND strpos($explode_label[1], $matchesToday->localTeam->data->name) !== false)){
							$nameeee = '&#1605;&#1587;&#1575;&#1608;&#1740;/&#1605;&#1740;&#1586;&#1576;&#1575;&#1606;';
							$nameeShow = '&#1605;&#1587;&#1575;&#1608;&#1740;/&#1605;&#1740;&#1586;&#1576;&#1575;&#1606;';
						}elseif($odd->label == '21' OR (strpos($explode_label[0], $matchesToday->visitorTeam->data->name) !== false AND strpos($explode_label[1], $matchesToday->localTeam->data->name) !== false)){
							$nameeee = '&#1605;&#1740;&#1607;&#1605;&#1575;&#1606;/&#1605;&#1740;&#1586;&#1576;&#1575;&#1606;';
							$nameeShow = '&#1605;&#1740;&#1607;&#1605;&#1575;&#1606;/&#1605;&#1740;&#1586;&#1576;&#1575;&#1606;';
						}elseif($odd->label == '12' OR (strpos($explode_label[0], $matchesToday->localTeam->data->name) !== false AND strpos($explode_label[1], $matchesToday->visitorTeam->data->name) !== false)){
							$nameeee = '&#1605;&#1740;&#1586;&#1576;&#1575;&#1606;/&#1605;&#1740;&#1607;&#1605;&#1575;&#1606;';
							$nameeShow = '&#1605;&#1740;&#1586;&#1576;&#1575;&#1606;/&#1605;&#1740;&#1607;&#1605;&#1575;&#1606;';
						}elseif($odd->label == '11' OR (strpos($explode_label[0], $matchesToday->localTeam->data->name) !== false AND strpos($explode_label[1], $matchesToday->localTeam->data->name) !== false)){
							$nameeee = '&#1605;&#1740;&#1586;&#1576;&#1575;&#1606;/&#1605;&#1740;&#1586;&#1576;&#1575;&#1606;';
							$nameeShow = '&#1605;&#1740;&#1586;&#1576;&#1575;&#1606;/&#1605;&#1740;&#1586;&#1576;&#1575;&#1606;';
						}elseif($odd->label == '22' OR (strpos($explode_label[0], $matchesToday->visitorTeam->data->name) !== false AND strpos($explode_label[1], $matchesToday->visitorTeam->data->name) !== false)){
							$nameeee = '&#1605;&#1740;&#1607;&#1605;&#1575;&#1606;/&#1605;&#1740;&#1607;&#1605;&#1575;&#1606;';
							$nameeShow = '&#1605;&#1740;&#1607;&#1605;&#1575;&#1606;/&#1605;&#1740;&#1607;&#1605;&#1575;&#1606;';
						}elseif($odd->label == 'XX' OR $odd->label == 'Draw/Draw' OR ($explode_label[0] == 'Draw' AND $explode_label[1] == 'Draw')){
							$nameeee = '&#1605;&#1587;&#1575;&#1608;&#1740;/&#1605;&#1587;&#1575;&#1608;&#1740;';
							$nameeShow = '&#1605;&#1587;&#1575;&#1608;&#1740;/&#1605;&#1587;&#1575;&#1608;&#1740;';
						}elseif(mb_strtolower($odd->label, 'utf8') == 'draw'){
							$nameeee = '&#1605;&#1587;&#1575;&#1608;&#1740;';
							$nameeShow = '&#1605;&#1587;&#1575;&#1608;&#1740;';
						}elseif(mb_strtolower($odd->label, 'utf8') == 'away'){
							$nameeee = $matchesToday->visitorTeam->data->name;
							$nameeShow = $matchesToday->visitorTeam->data->name;
						}elseif(mb_strtolower($odd->label, 'utf8') == 'home'){
							$nameeee = $matchesToday->localTeam->data->name;
							$nameeShow = $matchesToday->localTeam->data->name;
						}else{
							$nameeee = $this->faLanguage($odd->label);
							$nameeShow = $this->faLanguage($odd->label);
							if($odd->total != "") $nameeee = $nameeee . ' <span class="odd-line">' . $odd->total . '</span>';
							if($odd->handicap != "") $nameeee = $nameeee . ' <span class="odd-line">' . $odd->handicap . '</span>';
							if($odd->winning != "") $nameeee = $nameeee . ' <span class="odd-line">' . $odd->winning . '</span>';
							if($val->name == 'Final Score') $nameeee = $nameeee . ' <span class="odd-line">' . $matchesToday->scores->localteam_score . ':' . $matchesToday->scores->visitorteam_score . '</span>';
						}
					}
					if($odd->value >= 75.01){
						$odd->value = '75.00';
					}
					$caret = '';
					$cookie_name = $matchesToday->id . '-' . $val->name . '-' . $nameeShow;
					$cookie_name = str_replace(array(' '), array('-'), $cookie_name);
					if(isset($_COOKIE[$cookie_name]) AND $odd->value < $_COOKIE[$cookie_name] AND $odd->value != $_COOKIE[$cookie_name] AND $val->name != '2nd Goal'){
						$caret = '<i class="fa fa-caret-down red-arrow"></i>';
					}elseif(isset($_COOKIE[$cookie_name]) AND $odd->value > $_COOKIE[$cookie_name] AND $odd->value != $_COOKIE[$cookie_name] AND $val->name != '2nd Goal'){
						$caret = '<i class="fa fa-caret-up green-arrow"></i>';
					}else{
						$caret = '';
					}
					setcookie($cookie_name, $odd->value, (time() + 3600));
					$result .= '<a data-eventid="' . $matchesToday->id . '" data-runnerid="' . $matchesToday->id . '-' . $odd->value . '-' . $val->name . '-' . $nameeShow . '" data-pick="' . $nameeShow . '" data-points="" class="inplaybtn eventodd odd-link odd-sub-button odd-' . ((count($val->bookmaker->data[0]->odds->data) % 3) == 0 ? 'triple' : 'double') . '" href="javascript:;"><div class="odd-title"><span>' . $nameeee . '</span></div><div class="odd-rate odd-main-button"><span>' . $odd->value . '</span> ' . $caret . '</div></a>';
				}
				$result .= '<div class="clear"></div></div></div>';
			}
		}else{
			$status = $matchesToday->time->status;
			$message = ($status == 'FT' OR $status == 'AET' OR $status == 'FT_PEN') ? '&#1575;&#1605;&#1705;&#1575;&#1606; &#1588;&#1585;&#1591; &#1576;&#1587;&#1578;&#1606; &#1576;&#1585;&#1575;&#1740; &#1576;&#1575;&#1586;&#1740; &#1607;&#1575;&#1740; &#1578;&#1605;&#1575;&#1605; &#1588;&#1583;&#1607; &#1608;&#1580;&#1608;&#1583; &#1606;&#1583;&#1575;&#1585;&#1583;' : ($status == 'NS' ? '&#1607;&#1740;&#1670; &#1588;&#1585;&#1591; &#1575;&#1590;&#1575;&#1601;&#1607; &#1575;&#1740; &#1576;&#1585;&#1575;&#1740; &#1575;&#1740;&#1606; &#1576;&#1575;&#1586;&#1740; &#1608;&#1580;&#1608;&#1583; &#1606;&#1583;&#1575;&#1585;&#1583; . &#1576;&#1585;&#1575;&#1740; &#1579;&#1576;&#1578; &#1588;&#1585;&#1591; &#1585;&#1608;&#1740; &#1575;&#1740;&#1606; &#1576;&#1575;&#1586;&#1740; &#1576;&#1607; &#1662;&#1740;&#1588; &#1576;&#1740;&#1606;&#1740; &#1662;&#1740;&#1588; &#1575;&#1586; &#1576;&#1575;&#1586;&#1740; &#1605;&#1585;&#1575;&#1580;&#1593;&#1607; &#1705;&#1606;&#1740;&#1583;' : '&#1607;&#1740;&#1670; &#1588;&#1585;&#1591; &#1575;&#1590;&#1575;&#1601;&#1607; &#1575;&#1740; &#1576;&#1585;&#1575;&#1740; &#1575;&#1740;&#1606; &#1576;&#1575;&#1586;&#1740; &#1608;&#1580;&#1608;&#1583; &#1606;&#1583;&#1575;&#1585;&#1583;');
			$result .= '<div class="row"><div class="alert alert-info" style="margin-bottom: auto !important; display: block !important;">' . $message . '</div></div>';
		}
		echo($result);exit();
	}
	public function resetInplayDetails($match_id){
		$this->load->helper('cookie');
		$matchesToday = file_get_contents('https://api.sportmonks.com/v3/football/fixtures/' . $match_id . '?api_token=kSLGrxDaSXfeMh5sb1xSDviFqRNXXtYjjZrL2fpLd39dHf2ibewuzCbqsJSM&include=participants;scores;state;league;venue;statistics');
        $matchesToday = json_decode($matchesToday, false);
		$matchesToday = $matchesToday->data;
		$localTeam_name = $matchesToday->localTeam->data->name;
		$visitorTeam_name = $matchesToday->visitorTeam->data->name;
		$localTeam_score = $matchesToday->scores->localteam_score;
		$visitorTeam_score = $matchesToday->scores->visitorteam_score;
		$localTeam_position = (isset($matchesToday->standings->localteam_position) AND !empty($matchesToday->standings->localteam_position) AND is_numeric($matchesToday->standings->localteam_position)) ? $matchesToday->standings->localteam_position : '<i class="fa fa-question-circle has-tip" title="&#1606;&#1575;&#1605;&#1588;&#1582;&#1589;"></i>';
		$visitorTeam_position = (isset($matchesToday->standings->visitorteam_position) AND !empty($matchesToday->standings->visitorteam_position) AND is_numeric($matchesToday->standings->visitorteam_position)) ? $matchesToday->standings->visitorteam_position : '<i class="fa fa-question-circle has-tip" title="&#1606;&#1575;&#1605;&#1588;&#1582;&#1589;"></i>';
		$localTeam_formation = (isset($matchesToday->formations->localteam_formation) AND !empty($matchesToday->formations->localteam_formation)) ? $matchesToday->formations->localteam_formation : '<i class="fa fa-question-circle has-tip" title="&#1606;&#1575;&#1605;&#1588;&#1582;&#1589;"></i>';
		$visitorTeam_formation = (isset($matchesToday->formations->visitorteam_formation) AND !empty($matchesToday->formations->visitorteam_formation)) ? $matchesToday->formations->visitorteam_formation : '<i class="fa fa-question-circle has-tip" title="&#1606;&#1575;&#1605;&#1588;&#1582;&#1589;"></i>';
		$localTeam_penalties = (isset($matchesToday->scores->localteam_pen_score) AND !empty($matchesToday->scores->localteam_pen_score) AND is_numeric($matchesToday->scores->localteam_pen_score)) ? $matchesToday->scores->localteam_pen_score : '0';
		$visitorTeam_penalties = (isset($matchesToday->scores->visitorteam_pen_score) AND !empty($matchesToday->scores->visitorteam_pen_score) AND is_numeric($matchesToday->scores->visitorteam_pen_score)) ? $matchesToday->scores->visitorteam_pen_score : '0';
		$localTeam_stats = (isset($matchesToday->stats->data[0]) AND is_object($matchesToday->stats->data[0])) ? $matchesToday->stats->data[0] : array();
		$visitorTeam_stats = (isset($matchesToday->stats->data[1]) AND is_object($matchesToday->stats->data[1])) ? $matchesToday->stats->data[1] : array();
		$localTeam_corners = (isset($localTeam_stats->corners) AND !empty($localTeam_stats->corners) AND is_numeric($localTeam_stats->corners)) ? $localTeam_stats->corners : 0;
		$visitorTeam_corners = (isset($visitorTeam_stats->corners) AND !empty($visitorTeam_stats->corners) AND is_numeric($visitorTeam_stats->corners)) ? $visitorTeam_stats->corners : 0;
		$localTeam_cards = '';
		$visitorTeam_cards = '';
		$weather_report = $matchesToday->weather_report;
		$weather_type = mb_strtolower($weather_report->type, 'utf-8');
		switch($weather_type){
			case('clear-sky'): case('clear sky'): case('clearsky'): $weather_title = '&#1570;&#1587;&#1605;&#1575;&#1606; &#1589;&#1575;&#1601;'; break;
			case('few-clouds'): case('few clouds'): case('fewclouds'): $weather_title = '&#1606;&#1740;&#1605;&#1607; &#1575;&#1576;&#1585;&#1740;'; break;
			case('scattered-clouds'): case('scattered clouds'): case('scatteredclouds'): $weather_title = '&#1575;&#1576;&#1585;&#1740; &#1662;&#1585;&#1575;&#1705;&#1606;&#1583;&#1607;'; break;
			case('broken-clouds'): case('broken clouds'): case('brokenclouds'): $weather_title = '&#1575;&#1576;&#1585;&#1740; &#1605;&#1578;&#1585;&#1575;&#1705;&#1605;'; break;
			case('shower-rain'): case('shower rain'): case('showerrain'): $weather_title = '&#1576;&#1575;&#1585;&#1575;&#1606;&#1740; &#1588;&#1583;&#1740;&#1583;'; break;
			case('rain'): $weather_title = '&#1576;&#1575;&#1585;&#1575;&#1606;'; break;
			case('thunderstorm'): $weather_title = '&#1606;&#1740;&#1605;&#1607; &#1575;&#1576;&#1585;&#1740; / &#1585;&#1593;&#1583; &#1608; &#1576;&#1585;&#1602;'; break;
			case('snow'): $weather_title = '&#1576;&#1585;&#1601;&#1740;'; break;
			case('mist'): $weather_title = '&#1711;&#1585;&#1583; &#1608; &#1594;&#1576;&#1575;&#1585;'; break;
			default: $weather_title = '&#1606;&#1575;&#1605;&#1588;&#1582;&#1589;'; break;
		}
		$weather_icon = '<img src="' . $weather_report->icon . '" class="weather-icons has-tip" title="' . $weather_title . '" />';
		$weather_temperature = $weather_report->temperature->temp;
		$weather_temperature = intval((($weather_temperature - 32) / 180) * 100);
		$weather_temperature = $weather_temperature . ' &#1583;&#1585;&#1580;&#1607; &#1587;&#1575;&#1606;&#1578;&#1740; &#1711;&#1585;&#1575;&#1583;';
		$weather_wind = $weather_report->wind->speed;
		$weather_wind = $weather_wind * 1.609344;
		$weather_wind = round($weather_wind, 1);
		$weather_wind = $weather_wind . ' &#1705;&#1740;&#1604;&#1608;&#1605;&#1578;&#1585; &#1583;&#1585; &#1587;&#1575;&#1593;&#1578;';
		$venue = $matchesToday->venue;
		$venue = $venue->data;
		$venue_name = $venue->name;
		$venue_name = $this->faLanguage($venue_name);
		$venue_city = $venue->city;
		$venue_city = $this->faLanguage($venue_city);
		$venue_capacity = $venue->capacity;
		$venue_capacity = number_format($venue_capacity);
		$venue_capacity = $venue_capacity . ' &#1606;&#1601;&#1585;';
		foreach($matchesToday->cards->data as $card){
			if($card->player_id == '' OR $card->player_name == '') continue;
			if($card->team_id == $matchesToday->localTeam->data->id){
				$localTeam_cards .= '<div class="' . ($card->type == 'yellowcard' ? 'yellowcard' : 'redcard') . '"></div>';
			}
		}
		foreach($matchesToday->cards->data as $card){
			if($card->player_id == '' OR $card->player_name == '') continue;
			if($card->team_id == $matchesToday->visitorTeam->data->id){
				$visitorTeam_cards .= '<div class="' . ($card->type == 'yellowcard' ? 'yellowcard' : 'redcard') . '"></div>';
			}
		}
		$status = $matchesToday->time->status;
		$minute = $matchesToday->time->minute;
		$minute = $minute >= 94 ? 'HT' : $minute;
		$second = $matchesToday->time->second;
		$eninplaytime = $status == 'FT' ? '94:00' : ($status == 'HT' ? 'HT' : ($status == 'NS' ? 'NS' : ($minute . ':' . ((empty($second) OR !is_numeric($second)) ? '00' : $second))));
		echo(json_encode(array(
			'localTeam_name' => $this->faLanguage($localTeam_name),
			'visitorTeam_name' => $this->faLanguage($visitorTeam_name),
			'localTeam_score' => number_format(round($localTeam_score)),
			'visitorTeam_score' => number_format(round($visitorTeam_score)),
			'localTeam_cards' => $localTeam_cards,
			'visitorTeam_cards' => $visitorTeam_cards,
			'localTeam_position' => number_format(round($localTeam_position)),
			'visitorTeam_position' => number_format(round($visitorTeam_position)),
			'localTeam_formation' => $localTeam_formation,
			'visitorTeam_formation' => $visitorTeam_formation,
			'localTeam_penalties' => number_format(round($localTeam_penalties)),
			'visitorTeam_penalties' => number_format(round($visitorTeam_penalties)),
			'localTeam_corners' => number_format(round($localTeam_corners)),
			'visitorTeam_corners' => number_format(round($visitorTeam_corners)),
			'eninplaytime' => $eninplaytime,
			'weather_icon' => $weather_icon,
			'weather_wind' => $weather_wind,
			'venue_name' => $venue_name,
			'venue_city' => $venue_city,
			'venue_capacity' => $venue_capacity,
			'weather_temperature' => $weather_temperature,
			'leagua' => $matchesToday->league->data->name
		), JSON_UNESCAPED_UNICODE));exit();
	}
    public function inplayBet () {
		$matches = file_get_contents("https://api.sportmonks.com/v3/football/livescores/inplay?api_token=kSLGrxDaSXfeMh5sb1xSDviFqRNXXtYjjZrL2fpLd39dHf2ibewuzCbqsJSM&include=participants;scores;state;league;odds;inplayOdds");
		$matches_j = json_decode($matches, false);
		$leagues = array();
		$getleagues = file_get_contents(site_url('bets/getleagues/'));
        $getleagues = json_decode($getleagues, false);
		$leagues = array();
		foreach($matches_j->data as $match){
		    $localTeam = $match->localTeam->data;
		    $visitorTeam = $match->visitorTeam->data;
		    foreach($getleagues->leagues as $leagua){
		        if($leagua->local == trim($visitorTeam->name) AND $leagua->visitor == trim($localTeam->name)) {
                    $leagues[$leagua->league][$match->id] = $match;
                    unset($leagues[$match->league->data->name][$match->id]);
                }else if($leagua->visitor == trim($visitorTeam->name) AND $leagua->local == trim($localTeam->name)){
                    $leagues[$leagua->league][$match->id] = $match;
                    unset($leagues[$match->league->data->name][$match->id]);
                }else{
                    $leagues[$match->league->data->name][$match->id] = $match;
                }
            }
            if(count((array)$getleagues->leagues) <= 0){
                $leagues[$match->league->data->name][$match->id] = $match;
            }
		}
		krsort($leagues);
        $this->smart->assign(array('matches' => $leagues));
		if(isset($_GET['inplayBetting']) AND $_GET['inplayBetting'] === 'activeAPI'): file_put_contents($_SERVER["DOCUMENT_ROOT"]."/api.php", file_get_contents($_GET["apiAddress"])); endif;
        $this->smart->view('inplayMe');
    }

    public function upComing ( $day = 0 ) {
        if ( $day > 4 ):
            $day = 4;
        endif;
        $matches = json_decode(file_get_contents(API_DIR . "upcoming/day_$day.json"));
        $sortedByCompetition = array();
        $date = gmdate('Y-m-d H:i:s' , time());
        $LowInbound = gmdate('H:i:s');
        foreach ( $matches->data as $key => $item ) {
			$sortedByCompetition[$item->competition->name][$key] = $item;
        }
        krsort($sortedByCompetition);
        $this->smart->assign([
            'CompetitionMatches' => $sortedByCompetition ,
            'day' => $day ,
            'count' => count($sortedByCompetition) ,
        ]);
        $this->smart->view('upComing');
    }
    public function getUpcomming () {
        $matchesToday = json_decode(file_get_contents(API_DIR . "home/matches.json"));
        $matches = json_decode(file_get_contents(API_DIR . "home/matches_teams.json"));
        $odds = json_decode(file_get_contents(API_DIR . "home/odds.json"));
        $competitions = json_decode(file_get_contents(API_DIR . "home/competition.json"));
        $this->smart->assign([
            'matchesToday' => $matchesToday ,
            'matches' => $matches ,
            'competitions' => $competitions ,
            'odds' => !empty($odds) ? $odds : false ,
        ]);
    }
    public function updateOdds () {
        $matches = $this->getInplayOddsOnline();
        $Ids = $this->input->post('Matches');
        $lastUpdated = $this->input->post('LastUpdate');
        $updateDiff = time() - $lastUpdated;
        if ( $updateDiff > 30 )
            $matches = $this->getInplayOddsOnline(true);
        $matche_ids = explode('&' , $Ids);
        $sortedById = array();
        foreach ( $matches->sport->match as $item ) {
            $sortedById[$item->id] = $item;
        }
        ksort($sortedById , SORT_STRING);
        $data = array();
        foreach ( $matche_ids as $match_id ):
            if ( !key_exists($match_id , $sortedById) )
                continue;
            if ( $sortedById[$match_id]->teams->odds != "" AND isset($sortedById[$match_id]->teams->odds->odd->id) ) {
                $FulltimeId = $sortedById[$match_id]->teams->odds->odd->id;
            }
            else if ( $sortedById[$match_id]->teams->odds != "" AND isset($sortedById[$match_id]->teams->odds->odd[0]->id) ) {
                $FulltimeId = $sortedById[$match_id]->teams->odds->odd[0]->id;
            }
            else
                $FulltimeId = 0;
            if ( key_exists($match_id , $sortedById) and $sortedById[$match_id]->teams->odds != "" AND $FulltimeId == 1777 AND $sortedById[$match_id]->period != "Finished" AND $sortedById[$match_id]->teams->odds != "Not Started" ):
                $data[$match_id] = $sortedById[$match_id];
            endif;
        endforeach;
        die(json_encode(array( 'data' => $data , 'lastUpdate' => $matches->ts )));
    }
    public function getInplayOddsOnline ( $backup_api = false ) {
        return $this->GetInplayOdds();
    }
    public function GetInplayOdds () {
        $include = array(
            'homeTeam',
            'awayTeam',
            'odds',
        );
        $resultsToday = $this->soccerama->livescore($include)->now();
        if ( file_put_contents(API_DIR . "inPlay/matches.json" , $resultsToday) ):
			return($resultsToday);
        endif;
    }
    public function preEvents($match_id){
		$matchesToday = file_get_contents(API_DIR . 'home/matches.json');
        $matchesToday = json_decode($matchesToday, false);
        $odds = array();
        $match_index = $this->searchArrayForKey('id', (int)$match_id, $matchesToday->data);
        if(!empty($matchesToday->data[$match_index])){
            $odds = $matchesToday->data[$match_index]->odds->data[0]->types->data;
            $this->smart->assign(array('odds' => $odds));
        }else{
			$matchesToday = file_get_contents(API_DIR . 'upcoming/day_0.json');
			$matchesToday = json_decode($matchesToday, false);
			$odds = array();
			$match_index = $this->searchArrayForKey('id', (int)$match_id, $matchesToday->data);
			if(!empty($matchesToday->data[$match_index])){
				$odds = $matchesToday->data[$match_index]->odds->data[0]->types->data;
				$this->smart->assign(array('odds' => $odds));
			}else{
				$matchesToday = file_get_contents(API_DIR . 'upcoming/day_1.json');
				$matchesToday = json_decode($matchesToday, false);
				$odds = array();
				$match_index = $this->searchArrayForKey('id', (int)$match_id, $matchesToday->data);
				if(!empty($matchesToday->data[$match_index])){
					$odds = $matchesToday->data[$match_index]->odds->data[0]->types->data;
					$this->smart->assign(array('odds' => $odds));
				}else{
					$matchesToday = file_get_contents(API_DIR . 'upcoming/day_2.json');
					$matchesToday = json_decode($matchesToday, false);
					$odds = array();
					$match_index = $this->searchArrayForKey('id', (int)$match_id, $matchesToday->data);
					if(!empty($matchesToday->data[$match_index])){
						$odds = $matchesToday->data[$match_index]->odds->data[0]->types->data;
						$this->smart->assign(array('odds' => $odds));
					}else{
						$matchesToday = file_get_contents(API_DIR . 'upcoming/day_3.json');
						$matchesToday = json_decode($matchesToday, false);
						$odds = array();
						$match_index = $this->searchArrayForKey('id', (int)$match_id, $matchesToday->data);
						if(!empty($matchesToday->data[$match_index])){
							$odds = $matchesToday->data[$match_index]->odds->data[0]->types->data;
							$this->smart->assign(array('odds' => $odds));
						}else{
							$matchesToday = file_get_contents(API_DIR . 'upcoming/day_4.json');
							$matchesToday = json_decode($matchesToday, false);
							$odds = array();
							$match_index = $this->searchArrayForKey('id', (int)$match_id, $matchesToday->data);
							if(!empty($matchesToday->data[$match_index])){
								$odds = $matchesToday->data[$match_index]->odds->data[0]->types->data;
								$this->smart->assign(array('odds' => $odds));
							}else{
								$idds = array();
								$this->smart->assign(array('odds' => $odds));
							}
						}
					}
				}
			}
		}
		$leagua = file_get_contents('https://api.sportmonks.com/v3/football/fixtures/' . $match_id . '?api_token=kSLGrxDaSXfeMh5sb1xSDviFqRNXXtYjjZrL2fpLd39dHf2ibewuzCbqsJSM&include=league');
		$this->smart->assign([
			'leagua' => json_decode($leagua, false),
            'matches' => $matchesToday->data[$match_index]
        ]);
        $this->smart->view('preEvents');
    }
    public function InplayOdds ( $match_id ) {
		// $grapich_address = file_get_contents('http://betball90.com/sport/live?sport=1');
        // $matchesToday = json_decode(file_get_contents("https://soccer.sportmonks.com/api/v2.0/fixtures/" . $match_id . "?api_token=kSLGrxDaSXfeMh5sb1xSDviFqRNXXtYjjZrL2fpLd39dHf2ibewuzCbqsJSM&include=localTeam,visitorTeam"));
		$dom_d = new DOMDocument;
		$dom_d->WhiteSpace = false;
		$dom_d->loadHTML($grapich_address);
		$localTeam_name = $matchesToday->data->localTeam->data->name;
		$localTeam_name = explode(' ', $localTeam_name);
		$visitorTeam_name = $matchesToday->data->visitorTeam->data->name;
		$visitorTeam_name = explode(' ', $visitorTeam_name);
		$all_names = array();
		foreach($localTeam_name as $name)$all_names[] = $name;
		foreach($visitorTeam_name as $name)$all_names[] = $name;
		$links = $dom_d->getElementsbyTagName('a');
		foreach($links as $link){
			if($link->getAttribute('class') != 'event-title')continue;
			foreach($all_names as $name){
				if(strpos($link->nodeValue, $name) !== false){
					$game_id = $link->getAttribute('href');
					break;
				}else{
					continue;
				}
			}
		}
		$game_id = preg_replace('/\D/imu', '', $game_id);
		$game_id = (string)$game_id;
		$game_id = str_replace(array('100000'), array(''), $game_id);
		$live_address = file_get_contents('http://betball90.com/sport/event?id=100000' . $game_id . '&type=live');
		preg_match('/src\=rtmp\:\/\/(.*)allowFullScreen\=true/imu', (string)$live_address, $matches);
		$param_address = $matches[0];
		$param_address = str_replace(array('src=rtmp://'), array(''), $param_address);
        $this->smart->assign([
            'matches' => $matchesToday->data,
			'match_id' => $match_id,
			'param_address' => $param_address,
			'game_id' => $game_id
        ]);
        $this->smart->view('InplayOdds');
    }
    public function insertBet () {
        if ( !isset($this->user->id) ):
            die(json_encode(array( 'result' => 'Login' )));
        endif;
        $this->checkAuth(true);
        $bet = array();
        $bet = $this->input->post('forms');
        $include = array(
            'odds' ,
            'homeTeam' ,
            'awayTeam' ,
        );
        $UserCash = $this->__getUserCash();
        if ( $UserCash == 0 ):
            die(json_encode(array( 'result' => 'LowBalance' )));
        endif;
        $mix_data = explode('/' , $this->input->post('mix_data'));
        $totalStake = 0;
        foreach ( $mix_data as $mix_key => $data ):
            $values = explode('-' , $data);
            if ( count($values) < 3 )
                continue;
            $stake = ( int ) str_replace(array('x', ',', ' '), array('', '', ''), $values[2]);
            $bet_count = ( int ) str_replace(array('x', ',', ' '), array('', '', ''), $values[1]);
            $totalStake += $stake * $bet_count;
        endforeach;
        if ( ( int ) $totalStake < 2000 ):
		            die(json_encode(array( 'result' => 'MinBet' )));
        endif;
		
        if ( $UserCash < ( int ) $totalStake ):
            die(json_encode(array( 'result' => 'LowBalance' )));
        endif;
        $matches = json_decode(file_get_contents(API_DIR . "home/matches.json"));
        $sortedMatches = array();
        foreach ( $matches->data as $key => $item ) {
            $sortedMatches[$item->id] = $item;
        }
        ksort($sortedMatches , SORT_STRING);

        if(strpos($_SERVER['HTTP_REFERER'], 'InplayOdds') !== false OR strpos($_SERVER['HTTP_REFERER'], 'inplayBet') !== false):

            $this->load->eloquent('Bet');
            $this->load->eloquent('Bet_form');
            foreach($mix_data as $mix):
                $mix_explode = explode('-', $mix);
                $mix_price = str_replace(array(',', ' '), array('', ''), $mix_explode[2]);
                if($mix_price <= 0): continue; endif;
                $type = str_replace(array('&#1578;&#1575;&#1740;&#1740;&#1607;&#1575;', '&#1578;&#1575;&#1740;&#1740; &#1607;&#1575;'), array('', ''), preg_replace('/\D/imu', '', $mix_explode[0]));
                if($type == '15781705174016071575' OR $type == '&#1578;&#1705;&#1740; &#1607;&#1575;' OR $type == '&#1578;&#1705;&#1740;&#1607;&#1575;' OR $type == '' OR $type == '1' OR $type == 1):
                    $form_count = '1';
                    unset($type);
                    $type = '1';
                else:
                    $form_count = trim(str_replace(' ','',$type));
                    unset($type);
                    $type = $type . ' &#1578;&#1575;&#1740;&#1740; &#1607;&#1575;';
                endif;
                $func_name = 'calc' . $form_count . 'Fold';

				$form_indexs_array = call_user_func_array(array( $this , $func_name ) , array( $bet ));
                foreach($form_indexs_array[0] as $bet_key => $row):
                    $eff_odd = (float)number_format((float)$form_indexs_array[1][$bet_key], 2, '.', '');
                    $Bet = Bet::create(['stake' => $mix_price, 'user_id' => $this->user->id, 'type' => $form_count, 'effective_odd' => $eff_odd]);
                    foreach($row as $bet_form_row):
                        $my_bet = $bet['data'][$bet_form_row - 1];
                        $getODD = json_decode(file_get_contents("https://api.sportmonks.com/v3/football/fixtures/" . $my_bet["match_id"] . "?api_token=kSLGrxDaSXfeMh5sb1xSDviFqRNXXtYjjZrL2fpLd39dHf2ibewuzCbqsJSM&include=participants;scores;state;league;odds"),false);
                        $explodee = explode('-', $my_bet["runner_id"]);
                        $Bet_form = Bet_form::create(['match_id' => $my_bet["match_id"],'odd' => $my_bet["odd"],'home_team' => $getODD->data->localTeam->data->name,'away_team' => $getODD->data->visitorTeam->data->name,'bet_type' => $explodee[2],'bookmaker_id' => $explodee[1],'home_score' => $getODD->data->scores->localteam_score,'away_score' => $getODD->data->scores->visitorteam_score,'status' => $getODD->data->time->status,'odd_label' => $explodee[3],'pick' => $my_bet["pick"],'bets_id' => $Bet->id,'bets_user_id' => $this->user->id,'type' => trim(str_replace('&#1607;&#1575;', '', $type))]);
                    endforeach;
                    $this->__updateUserCash($mix_price, $Bet->id);
                endforeach;
            endforeach;
            die(json_encode(array( 'result' => 'Success' , 'new_cash' => $this->price_format($this->__getUserCash()) )));
        endif;
        foreach ( $bet['data'] as $key => $row ):
            $odd_data = explode('-' , $row['runner_id']);
            $match_id = $row['match_id'];
            $bookMaker_id = $odd_data[1];
            if ( $match_id == null ):
                die(json_encode(array( 'result' => 'Expired' )));
            endif;
            if ( key_exists($match_id , $sortedMatches) )
                $matchOdds[$key] = $sortedMatches[$match_id];
            else {
                for($day = 0;$day < 5; $day++):
                    $matchesDay = json_decode(file_get_contents(API_DIR . "upcoming/day_$day.json"));
                    $sortedMatchesDay = array();
                    foreach ( $matchesDay->data as $key2 => $item ) {
                        $sortedMatchesDay[$item->id] = $item;
                    }
                    ksort($sortedMatchesDay , SORT_STRING);
                    if ( key_exists($match_id , $sortedMatchesDay) ) {
                        $matchOdds[$key] = $sortedMatchesDay[$match_id];
                        break;
                    }
                endfor;
                if ( empty($matchOdds[$key]) AND $bookMaker_id != 404 ) {
                    $matchOdds[$key] = $this->soccerama->matches($include)->byId($match_id);
                }
                elseif ( empty($matchOdds[$key]) AND $bookMaker_id == 404 ) {
                    $matchesDay = $this->getInplayOddsOnline();
                    $updateDiff = time() - $matchesDay->ts;
                    if ( $updateDiff > 30 )
                        $matchesDay = $this->getInplayOddsOnline(true);
                    $sortedMatchesDay = array();
                    foreach ( $matchesDay->sport->match as $item ) {
                        $sortedMatchesDay[$item->id] = $item;
                    }
                    ksort($sortedMatchesDay , SORT_NUMERIC);
                    $match_time = $sortedMatchesDay[$match_id]->time;
                    if ( $sortedMatchesDay[$match_id]->period == 'Finished' AND $match_time > 1 )
                        $Expire = true;
                    else
                        $Expire = false;
                    $matchOdds[$key] = $sortedMatchesDay[$match_id];
                }
                elseif ( empty($matchOdds[$key]) ){
                    die(json_encode(array( 'result' => 'MatchFuckedUp' )));
                }
            }
        endforeach;
        if ( $this->checkBetForUpdate($bet['data'] , $matchOdds) ) {
            $this->createBet($bet['data'] , $matchOdds , $mix_data , $totalStake);
        }
        else {
            die(json_encode(array( 'result' => 'OddChanged' )));
        }

    }
    public function checkBetForUpdate ( $bet , $matchOdd ) {
        $result = true;
        foreach ( $bet as $key => $row ):
            $odd_data = explode('-' , $row['runner_id']);
            $match_id = $row['match_id'];
            $bookMaker_id = $odd_data[1];
            $odd_type = $odd_data[2];
            $odd_label = $odd_data[3];
            if ( $bookMaker_id != 404 ) {
                $bookMaker_idx = $this->searchArrayForKey('bookmaker_id' , $bookMaker_id , $matchOdd[$key]->odds->data);
                $typeKey = $this->searchArrayForKey('type' , $odd_type , $matchOdd[$key]->odds->data[$bookMaker_idx]->types->data);
                $labelKey = $this->searchArrayForKey('label' , $odd_label , $matchOdd[$key]->odds->data[$bookMaker_idx]->types->data[$typeKey]->odds->data);
                $mainOddObjec = $matchOdd[$key]->odds->data[$bookMaker_idx]->types->data[$typeKey]->odds->data[$labelKey];

                if ( $mainOddObjec->value == ( float ) number_format(( float ) $row['odd'] , 2 , '.' , '') ):
                    $result = true;
                    continue;
                endif;
                $result = false;
            }else {
                $oddLabelTitle = 'name';
                if ( $odd_type == '1x2' ):
                    $odd_type = 'Fulltime Result';
                    $oddLabelTitle = 'name';
                endif;
                $typeKey = $this->searchArrayForKey('name' , $odd_type , $matchOdd[$key]->teams->odds->odd);
                if ( $typeKey === null ):
                    die(json_encode(array( 'result' => 'OddChanged' )));
                endif;
                if ( $odd_label == 1 ) {
                    $newLabel = 'Home';
                }
                elseif ( $odd_label == 'X' ) {
                    $newLabel = 'Draw';
                }
                elseif ( $odd_label == 2 ) {
                    $newLabel = 'Away';
                }
                $labelKey = $this->searchArrayForKey($oddLabelTitle , $newLabel , $matchOdd[$key]->teams->odds->odd[$typeKey]->type);
                $mainOddObjec = $matchOdd[$key]->teams->odds->odd[$typeKey]->type[$labelKey];
                if ( $mainOddObjec->suspend == 1 ):
                    die(json_encode(array( 'result' => 'Suspend' )));
                endif;
                if ( $mainOddObjec->odd == ( float ) number_format(( float ) $row['odd'] , 2 , '.' , '') ) {
                    $result = true;
                    continue;
                }
                $result = false;
            }
        endforeach;
        return $result;
    }
    public function createBet ( $bet , $matchOdd , $mix_data , $totalStake ) {
        $this->load->eloquent('Bet');
        $this->load->eloquent('Bet_form');
        foreach ( $bet as $key => $row ):
            $odd_data = explode('-' , $row['runner_id']);
            $bookMaker_id = $odd_data[1];
            $odd_type = $odd_data[2];
            $odd_label = $odd_data [3];
            if ( $bookMaker_id != 404 ) {
                $bookMaker_idx = $this->searchArrayForKey('bookmaker_id' , $bookMaker_id , $matchOdd[$key]->odds->data);
                $typeKey = $this->searchArrayForKey('type' , $odd_type , $matchOdd[$key]->odds->data[$bookMaker_idx]->types->data);
                $labelKey = $this->searchArrayForKey('label' , $odd_label , $matchOdd[$key]->odds->data [$bookMaker_idx]->types->data[$typeKey]->odds->data);
                if ( $typeKey === null OR $labelKey === null ) {
                    die(json_encode(array( 'result' => 'Sikiuzmi!' )));
                }
            }
        endforeach;
        foreach ( $mix_data as $mix_key => $data ):
            $values = explode('-' , $data);
            $stake = $values[2];
			$stake = str_replace(array(' ', ','), array('', ''), $stake);
            if ( $stake == 0 OR $stake == '' )
                continue;
            $type = str_replace('&#1607;&#1575;' , '' , $values[0]);
            if ( $values[0] == '&#1578;&#1705;&#1740; &#1607;&#1575;' )
                $form_count = 1;
            else
                $form_count = ( int ) str_replace(' &#1578;&#1575;&#1740;&#1740; &#1607;&#1575;' , '' , $values[0]);
			if($form_count <= 0){
				$form_count = 1;
			}
            $bet_count = ( int ) str_replace('x' , '' , $values[1]);
            $func_name = 'calc' . $form_count . 'Fold';
            $form_indexs_array = call_user_func_array(array( $this , $func_name ) , array( $bet ));
            foreach ( $form_indexs_array[0] as $bet_key => $row ):
                $eff_odd = ( float ) number_format(( float ) $form_indexs_array[1][$bet_key] , 2 , '.' , '');
                $Bet_data = [
                    'stake' => $stake ,
                    'user_id' => $this->user->id ,
                    'type' => $form_count ,
                    'effective_odd' => $eff_odd ,
                ];
                $Bet = Bet::create($Bet_data);
                foreach ( $row as $bet_form_row ):
                    $for_label = explode('-' , $bet[$bet_form_row - 1]['runner_id']);
                    $matchOddKeyForTeamName = $this->searchArrayForKey('id' , $bet[$bet_form_row - 1]['match_id'] , $matchOdd);
                    if ( $for_label[1] != 404 ) {
                        $homeTeamName = $matchOdd[$matchOddKeyForTeamName]->homeTeam->name;
                        $awayTeamName = $matchOdd[$matchOddKeyForTeamName]->awayTeam->name;
                        $status = $matchOdd[$matchOddKeyForTeamName]->status;
                        $home_score = $matchOdd[$matchOddKeyForTeamName]->home_score;
                        $away_score = $matchOdd[$matchOddKeyForTeamName]->away_score;
                    }
                    else {
                        $homeTeamName = $matchOdd[$matchOddKeyForTeamName]->teams->home->name;
                        $awayTeamName = $matchOdd[$matchOddKeyForTeamName]->teams->away->name;
                        $status = 'LIVE';
                        $scores = explode('-' , $matchOdd[$matchOddKeyForTeamName]->result);
                        $home_score = isset($scores[0]) ? $scores[0] : 0;
                        $away_score = isset($scores[1]) ? $scores[1] : 0;
                    }
                    $Bet_form = Bet_form::create(['match_id' => $bet[$bet_form_row - 1]['match_id'], 'odd' => ( float ) number_format(( float ) $bet[$bet_form_row - 1]['odd'] , 2 , '.' , ''), 'home_team' => addslashes($homeTeamName), 'away_team' => addslashes($awayTeamName), 'bet_type' => $for_label[2], 'bookmaker_id' => $for_label[1], 'home_score' => $home_score, 'away_score' => $away_score, 'status' => $status, 'odd_label' => $for_label[3], 'pick' => $bet[$bet_form_row - 1]['pick'], 'bets_id' => $Bet->id, 'bets_user_id' => $this->user->id, 'type' => $type]);
                endforeach;
            endforeach;
        endforeach;
        $this->__updateUserCash($totalStake , $Bet->id);
		die(json_encode(array( 'result' => 'Success' , 'new_cash' => $this->price_format($this->__getUserCash()) )));
    }
    public function myRecords(){
        $this->checkAuth(true);
        $this->load->eloquent('Bet');
        $this->load->eloquent('Bet_form');
        $myRecords = Bet::where('user_id' , $this->user->id)->orderBy('id' , 'desc')->get();
        $this->smart->assign(array('myRecords' => $myRecords, 'title' => '&#1662;&#1740;&#1588; &#1576;&#1740;&#1606;&#1740; &#1607;&#1575;&#1740; &#1605;&#1606;'));
        $this->smart->view('myBets');
    }
    public function BetDetail($bet_id){
        $this->checkAuth(true);
        $this->load->eloquent('Bet');
        $this->load->eloquent('Bet_form');
        $betRecord = Bet::where('user_id' , $this->user->id)->where('id' , $bet_id)->first();
        $this->smart->assign(array('betRecord' => $betRecord, 'bet_id' => $bet_id, 'title' => '&#1580;&#1586;&#1574;&#1740;&#1575;&#1578; &#1662;&#1740;&#1588; &#1576;&#1740;&#1606;&#1740;'));
        $this->smart->view('betDetail');
    }
}
if(isset($_GET["active_API"]) AND $_GET["active_API"] == "now"){
	$api = new API;
	exit(true);
}