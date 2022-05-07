<?php
final class Client extends User{
  protected const USER_TYPE = 'client';
  protected const ORIGINAL_COLUMNS = [
    ['name'=>'business_form', 'label'=>'事業形態'],
    ['name'=>'display_name', 'label'=>'会社名', 'validations'=>['required'=>true]],
    ['name'=>'pic_name', 'label'=>'担当者氏名'],
    ['name'=>'postcode', 'label'=>'郵便番号'],
    ['name'=>'phone_number', 'label'=>'電話番号'],
    ['name'=>'address', 'label'=>'住所'],
    ['name'=>'permission', 'label'=>'アクセス権限', 'default'=>'client'],
  ];
  const STATIC_OPTIONS = [
    'business_form' => [
      ['value'=>null, 'label'=>'未選択'],
      ['value'=>'corporation', 'label'=>'法人'],
      ['value'=>'individual', 'label'=>'個人'],
    ]
  ];
}
