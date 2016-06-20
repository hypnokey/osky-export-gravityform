<?php
/**
* Returns the state for a postcode.
* eg. NSW
*
* @author http://waww.com.au/ramblings/determine-state-from-postcode-in-australia
* @link http://en.wikipedia.org/wiki/Postcodes_in_Australia#States_and_territories
*/
function findState($postcode) {
  $ranges = array(
    'NSW' => array(
      1000, 1999,
      2000, 2599,
      2619, 2898,
      2921, 2999
    ),
    'ACT' => array(
      200, 299,
      2600, 2618,
      2900, 2920
    ),
    'VIC' => array(
      3000, 3999,
      8000, 8999
    ),
    'QLD' => array(
      4000, 4999,
      9000, 9999
    ),
    'SA' => array(
      5000, 5999
    ),
    'WA' => array(
      6000, 6797,
      6800, 6999
    ),
    'TAS' => array(
      7000, 7999
    ),
    'NT' => array(
      800, 999
    )
  );
  $exceptions = array(
    872 => 'NT',
    2540 => 'NSW',
    2611 => 'ACT',
    2620 => 'NSW',
    3500 => 'VIC',
    3585 => 'VIC',
    3586 => 'VIC',
    3644 => 'VIC',
    3707 => 'VIC',
    2899 => 'NSW',
    6798 => 'WA',
    6799 => 'WA',
    7151 => 'TAS'
  );

  $postcode = intval($postcode);
  if ( array_key_exists($postcode, $exceptions) ) {
    return $exceptions[$postcode];
  }

  foreach ($ranges as $state => $range)
  {
    $c = count($range);
    for ($i = 0; $i < $c; $i+=2) {
      $min = $range[$i];
      $max = $range[$i+1];
      if ( $postcode >= $min && $postcode <= $max ) {
        return $state;
      }
    }
  }

  return null;
}