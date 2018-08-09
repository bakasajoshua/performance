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
			    Treatment <div class="display_date"></div>
		    </div>
			<div class="panel-body" id="treatment">
				<center><div class="loader"></div></center>
			</div>
		</div>
	</div>
</div>


<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		    <div class="panel-heading">
			    Current on Treatment Trend <div class="display_date"></div>
		    </div>
			<div class="panel-body" id="currenttx">
				<center><div class="loader"></div></center>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		    <div class="panel-heading">
			    New on Treatment Trend <div class="display_date"></div>
		    </div>
			<div class="panel-body" id="newtx">
				<center><div class="loader"></div></center>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-6 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		    <div class="panel-heading">
			    Positive Outcomes Gender <div class="display_date"></div>
		    </div>
			<div class="panel-body" id="gender">
				<center><div class="loader"></div></center>
			</div>
		</div>
	</div>

	<div class="col-md-6 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		    <div class="panel-heading">
			    Positive Outcomes Gender <div class="display_date"></div>
		    </div>
			<div class="panel-body" id="out_gender">
				<center><div class="loader"></div></center>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-6 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		    <div class="panel-heading">
			    Positive Outcomes Age <div class="display_date"></div>
		    </div>
			<div class="panel-body" id="age">
				<center><div class="loader"></div></center>
			</div>
		</div>
	</div>

	<div class="col-md-6 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		    <div class="panel-heading">
			    Positive Outcomes Age <div class="display_date"></div>
		    </div>
			<div class="panel-body" id="out_age">
				<center><div class="loader"></div></center>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		    <div class="panel-heading">
			    PMTCT <div class="display_date"></div>
		    </div>
			<div class="panel-body" id="pmtct">
				<center><div class="loader"></div></center>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		    <div class="panel-heading">
			    EID <div class="display_date"></div>
		    </div>
			<div class="panel-body" id="eid">
				<center><div class="loader"></div></center>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		    <div class="panel-heading">
			    Summary <div class="display_date"></div>
		    </div>
			<div class="panel-body" id="summary">
				<center><div class="loader"></div></center>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		    <div class="panel-heading">
			    Summary Breakdown <div class="display_date"></div>
		    </div>
			<div class="panel-body" id="summary_breakdown">
				<center><div class="loader"></div></center>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		    <div class="panel-heading">
			    New On Treatment <div class="display_date"></div>
		    </div>
			<div class="panel-body" id="art_new">
				<center><div class="loader"></div></center>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		    <div class="panel-heading">
			    Currently On Treatment <div class="display_date"></div>
		    </div>
			<div class="panel-body" id="art_current">
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
		$("#treatment").html("<center><div class='loader'></div></center>");		
		$("#currenttx").html("<center><div class='loader'></div></center>");
		$("#newtx").html("<center><div class='loader'></div></center>");
		$("#gender").html("<center><div class='loader'></div></center>");
		$("#out_gender").html("<center><div class='loader'></div></center>");
		$("#age").html("<center><div class='loader'></div></center>");
		$("#out_age").html("<center><div class='loader'></div></center>");
		$("#pmtct").html("<center><div class='loader'></div></center>");
		$("#eid").html("<center><div class='loader'></div></center>");

		$("#summary").html("<center><div class='loader'></div></center>");
		$("#summary_breakdown").html("<center><div class='loader'></div></center>");
		$("#art_new").html("<center><div class='loader'></div></center>");
		$("#art_current").html("<center><div class='loader'></div></center>");



		$("#treatment").load("{{ secure_url('old/chart/treatment') }}");
		$("#currenttx").load("{{ secure_url('old/chart/current') }}");
		$("#newtx").load("{{ secure_url('old/chart/art_new') }}");
		$("#gender").load("{{ secure_url('old/chart/testing_gender') }}");
		$("#out_gender").load("{{ secure_url('old/chart/outcome_gender') }}");
		$("#age").load("{{ secure_url('old/chart/testing_age') }}");
		$("#out_age").load("{{ secure_url('old/chart/outcome_age') }}");
		$("#pmtct").load("{{ secure_url('old/chart/pmtct') }}");
		$("#eid").load("{{ secure_url('old/chart/eid') }}");

		$("#summary").load("{{ secure_url('old/table/summary') }}");
		$("#summary_breakdown").load("{{ secure_url('old/table/summary_breakdown') }}");
		$("#art_new").load("{{ secure_url('old/table/art_new') }}");
		$("#art_current").load("{{ secure_url('old/table/art_current') }}");
	}


	$().ready(function(){
		
		date_filter('financial_year', {{ date('Y') }}, '{{ $date_url }}');

		$("select").change(function(){
			em = $(this).val();
			id = $(this).attr('id');

			var posting = $.post( "{{ secure_url('filter/any') }}", { 'session_var': id, 'value': em } );

			posting.done(function( data ) {
				console.log(data);
				reload_page();
			});
		});

	});

</script>

@endsection

