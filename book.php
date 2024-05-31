<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<?php
 // Start the session
        session_start();
        var_dump($_SESSION['']);
        // Check if the selectedDataArray key exists in the session
        if(isset($_SESSION['selectedDataArray'])) {
            // Decode the JSON string into a PHP array
            $selectedDataArray = $_SESSION['selectedDataArray'];
            


            // Now $selectedDataArray contains the decoded array
            // You can access and use its elements like any other PHP array

            // For example, to loop through the array and access its elements:
            foreach($selectedDataArray as $data) {
                echo "departureId: " . $data['departureId'] . "<br>";
                echo "priceTypeId: " . $data['priceTypeId'] . "<br>";
                echo "cabinId: " . $data['cabinId'] . "<br>";
                echo "quantity: " . $data['quantity'] . "<br>";
                echo "<br>";
            }
        } else {
            echo "No session data found.";
        }





function getToken(){ 
    $url = "https://jb-auth-uat.azurewebsites.net/Client";
    $postData = array(
        "claims" => array(
            "userName" => "stuart@geelongtravel.com.au"
        ),
        "clientId" => "515909a2-c9a6-46ee-a923-6cb1170e3571",
        "secret" => "oF7QWRUYYUmISudsRgixrg=="
    );
    
    // Convert the data to JSON format
    $postDataJson = json_encode($postData);
    // Initialize curl
    $ch = curl_init();
    // Set curl options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type: application/json" // Set the Content-Type header to JSON
    ));
    curl_setopt($ch, CURLOPT_POST, true); // Set the request method to POST
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postDataJson); // Set the request body data

    // Execute curl request
    $response = curl_exec($ch);

    // Decode JSON response
    $data = json_decode($response, true);

    // Check for curl errors
    if ($response === false) {
        die("Error: Curl request failed: " . curl_error($ch));
    }
    // Close curl
    curl_close($ch);

    $token = $data['accessToken'];
    return $token;
}
// package id 
if(isset($_GET['packageId'])){
    $packageId = $_GET['packageId'];
}   
// to initilize code 
if(isset($_GET['code'])){
    $code = $_GET['code'];
} 
// to initilize OCUPANY ID 
if(isset($_GET['occupancyId'])){
    $occupancyId = $_GET['occupancyId'];
}
if(isset($_GET['packageId'])){
    $url = "https://jb-b2b-api-test.azurewebsites.net/api/search/agent/search-packages?package=GHROW";

    $token = getToken();

    // Initialize curl
    $ch = curl_init();

    // Set curl options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Authorization: Bearer $token"
    ));

    // Execute curl request
    $response = curl_exec($ch);

    // Check for curl errors
    if ($response === false) {
        die("Error: Curl request failed: " . curl_error($ch));
    }

    // Close curl
    curl_close($ch);

    // Decode JSON response
    $data = json_decode($response, true);

    // Filter data based on search parameters
    $filteredData = $data['packageResponses'];
    foreach($filteredData as $filter){
        $packagePriceTypeId = $filter['priceTypeId']; 
    }
    // echo  $packagePriceTypeId;
    // Filter the data based on the specified packageId
    $filteredData = array_filter($filteredData, function($item) use ($packageId) {
        return $item['packageId'] == $packageId;
    });
}
// to repopulate api   
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process form data
    $title = $_POST["options"];
    $fname = $_POST["fname"];
    $lname = $_POST["lname"];
    $address1 = $_POST["address1"];
    $address2 = $_POST["address2"];
    $postno = $_POST["postno"];
    $city = $_POST["city"];
    $state = $_POST["state"];
    $country = $_POST["country"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];
    $priceTypeId = $selectedPriceTypeId;
    $veg = isset($_POST['veg']) && $_POST['veg'] == 'on' ? true : false;
    $lactose = isset($_POST['lactose']) && $_POST['lactose'] == 'on' ? true : false;
    $vegan = isset($_POST['vegan']) && $_POST['vegan'] == 'on' ? true : false;
    $gluten = isset($_POST['gluten']) && $_POST['gluten'] == 'on' ? true : false;
    $infants_details = $_POST["infants_details"];
    $other_request = $_POST["other_request"];
    $agent_ref = $_POST["agent_ref"];

    Construct the array for JSON conversion
    $postData = array(
        "packageId" => $packageId, // Hardcoded for now, replace with dynamic value if needed
        "packageDepartureDate" => "2024-05-30T18:00:00.480Z", // Hardcoded for now, replace with dynamic value if needed
        "packageBookingDate" => "2024-05-06T02:59:14.480Z", // Hardcoded for now, replace with dynamic value if needed
        "packagePriceTypeId" => $packagePriceTypeId, // Hardcoded for now, replace  with dynamic value if needed value = 3
        "bookingTypeId" => $bookingTypeId, // Hardcoded for now, replace with dynamic value if needed value = 2
        "employeeId" => "yrz7m8i3", // Hardcoded for now, replace with dynamic value if needed
        "cabins" => array(
            array(
                "cabinId" => 1, // Hardcoded for now, replace with dynamic value if needed
                "elementId" => $code, // Hardcoded for now, replace with dynamic value if needed
                "elementIdSole" => $code, // Hardcoded for now, replace with dynamic value if needed
                "pax" => $occupancyId, // Assuming single passenger for now, adjust as needed
                "occupancyType" => $occupancyId, // Assuming single occupancy for now, adjust as needed value = 1;
                "priceTypeId" => 16 // Hardcoded for now, replace with dynamic value if needed
            )
        ),
        "passengers" => array(
            array(
                "cabinId" => 1, // Hardcoded for now, replace with dynamic value if needed
                "isAdult" => true,
                "passengerNumberInRoom" => $occupancyId, // Assuming single passenger for now, adjust as needed
                "isLeadPassenger" => true,
                "firstName" => $fname,
                "lastName" => $lname,
                "title" => $title,
                "addressLine1" => $address1,
                "addressLine2" => $address2,
                "addressPostCode" => $postno,
                "addressCity" => $city,
                "addressState" => $state,
                "addressStateId" => 0, // Hardcoded for now, replace with dynamic value if needed
                "addressCountry" => $country,
                "addressCountryId" => 0, // Hardcoded for now, replace with dynamic value if needed
                "phoneNumber" => $phone,
                "emailAddress" => $email,
                "isVegeterian" => $veg,
                "isLactoseFree" => $lactose,
                "isGlutenFree" => $gluten,
                "isVegan" => $vegan,
                "travellingWithInfant" => false, // Assuming not traveling with an infant for now
                "travellingWithInfantDetails" => "", // No details provided, adjust as needed
                "otherRequests" => false, // Assuming no other requests for now
                "otherRequestsValue" => "", // No other requests provided, adjust as needed
                "qffNumber" => "", // No QFF number provided, adjust as needed
                "jbLoyaltyNumber" => "", // No JB Loyalty number provided, adjust as needed
                "jbTravelClubOptIn" => false, // Assuming not opted in for JB Travel Club for now
                "mailingList" => false, // Assuming not subscribed to mailing list for now
                "hasConcession" => false, // Assuming no concession for now
                "concessionCardType" => "", // No concession card type provided, adjust as needed
                "concessionCardDept" => "", // No concession card department provided, adjust as needed
                "concessionNumber" => "" // No concession number provided, adjust as needed
            )
        ),
        "agentReference" => $agent_ref
    );
    
    // Convert the data to JSON format
    $postDataJson = json_encode($postData);
    
    // You can perform further processing, validation, or save data to a database here

    $url = "https:///jb-b2b-api-test.azurewebsites.net/api/Booking/create-and-confirm";

    // Your authorization token
    $token = getToken();
    // Initialize curl
    $ch = curl_init();
    // Set curl options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type: application/json", // Set the Content-Type header to JSON
        "Authorization: Bearer $token"
    ));
    curl_setopt($ch, CURLOPT_POST, true); // Set the request method to POST
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postDataJson); // Set the request body data

    // Execute cURL request
$response = curl_exec($ch);
$data = json_decode($response,true);
// Check for errors
if ($response === false) {
    die("Error: Curl request failed: " . curl_error($ch));
} else {
    
}
// Close cURL session
curl_close($ch);
}


?>
<body>
     <!-- <div class="container">
        <h1 class="text-center my-4">Journey <span class="text-primary">Beyond</span></h1>
        <?php
        if (!isset($data['bookingReferenceNumber'])) {
            // Show the form if booking reference number is not set
        ?>
        <div id="div" >
        <form method="post">
            <div class="form-group">
                <label for="package_id">Package id : </label>
                <input type="text" name="package_id" id="package_id" disabled value="<?php echo $packageId; ?>">
            </div>

            <div class="form-group">
                <label for="options">Select title : </label>
                <select name="options" id="options">
                    <option value="" selected disabled hidden>Select Title:</option>
                    <option value="Mr">Mr</option>
                    <option value="Dr">Dr</option>
                    <option value="Father">Father</option>
                    <option value="Hon">Hon</option>
                    <option value="Lady">Lady</option>
                    <option value="Miss">Miss</option>
                    <option value="Mrs">Mrs</option>
                    <option value="Ms">Ms</option>
                    <option value="Mstr">Mstr</option>
                    <option value="Prof">Prof</option>
                    <option value="Rev">Rev</option>
                    <option value="Sir">Sir</option>
                    <option value="Sister">Sister</option>
                </select>
            </div>
            <div id="div" >
                <div class="form-group">
                    <label for="fname">Enter First Name : </label>
                    <input type="text" class="form-control" id="fname" name="fname" aria-describedby="emailHelp" placeholder="Enter First name">
                </div>
                <div class="form-group">
                    <label for="lname">Enter Last Name : </label>
                    <input type="text" class="form-control" id="lname" name="lname" aria-describedby="emailHelp" placeholder="Enter Lirst name">
                </div>
            </div>
            <div id="div" >
                <div class="form-group">
                    <label for="address1">Address Line : </label>
                    <input type="text" class="form-control" id="address1" name="address1" aria-describedby="emailHelp" placeholder="Enter Address">
                </div>
                <div class="form-group">
                    <label for="address2">Additional Address Line : </label>
                    <input type="text" class="form-control" id="address2" name="address2" aria-describedby="emailHelp" placeholder="Enter Address">
                </div>
            </div>
            <div id="div" >
                <div class="form-group">
                    <label for="postno">Enter Post code : </label>
                    <input type="text" class="form-control" id="postno" name="postno" aria-describedby="emailHelp" placeholder="Enter Post code">
                </div>
                <div class="form-group">
                    <label for="city">Enter City : </label>
                    <input type="text" class="form-control" id="city" name="city" aria-describedby="emailHelp" placeholder="Enter City">
                </div>
            </div>
            <div id="div" >
                <div class="form-group">
                    <label for="state">Enter State : </label>
                    <input type="text" class="form-control" id="state" name="state" aria-describedby="emailHelp" placeholder="Enter State">
                </div>
                <div class="form-group">
                    <label for="country">Enter Country : </label>
                    <input type="text" class="form-control" id="country" name="country" aria-describedby="emailHelp" placeholder="Enter Country">
                </div>
            </div>
            <div id="div" >
                <div class="form-group">
                    <label for="phone">Enter Phone no : </label>
                    <input type="text" class="form-control" id="phone" name="phone" aria-describedby="emailHelp" placeholder="Enter Phone no">
                </div>
                <div class="form-group">
                    <label for="email">Enter Email : </label>
                    <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp" placeholder="Enter Email">
                </div>
            </div>
            <div class="form-group">
                <input type="checkbox" class="form-check-input" id="veg" name="veg">
                <label class="form-check-label" for="veg">Vegeterian</label>
                <input type="checkbox" class="form-check-input" id="lactose" name="lactose">
                <label class="form-check-label" for="lactose">Lactose Free</label>
                <input type="checkbox" class="form-check-input" id="vegan" name="vegan">
                <label class="form-check-label" for="vegan">Vegan</label>
                <input type="checkbox" class="form-check-input" id="gluten" name="gluten">
                <label class="form-check-label" for="gluten">Gluten Free</label>
            </div>
            <div class="form-group">
                <label for="infants_details">Enter Infants Details : </label>
                <textarea name="infants_details" class="form-control" id="infants_details"></textarea>
            </div>
            <div class="form-group">
                <label for="other_request">Enter Other Request : </label>
                <textarea class="form-control" name="other_request" id="other_request"></textarea>
            </div>
            <div class="form-group">
                <label for="agent_ref">Select agent : </label>
                <select name="agent_ref" id="agent_ref">
                    <option value="" selected disabled hidden>Select your agent</option>
                    <option value="Sounthararajan Sriharshan">Sounthararajan Sriharshan</option>
                    <option value="Kiran Dhoke">Kiran Dhoke</option>
                </select>
            </div>
            <div id="div" >
                <button class="btn btn-primary" type="submit">Submit</button>
            </div>
        </form>
        </div>
        <?php
        } else {
            // Hide the form if booking reference number is set
        ?>
         <div class="d-flex justify-content text-center">
                <div>
                    <p>Your booking has been confirmed. Your booking reference number is: <?php echo $data['bookingReferenceNumber']; ?></p>
                    <p>Thank you for booking with us!</p>
                </div>
            </div>
        <?php
        }
        ?>
     </div> -->

</body>
</html>
<style>
    .form-check-input{
        margin-right:12px;
    }
    .form-check-label{
        margin-right:8px;
    }
    .container{
        max-width:660px !important;
    }
    label,input,textarea{
        margin-bottom:15px;
    }
    #div{
        display:flex;
        justify-content:center;
        gap:25px;
    }
    #priceT{
        margin: 10px 0 20px 0;
    }
    @media screen and  (max-width:767px) {
        #div{
            display:block !important;
        }
    }
</style>