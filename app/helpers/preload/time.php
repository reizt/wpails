<?php
/**
 * Carbonを用いた再利用関数をまとめたファイル
 * 
 * ここを介してCarbonを利用すれば、もし取得方法を変えてもここだけ変えれば済む
 * 
 * @package Helpers
 */
use Carbon\Carbon;
/** 日本時間で現在時刻を取得する関数 */
function now() :Carbon{
  return Carbon::now()->addHour(9);
}
/** 今日のカーボン
 * @todo 日本時間に合わせる
 */
function today() :Carbon{
  return Carbon::today();
}
/**
 * 日付のフォーマットを自由に変換できる
 * 
 * 「Carbonオブジェクトの初期化 -> format関数を呼ぶ」という定番の作業を1回で済ませられる
 * またCarbonのコンストラクタにnullや''が入ることを防げる
 * @param string $date 日付・時間など自由
 * @param string $format Y-m-dなど
 * @param bool $allow_null_and_empty 空でもCarbonコンストラクタを呼ぶかどうか
 */
function carbon_format(?string $date, ?string $format, bool $allow_null_and_empty = false) :?string{
  if($allow_null_and_empty || !is_null_or_empty($date)){
    $carbon = new Carbon($date);
    return $carbon->format($format);
  }else{
    return null;
  }
}
/** 分数から"時間:分"を返す関数 */
function colon_minutes(int $minutes) :string{
  $h = sprintf("%02d", floor(abs($minutes) / 60));
  $m = sprintf("%02d", floor(abs($minutes) % 60));
  if ($minutes >= 0) {
    return "{$h}:{$m}";
  } else {
    return "-{$h}:{$m}";
  }
}
