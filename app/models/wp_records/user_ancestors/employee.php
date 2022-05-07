<?php
final class Employee extends User{
  protected const USER_TYPE = 'employee';
  protected const ORIGINAL_COLUMNS = [
    ['name'=>'nickname', 'label'=>'ニックネーム'],
    ['name'=>'phone_number', 'label'=>'電話番号'],
    ['name'=>'postcode', 'label'=>'郵便番号'],
    ['name'=>'address', 'label'=>'住所'],
    ['name'=>'sex', 'label'=>'性別'],
    ['name'=>'birthday', 'label'=>'生年月日'],
    ['name'=>'job', 'label'=>'職業'],
  ];
  const STATIC_OPTIONS = [
    'permission' => [
      ['value' => 'admin', 'label' => '管理者'],
      ['value' => 'staff', 'label' => 'スタッフ'],
    ],
    'sex' => [
      ['value' => null, 'label' => '未選択'],
      ['value' => 'male', 'label' => '男性'],
      ['value' => 'female', 'label' => '女性'],
    ],
  ];
}
