<div class='osky-hla-report'>
	<h2>HLA Report Generator</h2>

	<div class="form-wrap">
	  	<form action="" method="get">

	  		<input type="hidden" name="page" value="hla-report-generator" />

			<div class="form-group">
		  		<select name="form"  class="form-control">
				<option value="null" selected disabled >Select Form</option>

					<?php foreach( $forms as $form_id => $form_title ): ?>

						<option value="<?php echo $form_id; ?>"><?php echo $form_title; ?></option>

					<?php endforeach; ?>

				</select>
			</div>

			<div class="form-group">
		  		<label>Start Date: </label>
			    <input id='start' class='input form-control' name="start_date" />
			</div>

			<div class="form-group">
			    <label>End Date: </label>
			    <input id='end' class='input form-control' name="end_date" />
			</div>

		    <input type="hidden" name="hla_report_export" value="true" />

			<div class="form-group">
		    	<input type='submit' value='Submit' class="btn btn-default"/>
		    </div>

	    </form>
	</div>
</div>
