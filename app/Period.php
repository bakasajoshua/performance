<?php

namespace App;

use App\BaseModel;

class Period extends BaseModel
{

	public function getMonthNameAttribute()
	{
		return Lookup::resolve_month($this->month);
	}


    public function scopeAchievement($query)
    {
    	$date_query = Lookup::date_query();

        return $query->whereRaw($date_query)
        	->whereRaw("year < ". date('Y') ." OR (year = ". date('Y') ." AND month < ". date('m') .")  ");
    }

    public function scopeLastMonth($query)
    {
        $y = date('Y');
        $m = date('m');

        if($m == 1){
            return $query->where('year', ($y-1))->where('month', 12);
        }else{
            return $query->where('year', $y)->where('month', ($m-1));            
        }
    }
}
