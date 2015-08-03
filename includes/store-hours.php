  <?php 

// ----------------------------------
// PHP STORE HOURS
// ----------------------------------
// Version 3.0
// Written by Cory Etzkorn
// https://github.com/coryetzkorn/php-store-hours

// DO NOT MODIFY THIS CLASS FILE

class StoreHours {

  function __construct($hours = array(), $exceptions = array(), $template = array()) {
    $this->hours = $hours;
    $this->exceptions = $exceptions;
    $this->template = $template;
    if(!isset($this->template['open'])) {
      $this->template['open'] = "<h3>Yes, we're open! Today's hours are {%hours%}.</h3>";
    }
    if(!isset($this->template['closed'])) {
      $this->template['closed'] = "<h3>Sorry, we're closed. Today's hours are {%hours%}.</h3>";
    }
    if(!isset($this->template['closed_all_day'])) {
      $this->template['closed_all_day'] = "<h3>Sorry, we're closed.</h3>";
    }
    if(!isset($this->template['separator'])) {
      $this->template['separator'] = " - ";
    }
    if(!isset($this->template['join'])) {
      $this->template['join'] = " and ";
    }
    if(!isset($this->template['format'])) {
      $this->template['format'] = "g:ia";
    }
    if(!isset($this->template['hours'])) {
      $this->template['hours'] = "{%open%}{%separator%}{%closed%}";
    }
  }

  // Returns today's hours
  public function hours_today() {
    $today = strtotime('today midnight');
    $day = strtolower(date("D"));
    $exceptions = $this->exceptions;
    $hours_today = $this->hours[$day];
    if($exceptions) {
      foreach($exceptions as $ex_day => $ex_hours) {
        //echo 'ex' . date('r', strtotime($ex_day + '/2014'));
        //echo 'today' . date('r', $today);
        if(strtotime($ex_day) == $today) {
          // Today is an exception, use alternate hours instead
          $hours_today = $ex_hours;
        }
      }
    }
    return $hours_today;
  }

  // Returns boolean
  public function is_open() {
    $now = strtotime(date("G:i"));
    $hours_today = $this->hours_today();
    $exceptions = $this->exceptions;
    $is_open = 0;
    if(!empty($hours_today[0])) {
      foreach($hours_today as $range) {
        $range = explode("-", $range);
        $start = strtotime($range[0]);
        $end = strtotime($range[1]);
        // Add one day if the end time is past midnight
        if($end <= $start) {
          $end = strtotime($range[1] . ' + 1 day');
        }
        if (($start <= $now) && ($end >= $now)) {
          $is_open ++;
        }
      }
    }
    if($is_open > 0) {
      return true;
    } else {
      return false;
    }
  }

  // Prep HTML
  private function render_html($template_name) {
    $template = $this->template;
    $hours_today = $this->hours_today();
    $output = '';
    $index = 0;
    if(!empty($hours_today[0])) {
      $hours_template = '';
      foreach($hours_today as $range) {
        $range = explode("-", $range);
        $start = strtotime($range[0]);
        $end = strtotime($range[1]);
        if($index >= 1) {
          $hours_template .= $template['join'];
        }
        $hours_template .= $template['hours'];
        $hours_template = str_replace('{%open%}', date($template['format'], $start), $hours_template);
        $hours_template = str_replace('{%closed%}', date($template['format'], $end), $hours_template);
        $hours_template = str_replace('{%separator%}', $template['separator'], $hours_template);
        $index ++;
      }
      $output .= str_replace('{%hours%}', $hours_template, $template[$template_name]);
    } else {
      $output .= $template['closed_all_day'];
    }
    echo $output;
  }

  // Output HTML
  public function render() {
    if($this->is_open()) {
      $this->render_html('open');
    } else {
      $this->render_html('closed');
    }
  }

}

date_default_timezone_set('America/NewYork');

// REQUIRED
// Define daily open hours
// Must be in 24-hour format, separated by dash 
// If closed for the day, leave blank (ex. sunday)
// If open multiple times in one day, enter time ranges separated by a comma
$hours = array(
  'mon' => array(''),
  'tue' => array('13:00-21:00'),
  'wed' => array('13:00-21:00'), 
  'thu' => array('13:00-21:00'), // Open late
  'fri' => array('16:00-23:00'),
  'sat' => array('16:00-23:00'),
  'sun' => array('') // Closed all day
);

// OPTIONAL
// Add exceptions (great for holidays etc.)
// MUST be in format month/day
// Do not include the year if the exception repeats annually
$exceptions = array(
  'Christmas' => '12/25',
  'New Years Day' => '1/1'
);

// OPTIONAL
// Place HTML for output below. This is what will show in the browser.
// Use {%hours%} shortcode to add dynamic times to your open or closed message.
$template = array(
  'open' => "<strong class='open'>Yes, we're open! Today's hours are {%hours%}.</strong>",
  'closed' => "<strong class='closed'>Sorry, we're closed. Today's hours are {%hours%}.</strong>",
  'closed_all_day' => "<strong class='closed'>Sorry, we're closed today.</strong>",
  'separator' => " - ",
  'join' => " and ",
  'format' => "g:ia", // options listed here: http://php.net/manual/en/function.date.php
  'hours' => "{%open%}{%separator%}{%closed%}"
);

$store_hours = new StoreHours($hours, $exceptions, $template);
$store_hours->render();

?>
