<?php
require_once '../src/bootstrap.php';

class CustomerRepository
{
  private $db;

  public function __construct()
  {
    $this->db = Database::getInstance();
  }
  public function saveCustomer($customerData)
  {
    $sql = "INSERT INTO customers (id, name, date, state, city, zip, size) 
            VALUES (:id, :name, :date, :state, :city, :zip, :size)
            ON DUPLICATE KEY UPDATE 
            name = :update_name, date = :update_date, state = :update_state, city = :update_city, zip = :update_zip, size = :update_size;";

    $params = [
      ':id' => $customerData['id'],
      ':name' => $customerData['name'],
      ':date' => $customerData['date_added'],
      ':city' => $customerData['location']['city'],
      ':state' => $customerData['location']['state'],
      ':zip' => $customerData['location']['zip'],
      ':size' => $customerData['size'],
      ':update_name' => $customerData['name'],
      ':update_date' => $customerData['date_added'],
      ':update_city' => $customerData['location']['city'],
      ':update_state' => $customerData['location']['state'],
      ':update_zip' => $customerData['location']['zip'],
      ':update_size' => $customerData['size']
    ];

    $this->db->query($sql, $params);
    return $customerData['id'];
  }
}
