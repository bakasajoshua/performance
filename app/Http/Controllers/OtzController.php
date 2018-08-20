<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Excel;
use App\Lookup;
use App\Facility;
use App\ViewFacility;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// pns partner notification services

class OtzController extends Controller
{

	public function facilities_count()
	{
		// $date_query = Lookup::date_query(true);
		$divisions_query = Lookup::divisions_query();

		$select_query = "financial_year, COUNT(DISTINCT t_non_mer.facility) AS total ";

		$viremia = DB::table('t_non_mer')
			->join('view_facilitys', 'view_facilitys.id', '=', 't_non_mer.facility')
			->selectRaw($select_query)
			// ->whereRaw($date_query)
			->whereRaw($divisions_query)
			->where('viremia_beneficiaries', '>', 0)
			->where('financial_year', '>', 2016)
			->groupBy('financial_year')
			->orderBy('financial_year', 'asc')
			->get();

		$dsd = DB::table('t_non_mer')
			->join('view_facilitys', 'view_facilitys.id', '=', 't_non_mer.facility')
			->selectRaw($select_query)
			->whereRaw($divisions_query)
			->where('dsd_beneficiaries', '>', 0)
			->where('financial_year', '>', 2016)
			->groupBy('financial_year')
			->orderBy('financial_year', 'asc')
			->get();

		$otz = DB::table('t_non_mer')
			->join('view_facilitys', 'view_facilitys.id', '=', 't_non_mer.facility')
			->selectRaw($select_query)
			->whereRaw($divisions_query)
			->where('otz_beneficiaries', '>', 0)
			->where('financial_year', '>', 2016)
			->groupBy('financial_year')
			->orderBy('financial_year', 'asc')
			->get();

		$men = DB::table('t_non_mer')
			->join('view_facilitys', 'view_facilitys.id', '=', 't_non_mer.facility')
			->selectRaw($select_query)
			->whereRaw($divisions_query)
			->where('men_clinic_beneficiaries', '>', 0)
			->where('financial_year', '>', 2016)
			->groupBy('financial_year')
			->orderBy('financial_year', 'asc')
			->get();

		$data['div'] = str_random(15);
		$data['stacking_false'] = false;

		$data['outcomes'][0]['name'] = "Viremia Facilities";
		$data['outcomes'][1]['name'] = "DSD Facilities";
		$data['outcomes'][2]['name'] = "OTZ Facilities";
		$data['outcomes'][3]['name'] = "Men Clinics";

		$data['outcomes'][0]['type'] = "column";
		$data['outcomes'][1]['type'] = "column";
		$data['outcomes'][2]['type'] = "column";
		$data['outcomes'][3]['type'] = "column";

		$data['categories'][0] = "FY 2017";
		$data['categories'][1] = "FY 2018";
		$data['categories'][2] = "FY 2019";

		$data["outcomes"][0]["data"] = array_fill(0, 3, 0);
		$data["outcomes"][1]["data"] = array_fill(0, 3, 0);
		$data["outcomes"][2]["data"] = array_fill(0, 3, 0);
		$data["outcomes"][3]["data"] = array_fill(0, 3, 0);

		foreach ($viremia as $key => $row) {
			$data['categories'][$key] = "FY " . $row->financial_year;
			$data["outcomes"][0]["data"][$key] = (int) $row->total;
			$data["outcomes"][1]["data"][$key] = (int) $dsd[$key]->total;
			$data["outcomes"][2]["data"][$key] = (int) $otz[$key]->total;
			$data["outcomes"][3]["data"][$key] = (int) $men[$key]->total;
		}
		return view('charts.bar_graph', $data);		
	}

	public function clinics()
	{
		// $date_query = Lookup::date_query(true);
		$divisions_query = Lookup::divisions_query();

		$select_query = "COUNT(id) AS total ";

		$viremia = DB::table('view_facilitys')
			->selectRaw($select_query)
			->whereRaw($divisions_query)
			->where('is_viremia', 1)
			->get();

		$dsd = DB::table('view_facilitys')
			->selectRaw($select_query)
			->whereRaw($divisions_query)
			->where('is_dsd', 1)
			->get();

		$otz = DB::table('view_facilitys')
			->selectRaw($select_query)
			->whereRaw($divisions_query)
			->where('is_otz', 1)
			->get();

		$men = DB::table('view_facilitys')
			->selectRaw($select_query)
			->whereRaw($divisions_query)
			->where('is_men_clinic', 1)
			->get();

		$data['div'] = str_random(15);
		$data['stacking_false'] = false;

		$data['outcomes'][0]['name'] = "Viremia Facilities";
		$data['outcomes'][1]['name'] = "DSD Facilities";
		$data['outcomes'][2]['name'] = "OTZ Facilities";
		$data['outcomes'][3]['name'] = "Men Clinics";

		$data['outcomes'][0]['type'] = "column";
		$data['outcomes'][1]['type'] = "column";
		$data['outcomes'][2]['type'] = "column";
		$data['outcomes'][3]['type'] = "column";

		$data['categories'][0] = "FY 2017";
		$data['categories'][1] = "FY 2018";
		$data['categories'][2] = "FY 2019";

		$data["outcomes"][0]["data"] = array_fill(0, 3, 0);
		$data["outcomes"][1]["data"] = array_fill(0, 3, 0);
		$data["outcomes"][2]["data"] = array_fill(0, 3, 0);
		$data["outcomes"][3]["data"] = array_fill(0, 3, 0);

		foreach ($viremia as $key => $row) {
			$data['categories'][$key] = "FY " . $row->financial_year;
			$data["outcomes"][0]["data"][$key] = (int) $row->total;
			$data["outcomes"][1]["data"][$key] = (int) $dsd[$key]->total;
			$data["outcomes"][2]["data"][$key] = (int) $otz[$key]->total;
			$data["outcomes"][3]["data"][$key] = (int) $men[$key]->total;
		}
		return view('charts.bar_graph', $data);		
	}

	public function beneficiaries()
	{
		// $date_query = Lookup::date_query(true);
		$divisions_query = Lookup::divisions_query();

		$viremia = DB::table('t_non_mer')
			->join('view_facilitys', 'view_facilitys.id', '=', 't_non_mer.facility')
			->selectRaw("financial_year, SUM(viremia_beneficiaries) AS beneficiaries, SUM(viremia_target) AS target ")
			->whereRaw($divisions_query)
			->where('financial_year', '>', 2016)
			->groupBy('financial_year')
			->orderBy('financial_year', 'asc')
			->get();

		$dsd = DB::table('t_non_mer')
			->join('view_facilitys', 'view_facilitys.id', '=', 't_non_mer.facility')
			->selectRaw("financial_year, SUM(dsd_beneficiaries) AS beneficiaries, SUM(dsd_target) AS target ")
			->whereRaw($divisions_query)
			->where('financial_year', '>', 2016)
			->groupBy('financial_year')
			->orderBy('financial_year', 'asc')
			->get();

		$otz = DB::table('t_non_mer')
			->join('view_facilitys', 'view_facilitys.id', '=', 't_non_mer.facility')
			->selectRaw("financial_year, SUM(otz_beneficiaries) AS beneficiaries, SUM(otz_target) AS target ")
			->whereRaw($divisions_query)
			->where('financial_year', '>', 2016)
			->groupBy('financial_year')
			->orderBy('financial_year', 'asc')
			->get();

		$men = DB::table('t_non_mer')
			->join('view_facilitys', 'view_facilitys.id', '=', 't_non_mer.facility')
			->selectRaw("financial_year, SUM(men_clinic_beneficiaries) AS beneficiaries, SUM(men_clinic_target) AS target ")
			->whereRaw($divisions_query)
			->where('financial_year', '>', 2016)
			->groupBy('financial_year')
			->orderBy('financial_year', 'asc')
			->get();

		$data['div'] = str_random(15);
		// $data['stacking_false'] = false;

		$data['outcomes'][0]['name'] = "Viremia Beneficiaries";
		$data['outcomes'][1]['name'] = "DSD Beneficiaries";
		$data['outcomes'][2]['name'] = "OTZ Beneficiaries";
		$data['outcomes'][3]['name'] = "Men Clinics Beneficiaries";

		// $data['outcomes'][4]['name'] = "Viremia Shortfall";
		// $data['outcomes'][5]['name'] = "DSD Shortfall";
		// $data['outcomes'][6]['name'] = "OTZ Shortfall";
		// $data['outcomes'][7]['name'] = "Men Clinics Shortfall";

		$data['outcomes'][0]['stack'] = "Viremia";
		$data['outcomes'][1]['stack'] = "DSD";
		$data['outcomes'][2]['stack'] = "OTZ";
		$data['outcomes'][3]['stack'] = "Men";

		// $data['outcomes'][4]['stack'] = "Viremia";
		// $data['outcomes'][5]['stack'] = "DSD";
		// $data['outcomes'][6]['stack'] = "OTZ";
		// $data['outcomes'][7]['stack'] = "Men";


		foreach ($viremia as $key => $row) {
			$data['categories'][$key] = "FY " . $row->financial_year;
			$data["outcomes"][0]["data"][$key] = (int) $row->beneficiaries;
			$data["outcomes"][1]["data"][$key] = (int) $dsd[$key]->beneficiaries;
			$data["outcomes"][2]["data"][$key] = (int) $otz[$key]->beneficiaries;
			$data["outcomes"][3]["data"][$key] = (int) $men[$key]->beneficiaries;


			// $data["outcomes"][4]["data"][$key] = ($row->target > $row->beneficiaries ? ($row->target-$row->beneficiaries) : 0);
			// $data["outcomes"][5]["data"][$key] = ($dsd[$key]->target > $dsd[$key]->beneficiaries ? ($dsd[$key]->target-$dsd[$key]->beneficiaries) : 0);
			// $data["outcomes"][6]["data"][$key] = ($otz[$key]->target > $otz[$key]->beneficiaries ? ($otz[$key]->target-$otz[$key]->beneficiaries) : 0);
			// $data["outcomes"][7]["data"][$key] = ($men[$key]->target > $men[$key]->beneficiaries ? ($men[$key]->target-$men[$key]->beneficiaries) : 0);
		}
		return view('charts.bar_graph', $data);		
	}

	public function achievement()
	{
		$divisions_query = Lookup::divisions_query();

		$rows = DB::table('t_non_mer')
			->join('view_facilitys', 'view_facilitys.id', '=', 't_non_mer.facility')
			->selectRaw("financial_year,
			 SUM(viremia_beneficiaries) AS viremia_beneficiaries, SUM(viremia_target) AS viremia_target,
			 SUM(dsd_beneficiaries) AS dsd_beneficiaries, SUM(dsd_target) AS dsd_target, 
			 SUM(otz_beneficiaries) AS otz_beneficiaries, SUM(otz_target) AS otz_target, 
			 SUM(men_clinic_beneficiaries) AS men_clinic_beneficiaries, SUM(men_clinic_target) AS men_clinic_target ")
			->whereRaw($divisions_query)
			->where('financial_year', '>', 2016)
			->groupBy('financial_year')
			->orderBy('financial_year', 'asc')
			->get();	

		$data['div'] = str_random(15);
		// $data['stacking_false'] = false;

		$data['outcomes'][0]['name'] = "Viremia Beneficiaries";
		$data['outcomes'][1]['name'] = "DSD Beneficiaries";
		$data['outcomes'][2]['name'] = "OTZ Beneficiaries";
		$data['outcomes'][3]['name'] = "Men Clinics Beneficiaries";

		$data['outcomes'][4]['name'] = "Viremia Shortfall";
		$data['outcomes'][5]['name'] = "DSD Shortfall";
		$data['outcomes'][6]['name'] = "OTZ Shortfall";
		$data['outcomes'][7]['name'] = "Men Clinics Shortfall";

		$data['outcomes'][0]['stack'] = "Viremia";
		$data['outcomes'][1]['stack'] = "DSD";
		$data['outcomes'][2]['stack'] = "OTZ";
		$data['outcomes'][3]['stack'] = "Men";

		$data['outcomes'][4]['stack'] = "Viremia";
		$data['outcomes'][5]['stack'] = "DSD";
		$data['outcomes'][6]['stack'] = "OTZ";
		$data['outcomes'][7]['stack'] = "Men";

		for ($i=0; $i < 8; $i++) { 
			$data['outcomes'][$i]['type'] = "column";
		}

		foreach ($rows as $key => $row) {
			$data['categories'][$key] = "FY " . $row->financial_year;
			$data["outcomes"][0]["data"][$key] = (int) $row->viremia_beneficiaries;
			$data["outcomes"][1]["data"][$key] = (int) $row->dsd_beneficiaries;
			$data["outcomes"][2]["data"][$key] = (int) $row->otz_beneficiaries;
			$data["outcomes"][3]["data"][$key] = (int) $row->men_clinic_beneficiaries;


			$data["outcomes"][4]["data"][$key] = ($row->viremia_target > $row->viremia_beneficiaries ? ($row->viremia_target-$row->viremia_beneficiaries) : 0);
			$data["outcomes"][5]["data"][$key] = ($row->dsd_target > $row->dsd_beneficiaries ? ($row->dsd_target-$row->dsd_beneficiaries) : 0);
			$data["outcomes"][6]["data"][$key] = ($row->otz_target > $row->otz_beneficiaries ? ($row->otz_target-$row->otz_beneficiaries) : 0);
			$data["outcomes"][7]["data"][$key] = ($row->men_clinic_target > $row->men_clinic_beneficiaries ? ($row->men_clinic_target-$row->men_clinic_beneficiaries) : 0);
		}
		return view('charts.bar_graph', $data);		
	}

	public function breakdown()
	{
		$divisions_query = Lookup::divisions_query();
		$date_query = Lookup::date_query(true);
		$q = Lookup::groupby_query();

		$data['rows'] = DB::table('t_non_mer')
			->join('view_facilitys', 'view_facilitys.id', '=', 't_non_mer.facility')
			->selectRaw($q['select_query'] . ",
			 SUM(viremia_beneficiaries) AS viremia_beneficiaries, SUM(viremia_target) AS viremia_target,
			 SUM(dsd_beneficiaries) AS dsd_beneficiaries, SUM(dsd_target) AS dsd_target, 
			 SUM(otz_beneficiaries) AS otz_beneficiaries, SUM(otz_target) AS otz_target, 
			 SUM(men_clinic_beneficiaries) AS men_clinic_beneficiaries, SUM(men_clinic_target) AS men_clinic_target ")
			->whereRaw($date_query)
			->whereRaw($divisions_query)
			->groupBy($q['group_query'])
			->get();

		$data['div'] = str_random(15);

		return view('combined.otz', $data);

	}









	public function get_data(Request $request)
	{
		$target = DB::table('t_non_mer')
			->join('view_facilitys', 'view_facilitys.id', '=', 't_non_mer.facility')
			->selectRaw("t_non_mer.*, name")
			->where('view_facilitys.id', $request->input('facility_id'))
			->where('financial_year', $request->input('financial_year'))
			->first();

		return json_encode($target);

		// return view('partials.targets', ['targets' => $targets]);
	}

	public function set_target(Request $request)
	{
		$financial_year = $request->input('financial_year');

		// $facilities = $request->input('facilities');
		$facility_id = $request->input('facility_id');
		$viremia_beneficiaries = $request->input('viremia_beneficiaries');
		$viremia_target = $request->input('viremia_target');
		$dsd_beneficiaries = $request->input('dsd_beneficiaries');
		$dsd_target = $request->input('dsd_target');
		$otz_beneficiaries = $request->input('otz_beneficiaries');
		$otz_target = $request->input('otz_target');
		$men_clinic_beneficiaries = $request->input('men_clinic_beneficiaries');
		$men_clinic_target = $request->input('men_clinic_target');

		$today = date('Y-m-d');
		DB::connection('mysql_wr')->table('t_non_mer')
			->where(['financial_year' => $financial_year, 'facility' => $facility_id])
			->update([
				'viremia_beneficiaries' => Lookup::clean_zero($viremia_beneficiaries),
				'viremia_target' => Lookup::clean_zero($viremia_target),
				'dsd_beneficiaries' => Lookup::clean_zero($dsd_beneficiaries),
				'dsd_target' => Lookup::clean_zero($dsd_target),
				'otz_beneficiaries' => Lookup::clean_zero($otz_beneficiaries),
				'otz_target' => Lookup::clean_zero($otz_target),
				'men_clinic_beneficiaries' => Lookup::clean_zero($men_clinic_beneficiaries),
				'men_clinic_target' => Lookup::clean_zero($men_clinic_target),
				
				// 'viremia_beneficiaries' => $viremia_beneficiaries,
				// 'viremia_target' => $viremia_target,
				// 'dsd_beneficiaries' => $dsd_beneficiaries,
				// 'dsd_target' => $dsd_target,
				// 'otz_beneficiaries' => $otz_beneficiaries,
				// 'otz_target' => $otz_target,
				// 'men_clinic_beneficiaries' => $men_clinic_beneficiaries,
				// 'men_clinic_target' => $men_clinic_target,
			]);



		session(['toast_message' => 'The target has been updated.']);
		return back();

		// foreach ($facilities as $key => $facility) {
		// 	DB::where(['financial_year' => $financial_year, 'facility_id' => $facility])->update([
		// 		'viremia_beneficiaries' => Lookup::clean_zero($viremia_beneficiaries[$key]),
		// 		'viremia_target' => Lookup::clean_zero($viremia_target[$key]),
		// 		'dsd_beneficiaries' => Lookup::clean_zero($dsd_beneficiaries[$key]),
		// 		'dsd_target' => Lookup::clean_zero($dsd_target[$key]),
		// 		'otz_beneficiaries' => Lookup::clean_zero($otz_beneficiaries[$key]),
		// 		'otz_target' => Lookup::clean_zero($otz_target[$key]),
		// 		'men_clinic_beneficiaries' => Lookup::clean_zero($men_clinic_beneficiaries[$key]),
		// 		'men_clinic_target' => Lookup::clean_zero($men_clinic_target[$key]),
		// 	]);
		// }
	}

	public function download_excel($financial_year)
	{
		$partner = session('session_partner');
		$data = [];

		$rows = DB::table('t_non_mer')
			->join('view_facilitys', 'view_facilitys.id', '=', 't_non_mer.facility')
			->selectRaw("financial_year AS `Financial Year`, name AS `Facility`, partnername AS `Partner Name`, facilitycode AS `MFL Code`, DHIScode AS `DHIS Code`, 
				is_viremia AS `Is Viremia (YES/NO)`, is_dsd AS `Is DSD (YES/NO)`, is_otz AS `Is OTZ (YES/NO)`, is_men_clinic AS `Is Men Clinic (YES/NO)`,
				viremia_beneficiaries AS `Viremia Beneficiaries`, dsd_beneficiaries AS `DSD Beneficiaries`, otz_beneficiaries AS `OTZ Beneficiaries`, men_clinic_beneficiaries AS `Men Clinic Beneficiaries` ")
			->when($financial_year, function($query) use ($financial_year){
				return $query->where('financial_year', $financial_year);
			})
			->where('partner', $partner->id)			
			->orderBy('name', 'asc')
			->get();

		foreach ($rows as $key => $row) {
			$row_array = get_object_vars($row);
			$data[] = $row_array;
			$data[$key]['Is Viremia (YES/NO)'] = Lookup::get_boolean($row_array['Is Viremia (YES/NO)']);
			$data[$key]['Is DSD (YES/NO)'] = Lookup::get_boolean($row_array['Is DSD (YES/NO)']);
			$data[$key]['Is OTZ (YES/NO)'] = Lookup::get_boolean($row_array['Is OTZ (YES/NO)']);
			$data[$key]['Is Men Clinic (YES/NO)'] = Lookup::get_boolean($row_array['Is Men Clinic (YES/NO)']);
		}

		$filename = str_replace(' ', '_', strtolower($partner->name)) . '_' . $financial_year;

    	Excel::create($filename, function($excel) use($data, $key){
    		$excel->sheet('sheet1', function($sheet) use($data, $key){
    			$sheet->fromArray($data);

	    		$letter_array = ['F', 'G', 'H', 'I'];

	    		for ($i=0; $i < $key; $i++) { 
	    			foreach ($letter_array as $letter) {
	    				$cell_no = $i+1;
	    				// $sheet->
	    				$objValidation = $sheet->getCell($letter . $cell_no)->getDataValidation();
	    				$objValidation->setType('list');
	    				$objValidation->setErrorStyle('information');
	    				$objValidation->setAllowBlank(true);
	    				$objValidation->setPromptTitle('Pick from list');
	    				$objValidation->setPrompt('Please pick a value from the drop-down list.');
	    				$objValidation->setFormula1('"YES,NO"');
	    			}
	    		}
    		});

    	})->store('xlsx');

    	$path = storage_path('exports/' . $filename . '.xlsx');
    	return response()->download($path);
	}

	/*public function download_excel($financial_year)
	{
		$partner = session('session_partner');
		$data = [];

		$rows = DB::table('t_non_mer')
			->join('view_facilitys', 'view_facilitys.id', '=', 't_non_mer.facility')
			->selectRaw("financial_year AS `Financial Year`, name AS `Facility`, partnername AS `Partner Name`, facilitycode AS `MFL Code`, DHIScode AS `DHIS Code`, 
				is_viremia AS `Is Viremia`, is_dsd AS `Is DSD`, is_otz AS `Is OTZ`, is_men_clinic AS `Is Men Clinic`,
				viremia_beneficiaries AS `Viremia Beneficiaries`, dsd_beneficiaries AS `DSD Beneficiaries`, otz_beneficiaries AS `OTZ Beneficiaries`, men_clinic_beneficiaries AS `Men Clinic Beneficiaries` ")
			->when($financial_year, function($query) use ($financial_year){
				return $query->where('financial_year', $financial_year);
			})
			->where('partner', $partner->id)			
			->orderBy('name', 'asc')
			->get();

		foreach ($rows as $key => $row) {
			$row_array = get_object_vars($row);
			$data[] = $row_array;
			$data[$key]['Is Viremia'] = Lookup::get_boolean($row_array['Is Viremia']);
			$data[$key]['Is DSD'] = Lookup::get_boolean($row_array['Is DSD']);
			$data[$key]['Is OTZ'] = Lookup::get_boolean($row_array['Is OTZ']);
			$data[$key]['Is Men Clinic'] = Lookup::get_boolean($row_array['Is Men Clinic']);
		}

		$filename = str_replace(' ', '_', strtolower($partner->name)) . '_' . $financial_year;

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

    	Excel::create($filename, function($excel) use($data, $key){
    		$excel->sheet('sheet1', function($sheet) use($data, $key){
    			$sheet->fromArray($data);

	    		$letter_array = ['F', 'G', 'H', 'I'];

	    		for ($i=0; $i < $key; $i++) { 
	    			foreach ($letter_array as $letter) {
	    				$cell_no = $i+1;
	    				$sheet->
	    				// $objValidation = $sheet->getCell($letter . $cell_no)->getDataValidation();
	    				// $objValidation->setType('list');
	    				// $objValidation->setErrorStyle('information');
	    				// $objValidation->setAllowBlank(true);
	    				// $objValidation->setPromptTitle('Pick from list');
	    				// $objValidation->setPrompt('Please pick a value from the drop-down list.');
	    				// $objValidation->setFormula1('"YES,NO"');
	    			}
	    		}
    		});

    	})->store('xlsx');

    	$path = storage_path('exports/' . $filename . '.xlsx');

		$writer = new Xlsx($spreadsheet);
		$writer->save($path);
    	return response()->download($path);
	}*/



	public function upload_excel(Request $request)
	{
		$file = $request->upload->path();
		// $path = $request->upload->store('public/results/vl');
		$financial_year = $request->input('financial_year');

		$data = Excel::load($file, function($reader){
			$reader->toArray();
		})->get();

		$partner = session('session_partner');

		// print_r($data);die();

		foreach ($data as $key => $value) {
			$fac = Facility::where('facilitycode', $value->mfl_code)->first();

			$view_facility = ViewFacility::find($fac->id);
			if($view_facility->partner != $partner->id) continue;

			$fac->fill([
				'is_viremia' => Lookup::clean_boolean($value->is_viremia_yesno), 
				'is_dsd' => Lookup::clean_boolean($value->is_dsd_yesno), 
				'is_otz' => Lookup::clean_boolean($value->is_otz_yesno), 
				'is_men_clinic' => Lookup::clean_boolean($value->is_men_clinic_yesno),
			]);
			$fac->save();

			DB::connection('mysql_wr')->table('t_non_mer')
				->where(['facility' => $fac->id, 'financial_year' => $financial_year])
				->update([
					'viremia_beneficiaries' => $value->viremia_beneficiaries,
					'dsd_beneficiaries' => $value->dsd_beneficiaries,
					'otz_beneficiaries' => $value->otz_beneficiaries,
					'men_clinic_beneficiaries' => $value->men_clinic_beneficiaries,
				]);
		}

		session(['toast_message' => 'The updates have been made.']);
		return back();
	}



}
