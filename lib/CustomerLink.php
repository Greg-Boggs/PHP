<?php
/**
 * CustomerLink class file.
 */

namespace iATS;

/**
 * Class CustomerLink
 *
 * @package iATS
 */
class CustomerLink extends Core {
  /**
   * CustomerLink constructor.
   *
   * @param string $agentcode
   *   iATS account agent code.
   * @param string $password
   *   iATS account password.
   * @param string $serverid
   *   Server identifier (Defaults to 'NA')
   *   \see serServer()
   */
  public function __construct($agentcode, $password, $serverid = 'NA') {
    parent::__construct($agentcode, $password, $serverid);
    $this->endpoint = '/NetGate/CustomerLink.asmx?WSDL';
  }

  /**
   * Get Customer Code Detail.
   *
   * @param array $parameters
   *   An associative array with the following possible values.
   *
   * @code
   *   array(
   *     'customerIPAddress' => '',
   *     'customerCode' => 'A10396688',
   *     // Not needed for request.
   *     'mop' => 'VISA',
   *     'currency' => 'USD',
   *   );
   * @endcode
   *
   * @return mixed
   *   Client response array or API error.
   */
  public function getCustomerCodeDetail($parameters) {
    $response = $this->apiCall('GetCustomerCodeDetail', $parameters);
    return $this->responseHandler($response, 'GetCustomerCodeDetailV1Result');
  }

  /**
   * Create Credit Card Customer Code.
   *
   * @param array $parameters
   *   An associative array with the following possible values.
   *
   * @code
   *   array(
   *     'customerIPAddress' => '',
   *     'customerCode' => '',
   *     'firstName' => 'Test',
   *     'lastName' => 'Account',
   *     'companyName' => 'Test Co.',
   *     'address' => '1234 Any Street',
   *     'city' => 'Schenectady',
   *     'state' => 'NY',
   *     'zipCode' => '12345',
   *     'phone' => '555-555-1234',
   *     'fax' => '555-555-4321',
   *     'alternatePhone' => '555-555-5555',
   *     'email' => 'email@test.co',
   *     'comment' => 'Customer code creation test.',
   *     'recurring' => FALSE,
   *     'amount' => '5',
   *     'beginDate' => 946684800,
   *     'endDate' => 946771200,
   *     'scheduleType' => 'Annually',
   *     'scheduleDate' => '',
   *     'creditCardCustomerName' => 'Test Account',
   *     'creditCardNum' => '4222222222222220',
   *     'creditCardExpiry' => '12/17',
   *     'mop' => 'VISA',
   *     // Not required.
   *     'currency' => 'USD',
   *   );
   * @endcode
   *
   * @return mixed
   *   Client response array or API error.
   */
  public function createCreditCardCustomerCode($parameters) {
    $response = $this->apiCall('CreateCreditCardCustomerCode', $parameters);
    return $this->responseHandler($response, 'CreateCreditCardCustomerCodeV1Result');
  }

  /**
   * Response Handler for CustomerLink calls.
   *
   * @param array $response
   *   Restriction, error or API result.
   * @param string $result_name
   *   API result name.
   *
   * @return mixed
   *   Restriction, error or API result.
   */
  public function responseHandler($response, $result_name) {
    $result = $this->xml2array($response->$result_name->any);
    if ($result['STATUS'] == 'Failure') {
      return $result['ERRORS'];
    }

    $authresult = FALSE;

    // Handle reject codes.
    if (isset($result['PROCESSRESULT'])) {
      $authresult = $result['PROCESSRESULT']['AUTHORIZATIONRESULT'];
    }
    else if (isset($result['CUSTOMERS']))
    {
      $authresult = $result['CUSTOMERS'];
    }

    if (!$authresult)
    {
      $authresult = $result;
    }

    return $authresult;
  }

}