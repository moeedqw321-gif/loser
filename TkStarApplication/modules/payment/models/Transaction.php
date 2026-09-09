<?php
use Illuminate\Database\Eloquent\Model as EloquentModel;
class Transaction extends EloquentModel {
    protected $table = "transactions";
    protected $guarded = [""];
    public function user() {
        return $this->belongsTo(CI::$APP->sentinel->getModel());
    }
    public function state(){
        return $this->belongsTo('Transaction_state', 'transaction_states_id','id');
    }
}
?>