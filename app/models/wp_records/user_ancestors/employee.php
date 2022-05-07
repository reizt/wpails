<?php
final class Employee extends User{
  protected const USER_TYPE = 'employee';
  protected const ORIGINAL_COLUMNS = [
    ['name'=>'nickname', 'label'=>'ニックネーム'],
    ['name'=>'address', 'label'=>'住所'],
    ['name'=>'sex', 'label'=>'性別'],
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
