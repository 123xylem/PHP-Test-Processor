<?php
// src/CustomerImporter.php

class CustomerImporter
{
  private $dataFile;
  private $customerData = [];

  public function __construct($dataFile)
  {
    $this->dataFile = $dataFile;
  }

  public function import()
  {

    $content = file_get_contents($this->dataFile);
    $data = json_decode($content, true);
    $this->customerData = $data;

    return $this->customerData;
  }

  public function getCustomerInfo()
  {
    $customerInfo = [];
    $newCustomer = [];
    foreach ($this->customerData as $customer) {
      $newCustomer['id'] = $customer['id'] ?? null;
      $newCustomer['name'] = $customer['name'] ?? null;
      $newCustomer['date_added'] = $customer['date_added'] ?? null;
      $newCustomer['size'] = $customer['size'] ?? null;
      $newCustomer['location'] = $customer['location'] ?? null;
      $customerInfo[] = $newCustomer;
    }
    return $customerInfo;
  }
}
