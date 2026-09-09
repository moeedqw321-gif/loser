<?php
use Illuminate\Database\Eloquent\Model as EloquentModel;
class Transaction_state extends EloquentModel {
    protected $table = "transaction_states";
    protected $guarded = array('');
    public function user() {
        return $this->belongsTo(CI::$APP->sentinel->getModel());
    }
    public function transaction() {
        return $this->hasMany('Transaction', 'transaction_states_id', 'id');
    }
}
?>