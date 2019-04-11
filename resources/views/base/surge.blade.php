@extends('layouts.master')

@section('content')

<style type="text/css">
	.display_date {
		width: 130px;
		display: inline;
	}
	.display_date {
		width: 130px;
		display: inline;
	}
</style>


<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		    <div class="panel-heading">
			    Testing <div class="display_date"></div>
		    </div>
			<div class="panel-body" id="testing">
				<center><div class="loader"></div></center>
			</div>
		</div>
	</div>
</div>


<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		    <div class="panel-heading">
			    New On Treatment & Linkage <div class="display_date"></div>
		    </div>
			<div class="panel-body" id="linkage">
				<center><div class="loader"></div></center>
			</div>
		</div>
	</div>
</div>


<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		    <div class="panel-heading">
			    Yield By Modality <div class="display_date"></div>
		    </div>
			<div class="panel-body" id="modality_yield">
				<center><div class="loader"></div></center>
			</div>
		</div>
	</div>
</div>


<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		    <div class="panel-heading">
			    Yield By Age <div class="display_date"></div>
		    </div>
			<div class="panel-body" id="age_yield">
				<center><div class="loader"></div></center>
			</div>
		</div>
	</div>
</div>

@endsection


@section('scripts')

<script type="text/javascript">

	function reload_page()
	{
		$("#testing").html("<center><div class='loader'></div></center>");
		$("#linkage").html("<center><div class='loader'></div></center>");
		$("#modality_yield").html("<center><div class='loader'></div></center>");
		$("#age_yield").html("<center><div class='loader'></div></center>");

		$("#testing").load("{{ url('surge/testing') }}");
		$("#linkage").load("{{ url('surge/linkage') }}");
		$("#modality_yield").load("{{ url('surge/modality_yield') }}");
		$("#age_yield").load("{{ url('surge/age_yield') }}");
	}


	$().ready(function(){
		
		// date_filter('financial_year', {{ date('Y') }}, '{{ $date_url }}');

		$("#filter_agency").val(1).change();

	});

</script>

@endsection


