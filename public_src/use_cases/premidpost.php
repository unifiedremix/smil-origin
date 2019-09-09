<?php

# sudo apt-get install php5-curl

error_reporting(0);

require('../remix-utils.php');

$ad_url = "http://sample-content/remix08_dref.mp4";
$ad = "      <video src=\"" . htmlspecialchars($ad_url, ENT_QUOTES, 'UTF-8') . "\" />\n";

#$ads .= "      </par>\n";

# is always 2|3|4,5|6|7 - pre, mid, post + clip
$rpid = $_GET["rpid"];

$items = explode(',', base64url_decode($rpid));

$clip_url = "http://sample-content/tears-of-steel-dref.mp4";
$clip .= "      <video src=\"" . htmlspecialchars($clip_url, ENT_QUOTES, 'UTF-8') . "\" clipBegin=\"wallclock(1970-01-01T00:00:00.000Z)\" clipEnd=\"wallclock(1970-01-01T00:00:30.000Z)\"/>\n";
$clip2 .= "      <video src=\"" . htmlspecialchars($clip_url, ENT_QUOTES, 'UTF-8') . "\" clipBegin=\"wallclock(1970-01-01T00:00:32.000Z)\" clipEnd=\"wallclock(1970-01-01T00:01:02.000Z)\"/>\n";


# do something simple
$videos = "";
$roll = $items[0];
if($roll == "2") # pre
{
  $videos = $ad . $clip;
}
else
if($roll == "3") # mid
{
  $videos = $clip . $ad . $clip2;
}
else
if($roll == "4") # post
{
  $videos = $clip . $ad;
}

echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
?>

<smil xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.w3.org/2001/SMIL20/Language">
  <head>
    <meta name="outputDescription" content="<?php echo $clip_url; ?>" />
  </head>
  <body>
    <seq>
<?php echo $videos; ?>
    </seq>
  </body>
</smil>
<?php

$etag = hash("md5", ob_get_contents());

if(trim($_SERVER['HTTP_IF_NONE_MATCH']) === $etag)
{ 
  header("HTTP/1.1 304 Not Modified"); 
  exit;
}

$headers = array(
  'X-Handled-By'              => 'smil-origin',
  'Content-Type'              => 'application/smil+xml',
  'Cache-Control'             => 'max-age=60',
  'ETag'                      => $etag,
);

foreach($headers as $key => $val)
{
  header("$key: $val");
}

