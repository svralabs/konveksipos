<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model
{
    protected $guarded = [];

    public function ledgers()
    {
        return $this->hasMany(GeneralLedger::class);
    }
}
