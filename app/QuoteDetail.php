<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuoteDetail extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'quote_detail';

    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'dt_create';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'dt_lastupdate';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'quote_hd_id', 'item', 'qty', 'price', 'unit_price'
    ];

    /**
     *  Get quote Hd
     */
    public function quoteHd()
    {
        return $this->belongsTo('App\QuoteHd', 'quote_hd_id');
    }
}
