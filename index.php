<?php
//this line makes PHP behave in a more strict way
declare(strict_types=1);

//we are going to use session variables so we need to enable sessions
session_start();

// define variables and set to empty values
$emailErr = $streetErr = $streetnumberErr = $cityErr = $zipcodeErr = $productsErr = "";
$email = $street = $streetnumber = $city = $zipcode = $products = "";
$isFormValid;
$submit = "";
$deliveryTime = "";
$totalMessage = "";
$total = 0;

// Validate and check requirements form
if (!empty($_POST)) {

  //email
  if (empty($_POST["email"])) {
    $emailErr = "Email is required";
    $isFormValid = false;
  } else {
    $email = test_input($_POST["email"]);
    // check if e-mail address is well-formed
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $emailErr = "Invalid email format";
      $isFormValid = false;
    }
  }

  //street
  if (empty($_POST["street"])) {
    $streetErr = "street is required";
    $isFormValid = false;
  } else {
    $street = test_input($_POST["street"]);
  }

  //street number
  if (empty($_POST["streetnumber"])) {
    $streetnumberErr = "street number is required";
    $isFormValid = false;
  } else {
    $streetnumber = test_input($_POST["streetnumber"]);
    // check if the streetnumber only consists out of numbers
    if (!preg_match("/^[0-9]*$/", $streetnumber)) {
      $streetnumberErr = "Only numbers are allowed";
      $isFormValid = false;
    }
  }

  //city
  if (empty($_POST["city"])) {
    $cityErr = "city is required";
    $isFormValid = false;
  } else {
    $city = test_input($_POST["city"]);
  }

  //zipcode
  if (empty($_POST["zipcode"])) {
    $zipcodeErr = "zipcode is required";
    $isFormValid = false;
  } else {
    $zipcode = test_input($_POST["zipcode"]);
    // check if the zipcode only consists out of numbers
    if (!preg_match("/^[0-9]*$/", $zipcode)) {
      $zipcodeErr = "Only numbers are allowed";
    }
  }

  //products
  if (empty($_POST["products"])) {
    $productsErr = "Pick at least one product";
    $isFormValid = false;
  } else {
    $products = $_POST["products"];
  }

  // The form is valid
  if ($isFormValid = true) {
    createSessionValues();

    $submit = 'Thank you! Your order is submitted. </br>';

    $deliveryTime = estimateDeliveryTime();
    $total = totalAmountPerOrder();
    $totalMessage = 'The total amount of this order is €' . totalAmountPerOrder() . "</br>";
  }
};


// Check if session exist and store them in the values to autofill the form.
if (isset($_SESSION["email"])) {
  $email = $_SESSION["email"];
};
if (isset($_SESSION["street"])) {
  $street = $_SESSION["street"];
};
if (isset($_SESSION["streetnumber"])) {
  $streetnumber = $_SESSION["streetnumber"];
};
if (isset($_SESSION["city"])) {
  $city = $_SESSION["city"];
};
if (isset($_SESSION["zipcode"])) {
  $zipcode = $_SESSION["zipcode"];
};


// Create the session values
function createSessionValues()
{
  $_SESSION["email"] = $_POST["email"];
  $_SESSION["street"] = test_input($_POST["street"]);
  $_SESSION["streetnumber"] = test_input($_POST["streetnumber"]);
  $_SESSION["city"] = test_input($_POST["city"]);
  $_SESSION["zipcode"] = test_input($_POST["zipcode"]);
};

//your products with their price.
if (isset($_GET["drinks"]) && $_GET['drinks'] == 1) {
  $products = [
    ['name' => 'Sake', 'price' => 4],
    ['name' => 'Asahi', 'price' => 5],
    ['name' => 'Sprite', 'price' => 2],
    ['name' => 'Ice-tea', 'price' => 3],
  ];
} else {
  $products = [
    ['name' => 'sashimi', 'price' => 8],
    ['name' => 'unagi maki', 'price' => 12],
    ['name' => 'Philadelphia roll', 'price' => 15],
    ['name' => 'California roll', 'price' => 14],
    ['name' => 'Tuna roll', 'price' => 10]
  ];
};

// Calculate the total amount per order
function totalAmountPerOrder()
{
  $totalValue = 0;
  foreach ($_POST["products"] as $value) {
    // echo "$value <br>";
    $totalValue += $value;
  }

  return $totalValue;
}

// count delivery time
function estimateDeliveryTime()
{
  $timeNow = date("H:i");

  if (!empty($_POST["expressDelivery"])) {
    $hourExpressDel = date('H:i', strtotime('+45 minutes', strtotime($timeNow)));
    return "Your order will be delivered at " . $hourExpressDel . "</br>";
  } else {
    $hourNormalDel = date('H:i', strtotime('+2 hours', strtotime($timeNow)));
    return "Your order will be delivered at " . $hourNormalDel . "</br>";
  };
}

//Total value with cookies
$totalAmount = 0;

if (isset($_COOKIE['amountOverall'])) {
  $totalAmount = $_COOKIE['amountOverall'] + $total;
};

$cookie = "$totalAmount";
setcookie("amountOverall", $cookie, time() + (86400 * 30), "/"); // 86400 = 1 day

//Function to validate the input

function test_input($data)
{
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}


/* // Send an email
$to = $email;
$subject = "Your order with 'Sushi studio'";
$txt = $submit . "</br>" . $deliveryTime . "</br>" . $totalMessage . "</br>";
mail($to, $subject, $txt); */

// Debug function what is happening
function whatIsHappening()
{
  echo '<h2>$_GET</h2>';
  var_dump($_GET);
  echo '<h2>$_POST</h2>';
  var_dump($_POST);
  echo '<h2>$_COOKIE</h2>';
  var_dump($_COOKIE);
  echo '<h2>$_SESSION</h2>';
  var_dump($_SESSION);
}

//-----------------------------------------------------------------------------
// NAME: sendEmail
// FUNCTION: send the email, using the input that the user typed
//-----------------------------------------------------------------------------
function sendEmail($email)
{


  $headers .= "From: Oyuna Tsybdenova" . "\r\n";
  $message = "Dear Customer: \n\n Thank you for your order!.\n\n\nSincerely : sushi studio";
  $subject = "Order Received";
  mail($email, $subject, $message, $headers);
}
// whatIsHappening();

require 'form-view.php';