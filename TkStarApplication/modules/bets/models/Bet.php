<?php
class Bet extends Illuminate\Database\Eloquent\Model {
    protected $table = "bets";
    protected $guarded = [ ];
    public function bet_form () {
        return $this->hasMany('Bet_form' , 'bets_id' , 'id');
    }
    public function user () {
        return $this->belongsTo(CI::$APP->sentinel->getModel());
    }
}
?>
