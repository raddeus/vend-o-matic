<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'name',
        'coins',
    ];

    /**
     * Gets the items for this machine
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(MachineItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getInventoryAttribute()
    {
        return $this->items->pluck('count');
    }

}
