<?php


class CTPUserInfo
{
  public $firstName;
  public $lastName;
  public $email;
  public $address;
  public $dateBirth;
  public $department;
  public $title;

  public function __construct($firstName, $lastName, $email, $address, $dateBirth, $department, $title)
  {
    $this->firstName = $firstName;
    $this->lastName = $lastName;
    $this->email = $email;
    $this->address = $address;
    $this->dateBirth = $dateBirth;
    $this->department = $department;
    $this->title = $title;
  }
}

class CTPApiGateway
{
  private $baseUri;

  public function __construct($baseUri)
  {
    $this->baseUri = $baseUri;
  }

  public function getUsersList()
  {
    $path = $this->baseUri . '/users';
    $request = wp_remote_get($path);

    if (is_wp_error($request)) {
      return false;
    }

    $body = wp_remote_retrieve_body($request);

    $result = json_decode($body, true);

    return $this->parseUsersList($result['users']);
  }

  private function parseUsersList($users)
  {
    $result = array();

    foreach ($users as $user) {
      $address = $user['address'];
      $fullAddress = $address['address'] . ', ' . $address['city'] . ', ' . $address['state'] . ', ' . $address['postalCode'];

      array_push(
        $result,
        new CTPUserInfo(
          $user['firstName'],
          $user['lastName'],
          $user['email'],
          $fullAddress,
          $user['birthDate'],
          $user['company']['department'],
          $user['company']['title']
        )
      );
    }

    return $result;
  }
}


class CTPUserListHTML
{
  private $users;

  private $id = "ctp-root-table";
  private $clsName = "ctp-table";
  private $header = "<thead>
    <tr>
    <th>First Name</th>
    <th>Last Name</th>
    <th>Email</th>
    <th>Address</th>
    <th>Date of Birth</th>
    <th>Department</th>
    <th>Title</th>
    </tr>
    </thead>";
  private $controllerId = "ctp-root-table-controller";

  public function __construct($users)
  {
    $this->users = $users;
  }

  public function render($showCount = -1)
  {
    $content = '<table class="' . $this->clsName . '" id="' . $this->id . '">';
    $content .= $this->header;
    $content .= '<tbody>';
    $step = 0;
    $extraUsers = array();

    foreach ($this->users as $user) {
      if ($showCount == -1 || $showCount > $step) {
        $content .= '<tr>';
        $content .= '<td>' . $user->firstName . '</td>';
        $content .= '<td>' . $user->lastName . '</td>';
        $content .= '<td>' . $user->email . '</td>';
        $content .= '<td>' . $user->address . '</td>';
        $content .= '<td>' . $user->dateBirth . '</td>';
        $content .= '<td>' . $user->department . '</td>';
        $content .= '<td>' . $user->title . '</td>';
        $content .= '</tr>';
      } else {
        array_push($extraUsers, $user);
      }

      $step++;
    }
    $content .= '</tbody></table>';

    if (count($extraUsers) > 0) {
      $content .= "<script>window.CTP = { tableId: '" .
        $this->id .
        "', contorllerId: '" .
        $this->controllerId .
        "' ,users: " .
        json_encode($extraUsers) .
        " };</script>";
      $content .= '<div class="btn-container">
        <button onclick="ctpTableController()" class="ctp-show-btn" id="' . $this->controllerId . '">Show more</button>
      </div>';
    }

    return $content;
  }
}

function ctpCreateUsersList()
{
  $api = new CTPApiGateway("https://dummyjson.com");
  $usersList = $api->getUsersList();
  $table = new CTPUserListHTML($usersList);

  return $table->render(10);
}

add_shortcode('ctp-users-list', 'ctpCreateUsersList');
