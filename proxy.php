<?php
// پروکسی ساده برای دور زدن محدودیت CORS. فقط دامنه‌های مجاز را می‌خواند.
// آدرس‌های مجاز را اینجا بنویسید (فقط نام دامنه، بدون https://):
$ALLOWED = ['call5.tgju.org','call1.tgju.org','nerkhara.com'];
$url = $_GET['url'] ?? '';
$host = parse_url($url, PHP_URL_HOST);
if (!$host || !in_array($host, $ALLOWED, true) || !preg_match('#^https://#', $url)) { http_response_code(403); exit('forbidden'); }
$cache = sys_get_temp_dir().'/pc_'.md5($url);
if (is_file($cache) && time()-filemtime($cache) < 30) { $body = file_get_contents($cache); }
else {
  $ch = curl_init($url);
  curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>1,CURLOPT_TIMEOUT=>8,CURLOPT_FOLLOWLOCATION=>1,CURLOPT_USERAGENT=>'Mozilla/5.0']);
  $body = curl_exec($ch); $code = curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
  if ($body === false || $code !== 200) { http_response_code(502); exit('upstream error'); }
  file_put_contents($cache, $body);
}
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
echo $body;
