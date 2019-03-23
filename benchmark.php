<?php
function getCpuModel() {
	if(function_exists("exec")) {
		$data = exec('cat /proc/cpuinfo | grep "model name"');
		$data = str_replace("model name	: ", "", $data);
		return trim($data);
	} else {
		return "Function exec is disabled.";
	}
}
function getMemorySpace() {
	if(function_exists("exec")) {
		$data = exec('cat /proc/meminfo | grep "MemTotal"');
		$data = str_replace("MemTotal:       ", "", $data);
		$data = str_replace(" kB", "", $data);
		return Intval($data);
	} else {
		return "Function exec is disabled.";
	}
}
function getSystemMotherboard() {
	if(function_exists("exec")) {
		$data = exec('dmidecode -s "system-product-name"');
		$data = exec('dmidecode -s "system-manufacturer"') . " " . $data;
		return $data;
	} else {
		return "Function exec is disabled.";
	}
}
function getCpuCores() {
	$data = exec('cat /proc/cpuinfo | grep "processor" | wc -l');
	return Intval($data);
}
function runCommand($cmd, $return_exitvalue = false) {
	$descriptorspec = array(
		0 => array("pipe", "r"),
		1 => array("pipe", "w"),
		2 => array("pipe", "r")
	);
	$return_value = 0;
	$result = "";
	$process = proc_open($cmd, $descriptorspec, $pipes);
	if (is_resource($process)) {
		while(!feof($pipes[1])) {
			$result .= fread($pipes[0], 65535);
			$result .= fread($pipes[1], 65535);
			$result .= fread($pipes[2], 65535);
		}
		fclose($pipes[1]);
		fclose($pipes[0]);
		fclose($pipes[2]);
		$return_value = proc_close($process);
	}
	return $return_exitvalue ? $return_value : $result;
}
function isProcessExist($pid) {
	$data = exec('ps aux | awk \'{print $2}\' | grep ' . $pid);
	return !empty($data);
}
if(isset($argv[1]) && $argv[1] == "multi-thread") {
	if(isset($argv[2]) && preg_match("/^[0-9]+$/", $argv[2])) {
		$s1 = microtime(true);
		$float = 0.1;
		$float_1 = 0.5;
		for($i = 0;$i < 1000000000;$i++) {
			$float = $float * $float_1;
		}
		$s2 = microtime(true) - $s1;
		$s2 = round($s2, 5);
		if(!file_exists("/tmp/phpbenchmark/")) {
			mkdir("/tmp/phpbenchmark/");
		}
		file_put_contents("/tmp/phpbenchmark/{$argv[2]}.tmp", $s2);
		exit;
	} else {
		exit;
	}
}

if(isset($argv[1]) && $argv[1] == "multi-thread-hash") {
	if(isset($argv[2]) && preg_match("/^[0-9]+$/", $argv[2])) {
		$data = md5(mt_rand(0, 999999) . time());
		$s1 = microtime(true);
		for($i = 0;$i < 10000000;$i++) {
			$data = hash($i, $data);
		}
		$s2 = microtime(true) - $s1;
		$s2 = round($s2, 5);
		if(!file_exists("/tmp/phpbenchmark/")) {
			mkdir("/tmp/phpbenchmark/");
		}
		file_put_contents("/tmp/phpbenchmark/{$argv[2]}_hash.tmp", $s2);
		exit;
	} else {
		exit;
	}
}

if(isset($argv[1]) && $argv[1] == "multi-thread-md5") {
	if(isset($argv[2]) && preg_match("/^[0-9]+$/", $argv[2])) {
		$data = md5(mt_rand(0, 999999) . time());
		$s1 = microtime(true);
		for($i = 0;$i < 10000000;$i++) {
			$data = md5($data);
		}
		$s2 = microtime(true) - $s1;
		$s2 = round($s2, 5);
		if(!file_exists("/tmp/phpbenchmark/")) {
			mkdir("/tmp/phpbenchmark/");
		}
		file_put_contents("/tmp/phpbenchmark/{$argv[2]}_md5.tmp", $s2);
		exit;
	} else {
		exit;
	}
}

error_reporting(0);
echo <<<EOF
 ____  _   _ ____    ____                  _                          _    
|  _ \\| | | |  _ \\  | __ )  ___ _ __   ___| |__  _ __ ___   __ _ _ __| | __
| |_) | |_| | |_) | |  _ \\ / _ \\ '_ \\ / __| '_ \\| '_ ` _ \\ / _` | '__| |/ /
|  __/|  _  |  __/  | |_) |  __/ | | | (__| | | | | | | | | (_| | |  |   < 
|_|   |_| |_|_|     |____/ \\___|_| |_|\\___|_| |_|_| |_| |_|\\__,_|_|  |_|\\_\\


EOF;
echo "PHP Benchmark 1.0 by Akkariin\n";
echo "https://github.com/kasuganosoras/php-benchmark\n\n";
echo "服务器信息\n\n";
echo " - 主板型号：" . getSystemMotherboard() . "\n";
echo " - CPU 型号：" . getCpuModel() . "\n";
echo " - 内存大小：" . round(getMemorySpace() / 1024 / 1024, 2) . " GB\n";
echo "\n";

// 单线程浮点数计算
$list_float = Array();
echo "开始进行第 1 次单线程浮点测试...";
$s1 = microtime(true);
$float = 0.1;
$float_1 = 0.5;
for($i = 0;$i < 1000000000;$i++) {
	$float = $float * $float_1;
}
$s2 = microtime(true) - $s1;
$s2 = round($s2, 5);
$list_float[] = $s2;
echo "测试完成，耗时: {$s2}\n";

echo "开始进行第 2 次单线程浮点测试...";
$s1 = microtime(true);
$float = 0.1;
$float_1 = 0.5;
for($i = 0;$i < 1000000000;$i++) {
	$float = $float * $float_1;
}
$s2 = microtime(true) - $s1;
$s2 = round($s2, 5);
$list_float[] = $s2;
echo "测试完成，耗时: {$s2}\n";

echo "开始进行第 3 次单线程浮点测试...";
$s1 = microtime(true);
$float = 0.1;
$float_1 = 0.5;
for($i = 0;$i < 1000000000;$i++) {
	$float = $float * $float_1;
}
$s2 = microtime(true) - $s1;
$s2 = round($s2, 5);
$list_float[] = $s2;
echo "测试完成，耗时: {$s2}\n";

echo "开始进行第 4 次单线程浮点测试...";
$s1 = microtime(true);
$float = 0.1;
$float_1 = 0.5;
for($i = 0;$i < 1000000000;$i++) {
	$float = $float * $float_1;
}
$s2 = microtime(true) - $s1;
$s2 = round($s2, 5);
$list_float[] = $s2;
echo "测试完成，耗时: {$s2}\n";

echo "开始进行第 5 次单线程浮点测试...";
$s1 = microtime(true);
$float = 0.1;
$float_1 = 0.5;
for($i = 0;$i < 1000000000;$i++) {
	$float = $float * $float_1;
}
$s2 = microtime(true) - $s1;
$s2 = round($s2, 5);
$list_float[] = $s2;
echo "测试完成，耗时: {$s2}\n";

// 单线程哈希散列计算测试
echo "开始进行第 1 次单线程哈希计算测试...";
$data = md5(mt_rand(0, 999999) . time());
$s1 = microtime(true);
for($i = 0;$i < 10000000;$i++) {
	$data = hash($i, $data);
}
$s2 = microtime(true) - $s1;
$s2 = round($s2, 5);
$list_float[] = $s2;
echo "测试完成，耗时: {$s2}\n";

echo "开始进行第 2 次单线程哈希计算测试...";
$data = md5(mt_rand(0, 999999) . time());
$s1 = microtime(true);
for($i = 0;$i < 10000000;$i++) {
	$data = hash($i, $data);
}
$s2 = microtime(true) - $s1;
$s2 = round($s2, 5);
$list_float[] = $s2;
echo "测试完成，耗时: {$s2}\n";

echo "开始进行第 3 次单线程哈希计算测试...";
$data = md5(mt_rand(0, 999999) . time());
$s1 = microtime(true);
for($i = 0;$i < 10000000;$i++) {
	$data = hash($i, $data);
}
$s2 = microtime(true) - $s1;
$s2 = round($s2, 5);
$list_float[] = $s2;
echo "测试完成，耗时: {$s2}\n";

echo "开始进行第 4 次单线程哈希计算测试...";
$data = md5(mt_rand(0, 999999) . time());
$s1 = microtime(true);
for($i = 0;$i < 10000000;$i++) {
	$data = hash($i, $data);
}
$s2 = microtime(true) - $s1;
$s2 = round($s2, 5);
$list_float[] = $s2;
echo "测试完成，耗时: {$s2}\n";

echo "开始进行第 5 次单线程哈希计算测试...";
$data = md5(mt_rand(0, 999999) . time());
$s1 = microtime(true);
for($i = 0;$i < 10000000;$i++) {
	$data = hash($i, $data);
}
$s2 = microtime(true) - $s1;
$s2 = round($s2, 5);
$list_float[] = $s2;
echo "测试完成，耗时: {$s2}\n";

// 单线程 md5 散列计算测试
echo "开始进行第 1 次单线程 md5 计算测试...";
$data = md5(mt_rand(0, 999999) . time());
$s1 = microtime(true);
for($i = 0;$i < 10000000;$i++) {
	$data = md5($data);
}
$s2 = microtime(true) - $s1;
$s2 = round($s2, 5);
$list_float[] = $s2;
echo "测试完成，耗时: {$s2}\n";

echo "开始进行第 2 次单线程 md5 计算测试...";
$data = md5(mt_rand(0, 999999) . time());
$s1 = microtime(true);
for($i = 0;$i < 10000000;$i++) {
	$data = md5($data);
}
$s2 = microtime(true) - $s1;
$s2 = round($s2, 5);
$list_float[] = $s2;
echo "测试完成，耗时: {$s2}\n";

echo "开始进行第 3 次单线程 md5 计算测试...";
$data = md5(mt_rand(0, 999999) . time());
$s1 = microtime(true);
for($i = 0;$i < 10000000;$i++) {
	$data = md5($data);
}
$s2 = microtime(true) - $s1;
$s2 = round($s2, 5);
$list_float[] = $s2;
echo "测试完成，耗时: {$s2}\n";

echo "开始进行第 4 次单线程 md5 计算测试...";
$data = md5(mt_rand(0, 999999) . time());
$s1 = microtime(true);
for($i = 0;$i < 10000000;$i++) {
	$data = md5($data);
}
$s2 = microtime(true) - $s1;
$s2 = round($s2, 5);
$list_float[] = $s2;
echo "测试完成，耗时: {$s2}\n";

echo "开始进行第 5 次单线程 md5 计算测试...";
$data = md5(mt_rand(0, 999999) . time());
$s1 = microtime(true);
for($i = 0;$i < 10000000;$i++) {
	$data = md5($data);
}
$s2 = microtime(true) - $s1;
$s2 = round($s2, 5);
$list_float[] = $s2;
echo "测试完成，耗时: {$s2}\n";

$result = (array_sum($list_float) / count($list_float));
$one_score = (100 - $result) * 100;
echo "\n + 单核心测试平均值: " . round($result) . " | 单核心性能跑分: " . round($one_score) . "\n\n";

// 多核心性能测试
echo "开始进行多核心浮点测试...";
exec("rm -rf /tmp/phpbenchmark/*");
$runCore = 8;
$result_arr = Array();
$multi_score = Array();
$s1 = microtime(true);
$global_s1 = microtime(true);
for($i = 1;$i <= $runCore;$i++) {
	$null = exec("nohup php {$argv[0]} multi-thread {$i} >/dev/null 2>&1 &");
}
$s = 0;
while($s !== $runCore) {
	for($i = 1;$i <= $runCore;$i++) {
		if(file_exists("/tmp/phpbenchmark/{$i}.tmp")) {
			$result_arr[] = file_get_contents("/tmp/phpbenchmark/{$i}.tmp");
			$s++;
		}
	}
}
$s2 = microtime(true) - $s1;
$s2 = round($s2, 5);
$result = (array_sum($result_arr) / count($result_arr));
$temp_score = ((1000 - $s2) + (1000 - $result)) * 100;
$multi_score[] = $temp_score;
echo "测试完成，耗时: {$s2} | {$temp_score}\n";

// 多核心哈希测试
echo "开始进行多核心哈希测试...";
exec("rm -rf /tmp/phpbenchmark/*");
$runCore = 8;
$result_arr = Array();
$s1 = microtime(true);
for($i = 1;$i <= $runCore;$i++) {
	$null = exec("nohup php {$argv[0]} multi-thread-hash {$i} >/dev/null 2>&1 &");
}
$s = 0;
while($s !== $runCore) {
	for($i = 1;$i <= $runCore;$i++) {
		if(file_exists("/tmp/phpbenchmark/{$i}_hash.tmp")) {
			$result_arr[] = file_get_contents("/tmp/phpbenchmark/{$i}_hash.tmp");
			$s++;
		}
	}
}
$s2 = microtime(true) - $s1;
$s2 = round($s2, 5);
$result = (array_sum($result_arr) / count($result_arr));
$temp_score = ((1000 - $s2) + (1000 - $result)) * 100;
$multi_score[] = $temp_score;
echo "测试完成，耗时: {$s2} | {$temp_score}\n";

// 多核心 MD5 测试
echo "开始进行多核心 MD5 测试...";
exec("rm -rf /tmp/phpbenchmark/*");
$runCore = 8;
$result_arr = Array();
$s1 = microtime(true);
for($i = 1;$i <= $runCore;$i++) {
	$null = exec("nohup php {$argv[0]} multi-thread-md5 {$i} >/dev/null 2>&1 &");
}
$s = 0;
while($s !== $runCore) {
	for($i = 1;$i <= $runCore;$i++) {
		if(file_exists("/tmp/phpbenchmark/{$i}_md5.tmp")) {
			$result_arr[] = file_get_contents("/tmp/phpbenchmark/{$i}_md5.tmp");
			$s++;
		}
	}
}
$s2 = microtime(true) - $s1;
$global_s2 = microtime(true) - $global_s1;
$s2 = round($s2, 5);
$global_s2 = round($global_s2, 5);

$result = (array_sum($result_arr) / count($result_arr));
$temp_score = ((1000 - $s2) + (1000 - $result)) * 100;
$multi_score[] = $temp_score;
echo "测试完成，耗时: {$s2} | {$temp_score}\n";

$multi = array_sum($multi_score) / count($multi_score);
$result_score = round(((1000 - $global_s2) + $multi) * 10);

echo "\n + 多核心测试平均值: " . round($multi) . " | 总耗时: " . round($global_s2, 2) . "s | 多核心性能跑分: {$result_score}\n\n";

// 硬盘读写测试

echo "开始进行硬盘读写测试...";
$random_int = mt_rand(0, 9);
$str = str_pad($str, 512, $random_int);
$file = fopen("/tmp/phpbenchmark/writetest.tmp","a+");
$i = 0;
$bytes = 1024000000;
$size = $bytes / 1000000;
$s1 = microtime(true);
while($i < $bytes){
    $i += fwrite($file, $str);
}
$s2 = microtime(true);
$ws = $s2 - $s1;
$write_speed = round($size / $ws, 2);

unlink("/tmp/phpbenchmark/writetest.tmp");

$disk_score = round(($ws + $write_speed) * 100);
echo "测试完成，耗时：" . round($ws, 2) . "s\n";
echo "\n + 硬盘写入测试: {$write_speed}MB/s | 硬盘性能跑分: {$disk_score}\n\n";
echo "==========================================================\n\n";
echo "服务器综合跑分: " . round(($one_score + $result_score + $disk_score) / 10) . "\n\n";
echo "PHP Benchmark 测试完毕，以上分数仅供参考，分数会受 PHP 版本以及系统当前状况影响。\n\n";
exec("rm -rf /tmp/phpbenchmark/*");
