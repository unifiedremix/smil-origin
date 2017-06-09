<?php

error_reporting(0);

require('../remix-utils.php');

$headers = array(
  'X-Handled-By'              => 'smil-origin',
  'Content-Type'              => 'application/smil+xml',
  'Cache-Control'             => 'max-age=60',
);

foreach($headers as $key => $val)
{
  header("$key: $val");
}

$rpid = $_GET["rpid"];

$json = json_decode(base64url_decode($rpid), true);

$videos = "";

// var_dump($json);

foreach ($json["movies"] as $movie) {
  $video_src = $movie["url"];

  foreach ($movie["segments"] as $segment) {
    // var_dump($segment);
    $videos .= "\n      <video src=\"" . $video_src . "\"";
    if (!empty($segment["tc_in"])) {
      $videos .= " clipBegin=\"wallclock(1970-01-01T" . substr($segment["tc_in"], 0, 8) . ".000Z)\"";
    }
    if (!empty($segment["tc_out"])) {
      $videos .= " clipEnd=\"wallclock(1970-01-01T" . substr($segment["tc_out"], 0, 8) . ".000Z)\"";
    }
    $videos .= " />";
  }
}


echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
?>

<smil xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.w3.org/2001/SMIL20/Language">
  <head>
  </head>
  <body>
    <seq><?php echo $videos; ?> 
    </seq>
  </body>
</smil>

