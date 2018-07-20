<?php

namespace App;

use GuzzleHttp\Client;

use \App\County;
use \App\Subcounty;
use \App\Partner;
use \App\Ward;
use \App\Facility;

use \App\DataSet;
use \App\DataSetElement;

use \App\Lookup;

use DB;


class Synch 
{
	public static $base = 'https://hiskenya.org/api/';

	public static function subcounties(){

        $client = new Client(['base_uri' => self::$base]);
        $loop=true;
        $page=1;

        while($loop){

	        $response = $client->request('get', 'organisationUnits.json?paging=true&fields=id,name,code,parent[id,code,name]&filter=level:eq:3&page=' . $page, [
	            'auth' => [env('DHIS_USERNAME'), env('DHIS_PASSWORD')],
	            // 'http_errors' => false,
	        ]);

	        $body = json_decode($response->getBody());

	        foreach ($body->organisationUnits as $key => $value) {
	        	$sub = Subcounty::where('SubCountyDHISCode', $value->id)->get()->first();

	        	if(!$sub) $sub = new Subcounty;

        		$county = County::where('CountyDHISCode', $value->parent->id)->get()->first();
        		if($county && !$county->rawcode){
        			$county->rawcode = $value->parent->code;
        			$county->save();
        		}
        		$sub->county = $county->id ?? 0;
        		$sub->name = $value->name;
        		$sub->SubCountyDHISCode = $value->id;
        		$sub->save();
	        }

	        echo  'Page ' . $page . " completed \n";
	        if($page == $body->pager->pageCount) break;
	        $page++;
        }

	}

	public static function wards(){

        $client = new Client(['base_uri' => self::$base]);
        $loop=true;
        $page=1;

        while($loop){

	        $response = $client->request('get', 'organisationUnits.json?paging=true&fields=id,name,code,parent[id,code,name]&filter=level:eq:4&page=' . $page, [
	            'auth' => [env('DHIS_USERNAME'), env('DHIS_PASSWORD')],
	            // 'http_errors' => false,
	        ]);

	        $body = json_decode($response->getBody());

	        foreach ($body->organisationUnits as $key => $value) {
	        	$ward = Ward::where('WardDHISCode', $value->id)->get()->first();

	        	if(!$ward) $ward = new Ward;

        		$ward->name = $value->name;
        		$ward->WardDHISCode = $value->id;
        		$ward->rawcode = $value->code ?? null;

				$sub = Subcounty::where('SubCountyDHISCode', $value->parent->id)->get()->first();
				$ward->subcounty_id = $sub->id ?? 0;   
				$ward->save();     		
	        }

	        echo  'Page ' . $page . " completed \n";
	        if($page == $body->pager->pageCount) break;
	        $page++;
        }
	}

	public static function facilities()
	{
        $client = new Client(['base_uri' => self::$base]);
        $loop=true;
        $page=125;

        while($loop){

	        $response = $client->request('get', 'organisationUnits.json?paging=true&fields=id,name,code,parent[id,code,name]&filter=level:eq:5&page=' . $page, [
	            'auth' => [env('DHIS_USERNAME'), env('DHIS_PASSWORD')],
	            // 'http_errors' => false,
	        ]);

	        $body = json_decode($response->getBody());

	        foreach ($body->organisationUnits as $key => $value) {

	        	$mfl = $value->code ?? null;

	        	$fac = Facility::where('DHIScode', $value->id)
			        	->when($mfl, function($query) use ($value){
			        		return $query->orWhere('facilitycode', $value->code);
			        	})
			        	->get()->first();

	        	if(!$fac) $fac = new Facility;

        		$fac->new_name = $value->name;
        		$fac->DHIScode = $value->id;
        		$fac->facilitycode = $fac->facilitycode ?? $value->code ?? 0;
        		$fac->facilitycode = (int) $fac->facilitycode;

        		$ward = Ward::where('WardDHISCode', $value->parent->id)->get()->first();
				$fac->ward_id = $ward->id ?? 0;        		
				$fac->subcounty_id = $ward->subcounty_id ?? $fac->district ?? 0;  

				$fac->save();
	        }

	        echo  'Page ' . $page . " completed \n";
	        if($page == $body->pager->pageCount) break;
	        $page++;
        }
	}

	public static function datasets()
	{
		$url = "dataSets.json?paging=false&filter=name:ilike:731&fields=id,name,code";
        $client = new Client(['base_uri' => self::$base]);

        $response = $client->request('get', $url, [
            'auth' => [env('DHIS_USERNAME'), env('DHIS_PASSWORD')],
            // 'http_errors' => false,
        ]);

        $body = json_decode($response->getBody());

        foreach ($body->dataSets as $key => $value) {
        	$d = new DataSet;
        	$d->name = $value->name ?? '';
        	$d->dhis = $value->id ?? '';
        	$d->code = $value->code ?? '';
        	$d->save();

        	$table_name = Lookup::table_name_formatter($d->name);

        	$sql = "CREATE TABLE `{$table_name}` (
        				id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        				facility int(10) UNSIGNED DEFAULT 0,
        				year smallint(4) UNSIGNED DEFAULT 0,
        				month tinyint(3) UNSIGNED DEFAULT 0,
        	";

        	$new_url = "dataSets/" . $d->dhis . ".json?fields=name,code,id,dataSetElements[dataElement[name,id,code],categoryCombo[id,name";

	        $elements_request = $client->request('get', $new_url, [
	            'auth' => [env('DHIS_USERNAME'), env('DHIS_PASSWORD')],
	            // 'http_errors' => false,
	        ]);

	        $elements = json_decode($elements_request->getBody());

	        foreach ($elements->dataSetElements as $element) {
	        	$e = new DataSetElement;
	        	$e->data_set_id = $d->id;
	        	$e->name = $element->dataElement->name ?? '';
	        	$e->code = $element->dataElement->code ?? '';
	        	$e->dhis = $element->dataElement->id ?? '';

	        	$column_name = Lookup::column_name_formatter($e->name);

	        	$e->table_name = $table_name;
	        	$e->column_name = $column_name;
	        	$e->save();

	        	$sql .= "
	        	`{$column_name}` int(10) DEFAULT NULL, ";

	        	$d->category_dhis = $element->categoryCombo->id ?? '';
	        }

	        DB::statement("DROP TABLE IF EXISTS `{$table_name}`;");

	        $sql .= "
	        		dateupdated date DEFAULT NULL,
					PRIMARY KEY (`id`),
					KEY `identifier`(`facility`, `year`, `month`),
					KEY `facility` (`facility`),
					KEY `specific_time` (`year`, `month`)
				);
	        ";

	        DB::statement($sql);
	        $d->save();
	        echo  'Data set ' . ($key+1) . " completed \n";
        }
	}

	public static function insert_rows($year=null)
	{
		if(!$year) $year = date('Y');

		$tables = DataSetElement::selectRaw("distinct table_name")->get();

		$facilities = Facility::select('id')->get();

		foreach ($tables as $table) {

			$i=0;
			$data_array = [];

			for ($month=1; $month < 13; $month++) { 
				foreach ($facilities as $k => $val) {
					$data_array[$i] = array('year' => $year, 'month' => $month, 'facility' => $val->id);
					$i++;

					if ($i == 200) {
						DB::table($table->table_name)->insert($data_array);
						$data_array=null;
				    	$i=0;
					}
				}
			}
			if($data_array) DB::table($table->table_name)->insert($data_array);

	        echo  'Completed entry for ' . $table->table_name . " \n";
		}
	}

	public static function truncate_tables()
	{
		$tables = DataSetElement::selectRaw("distinct table_name")->get();

		foreach ($tables as $table){
			DB::statement("TRUNCATE TABLE " . $table->table_name . ";");
		}

	}

	public static function populate($year=null)
	{
		if(!$year) $year = date('Y');
        $client = new Client(['base_uri' => self::$base]);
		$datasets = DataSet::with(['element'])->get();

		echo 'Begin updates at ' . date('Y-m-d H:i:s a') . " \n";

		$pe='';
		$offset=0;

		for($month=1; $month < 13; $month++) {
			if($month < 10) $month = '0' . $month;
			$pe .= $year . $month . ';';
		}

		// Begin loop to get facilities
		while(true){

			$facilities = Facility::eligible($offset)->get();
			if($facilities->isEmpty()) break;
			$ou = '';

			// Put facilities' DHIS codes into a string
			foreach ($facilities as $facility) {
				$ou .= $facility->DHIScode . ';';
			}

			// Iterate through the data sets
			foreach ($datasets as $dataset) {
				$dx = '';
				foreach ($dataset->element as $element) {
					$dx .= $element->dhis . ';';
				}
				dd($dx);
				$co = $dataset->category_dhis;

				// $url = "analytics?dimension=dx:" . $dx . "&dimension=ou:" . $ou . "&dimension=co:" . $co . "&dimension=pe:" . $pe;
				// If co is set, it will be value[1]


				$url = "analytics?dimension=dx:" . $dx . "&dimension=ou:" . $ou . "&dimension=pe:" . $pe;

		        $response = $client->request('get', $url, [
		            'auth' => [env('DHIS_USERNAME'), env('DHIS_PASSWORD')],
		            // 'http_errors' => false,
		        ]);

		        $body = json_decode($response->getBody());


		        foreach ($body->rows as $key => $value) {
		        	$elem = $dataset->element->where('dhis', $value[0])->first();
		        	$fac = $facilities->where('DHIScode', $value[1])->first();
		        	$period = str_split($value[3], 2);
		        	$y = $period[0];
		        	$m = $period[1];

		        	if(!$elem->table_name || !$elem->column_name) continue;

		        	DB::table($elem->table_name)
		        		->where(['facility' => $fac->id, 'year' => $y, 'month' => $m, 'dateupdated' => date('Y-m-d')])
		        		->update([$elem->column_name => $value[3]]);
		        }
			}			
			$offset += 50;
	        echo  'Completed updated for ' . $offset . " facilities at " . date('Y-m-d H:i:s a') . " \n";
		}
	} 

	public static function stuff()
	{
		// https://hiskenya.org/api/analytics?dimension=dx:F9yzD1uwtqU;&dimension=ou:z2V9BrTObHC;mu9d9jNXA6Y;&dimension=pe:2018;&
		// dx is the dataset data datasetelement dataelement id
		// co is the dataset data datasetelement categorycombo id
		// ou is the facility dhis code
		// pe is the period
	}




}
