<?php

function get_db_date( $date ) {
	if( !is_object( $date ) ) {
		$date = new DateTime( $date );
	}

	$date->modify( '-10 Hours' );
	return $date->format( 'Y-m-d H:i:s' );
}

function get_form_key( $form_id ) {
	$keys = array();

	switch ( $form_id ) {
		case 6: //make an appointment form
			$first_name   = 2;
			$last_name    = 3;
			$landline     = 4;
			$phone        = 5;
			$mobile_phone = 6;
			$email        = 7;
			$postcode     = 8;
			$branch       = 11;
			$source_url   = 'source_url';
			$dob_day      = '';
			$dob_month    = '';
			$dob_year     = '';
			$entry_date   = 'date_created';
			break;

		case 8: //Survey
			$first_name       = 24;
			$last_name        = 25;
			$landline         = 42;
			$phone            = 43;
			$mobile_phone     = 26;
			$email            = 28;
			$postcode         = 29;
			$branch           = '';
			$source_url       = 'source_url';
			$dob_day          = 39;
			$dob_month        = 40;
			$dob_year         = 41;
			$make_appointment = 54;
			$entry_date       = 'date_created';
			break;

		case 17: //Landing Page
			$first_name   = 1;
			$last_name    = 2;
			$landline     = '';
			$phone        = 4;
			$mobile_phone = '';
			$email        = 3;
			$postcode     = 6;
			$branch       = '';
			$source_url   = 'source_url';
			$dob_day      = '';
			$dob_month    = '';
			$dob_year     = '';
			$entry_date   = 'date_created';
			break;

		case 19: //Tinnitus
			$first_name   = 1;
			$last_name    = 2;
			$landline     = '';
			$phone        = 4;
			$mobile_phone = '';
			$email        = 3;
			$postcode     = 6;
			$branch       = '';
			$source_url   = 'source_url';
			$dob_day      = '';
			$dob_month    = '';
			$dob_year     = '';
			$entry_date   = 'date_created';
			break;

		case 20: //Win Ipad
			$first_name   = 1;
			$last_name    = 2;
			$landline     = '';
			$phone        = 4;
			$mobile_phone = '';
			$email        = 3;
			$postcode     = 6;
			$branch       = '';
			$source_url   = 'source_url';
			$dob_day      = '';
			$dob_month    = '';
			$dob_year     = '';
			$entry_date   = 'date_created';
			break;

		default:
			return 'Error, invalid form id';
			break;
	}

	$keys = array (
		'first_name'       => $first_name,
		'last_name'        => $last_name,
		'landline'         => $landline,
		'phone'            => $phone,
		'mobile_phone'     => $mobile_phone,
		'email'            => $email,
		'postcode'         => $postcode,
		'branch'           => $branch,
		'source_url'       => $source_url,
		'state'            => '',
		'dob_day'          => $dob_day,
		'dob_month'        => $dob_month,
		'dob_year'         => $dob_year,
		'make_appointment' => $make_appointment,
		'entry_date'       => $entry_date
		);

	return $keys;
}

function sanitize_entries( $form_id, $start_date, $end_date ) {
	$form_id           = $form_id;
	$sort_field_number = 0;
	$sort_direction    = 'DESC';
	$search            = '';
	$offset            = 0;
	$page_size         = 1000;
	$star              = null;
	$read              = null;
	$is_numeric_sort   = false;

	$entries = GFFormsModel::get_leads($form_id, $sort_field_number, $sort_direction, $search, $offset, $page_size, $star, $read, $is_numeric_sort, $start_date, $end_date);

	// var_dump($entries);

	$keys = get_form_key( $form_id );

	$sanitiezed_entries = array();
	$index = 0;

	$phone = array( 'phone', 'landline', 'mobile_phone' );
	foreach ($entries as $entry) {
		foreach ($keys as $key => $value) {
			if( in_array( $key, $phone ) ){
				$sanitiezed_entries[$index][$key] = str_replace( ' ', '', $entry[$value] );
			} else {
				$sanitiezed_entries[$index][$key] = $entry[$value];
			}
		}
		$index++;
	}

	return $sanitiezed_entries;
}

function extra_data( $entries ) {
	if ( !file_exists( OCS_PLUGIN_DIR . "/files/postcode_clinicname.csv" ) ) {
		exit( "Clinic file not exist" . EOL );
	}

	$file = fopen( OCS_PLUGIN_DIR . "/files/postcode_clinicname.csv", "r" );

	while( ! feof( $file ) ){
		$data[] = fgetcsv($file);
	}

	fclose( $file );

	foreach ($data as $key => $clinic) {
		if( $key != 0 ){
			$branch[$clinic[0]] = $clinic[1];
		}
	}

	$temp = array();
	foreach ($entries as $entry) {
		/* get state */
		if( $entry['postcode'] ){
			$entry['state'] = findState( $entry['postcode'] );

		}
		/* end get state */

		/* get entry_branch */
		if( array_key_exists( $entry['postcode'], $branch ) ){
			$entry['branch'] = $branch[$entry['postcode']];
		} else {
			$entry['branch'] = 'PRCC';
		}
		/* end get entry_branch */
		array_push($temp, $entry);
	}

	return $temp;
}

function generate_array_leads( $form_id, $entries ) {
	$temp = array();
	switch ( $form_id ) {
		case 6:
			$campaign_code    = 'WEBSITE';
			$stream           = 'Stream 6';
			$incentive        = 'Free hearing test';
			$appointment_self = '1';
			$message          = '';
			break;

		case 8:
			$campaign_code    = 'ADCC5';
			$stream           = 'Stream 5';
			$incentive        = 'Survey $1k EFTPOS card';
			$appointment_self = '1';
			$message          = '';
			break;

		case 17:
			$campaign_code    = 'ADCC5';
			$stream           = 'Stream 5';
			$incentive        = 'Crystal Clear';
			$appointment_self = '1';
			$message          = '';
			break;

		case 19:
			$campaign_code    = 'ADCC5';
			$stream           = 'Stream 5';
			$incentive        = 'Tinnitus - Win Apple iPad Air n Sennheiser Headphones';
			$appointment_self = '1';
			$message          = 'Tinnitus - Win iPad Air n Free Tinnitus HC';
			break;

		case 20:
			$campaign_code    = 'ADCC5';
			$stream           = 'Stream 5';
			$incentive        = 'Win iPad - Win Apple iPad Air n Sennheiser Headphones';
			$appointment_self = '1';
			$message          = 'Win iPad - Win iPad Air n Free HC';
			break;

		default:
			return 'Error, invalid form id';
			break;
	}
	foreach ( $entries as $entry ) {
		$prefix_fullName = $entry['first_name'] . ' ' . $entry['last_name'];
		$entry_branch    = $entry['branch'];
		$prefix_phone_no = ( $entry['phone'] ) ? '8' . str_replace( ' ', '', $entry['landline'] . $entry['phone'] ) : '';
		$prefix_mobile   = ( $entry['mobile_phone'] ) ? '8' . str_replace( ' ', '', $entry['mobile_phone'] ) : '';
		if( $entry['dob_day'] != '' && $entry['dob_month'] != '' && $entry['dob_year'] != '' ) {
			$dob = $entry['dob_day'] . '-' . $entry['dob_month'] . '-' . $entry['dob_year'];
		} else {
			$dob = '';
		}
		$state               = $entry['state'];
		$postcode            = $entry['postcode'];
		$email               = $entry['email'];
		$path_indicator      = $entry['source_url'];
		$survey_link         = '';
		$appointment_partner = '';
		$entry_date = $entry['entry_date'];

		$temp[] = array($prefix_fullName, $entry_branch, $prefix_phone_no, $prefix_mobile, $dob, $campaign_code, $stream, $incentive, $state, $postcode, $email, $path_indicator, $survey_link, $appointment_self, $appointment_partner, $message, $entry_date);
	}

	return $temp;
}

function filter_entries( $form_id, $entries ) {
	array_unique( $entries ); // Filter Duplicates

	// Filter for confirmation
	switch ( $form_id ) {
		case 8:
			$confirmation = 'Yes';
			break;

		default:
			# code...
			break;
	}

	foreach ( $entries as $key => $entry ) {
		// Filter test entries
		if( $entry['email'] == 'test@osky.com' ) {
			unset( $entries[$key] );
		}

		// Filter non numeric phone
		if( $entry['phone'] != '' && !is_numeric( $entry['phone'] ) ) {
			unset( $entries[$key] );
		}

		if( $entry['landline'] != '' && !is_numeric( $entry['landline'] ) ) {
			unset( $entries[$key] );
		}

		if( $entry['mobile_phone'] != '' && !is_numeric( $entry['mobile_phone'] ) ) {
			unset( $entries[$key] );
		}

		if( isset( $confirmation ) && $entry['make_appointment'] != $confirmation ) {
			unset( $entries[$key] );
		}
	}

	$entries = array_values($entries);

	return $entries;
}

function array_to_csv_download($array, $filename = "report.csv", $delimiter=",") {
    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename="'.$filename.'";');

    // open the "output" stream
    // see http://www.php.net/manual/en/wrappers.php.php#refsect2-wrappers.php-unknown-unknown-unknown-descriptioq
    $f = fopen('php://output', 'w');


    foreach ($array as $line) {
        fputcsv($f, $line, $delimiter);
    }
}

function print_osky($object, $heading = '', $var_dump = false) {
    echo '<div class="clearfix"></div>';

    $bt = array(array('file'=>'', 'line'=>''));
    if(empty($heading))
    {
        $bt = debug_backtrace();
        $src = file($bt[0]["file"]);
        $line = $src[ $bt[0]['line'] - 1 ];
        preg_match( "#\\$(\w+)#", $line, $match );

        if(!empty($match[0]))
        {
            $heading = $match[0];
        }
    }

    if(!empty($heading))
    {
        printf('<h3 style="color: #F00;margin: 0;padding: 25px 0 0;line-height: 0px;">%s</h3>', $heading);
        if(isset($src))
        {
            echo '<small style="margin-top: 10px;display: block;">' . $bt[0]["file"]  . ':' . $bt[0]['line'] . '</small>';
        }
    }
    echo "\r\n" . '<pre style="width: 90%;word-wrap: break-word;">';
        if($object)
        {
            ($var_dump ? var_dump($object) : print_r($object));
        }
        else
        {
            var_dump($object);
        }
    echo '</pre>' . "\r\n";
}