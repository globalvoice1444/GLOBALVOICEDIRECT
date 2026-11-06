<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdditionalCreditAppInfo extends Model
{
	protected $table='additional_creditapp_info';
    protected $fillable=['duns_number', 'sic_code', 'naics_code', 'first_sale','last_sale','credit_limit','business_address','business_name'];
}
