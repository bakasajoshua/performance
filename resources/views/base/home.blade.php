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
			    Testing Gender <div class="display_date"></div>
		    </div>
			<div class="panel-body" id="gender">
				<center><div class="loader"></div></center>
			</div>
		</div>
	</div>

	<div class="col-md-6 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		    <div class="panel-heading">
			    Outcomes Gender <div class="display_date"></div>
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
			    Testing Age <div class="display_date"></div>
		    </div>
			<div class="panel-body" id="age">
				<center><div class="loader"></div></center>
			</div>
		</div>
	</div>

	<div class="col-md-6 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		    <div class="panel-heading">
			    Outcomes Age <div class="display_date"></div>
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

		$("#currenttx").load("{{ secure_url('chart/current') }}");
		$("#newtx").load("{{ secure_url('chart/art_new') }}");
		$("#gender").load("{{ secure_url('chart/testing_gender') }}");
		$("#out_gender").load("{{ secure_url('chart/outcome_gender') }}");
		$("#age").load("{{ secure_url('chart/testing_age') }}");
		$("#out_age").load("{{ secure_url('chart/outcome_age') }}");

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


