<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuoteHd extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'quote_hd';

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
        'customer_id',
        'seller_intr', 'seller_email', 'seller_comp', 'seller_name', 'seller_country', 'seller_addr1', 'seller_addr2', 'seller_addr3',
        'buyer_intr', 'buyer_email', 'buyer_comp', 'buyer_name', 'buyer_country', 'buyer_addr1', 'buyer_addr2', 'buyer_addr3',
        'rfq_initype', 'trading_fee', 'total_amount'
    ];


    /**
     * Get the details for the quote
     */
    public function quoteDetails()
    {
        return $this->hasMany('App\QuoteDetail');
    }

}
