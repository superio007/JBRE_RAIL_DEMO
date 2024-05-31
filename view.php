<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View - Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php
    session_start();
    if(isset($_POST['quantity'])) {
        // Update session variable 'quantity' with the quantity value from the AJAX request
        $_SESSION['quantity'] = $_POST['quantity'];
        echo "Session variable 'quantity' updated successfully.";
    } else {
        echo "Error: Quantity value not received.";
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
    // Your authorization token
    $token = getToken();
    
    // Check if token needs refreshing
    if (isset($data) === null) {
        // Refresh token
        $token = getToken();
    }
    
    // API endpoint
    $url = "https://jb-b2b-api-test.azurewebsites.net/api/search/agent/search-packages?package=GHROW";
    
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
    
    // Check if JSON decoding was successful
    if (isset($data) === null) {
        // Output the actual response for debugging
        echo "Error: Kindly check the authentication token";
        exit;
    }
    $cabinId = "23985";
    // $departure_id = isset($_POST['departureId']);
    // echo "cabin : " . $cabinId ;
    // echo"departure : " . $departure_id;
    
    // Check if packageId is provided in the URL
    if(isset($_GET['packageId'])) {
        $packageId= $_GET['packageId'];
        
        // Find the package with the matching packageId
        $filteredPackage = null;
        foreach($data['packageResponses'] as $item) {
            if($item['packageId'] == $packageId) {
                $filteredPackage = $item;
                break;
            }
        }
        
        if($filteredPackage !== null) {
            // Display package details
            ?>
            <div class="w-100 mb-2" id="div" style="background-image: url(<?php echo $filteredPackage['alternativePackageImageUrl'] ?>);height: 250px;background-repeat: no-repeat;background-size: cover;background-position-y: center;">
                <h1 class="d-flex justify-content-center align-items-center text-center h-100 text-white"><?php echo $filteredPackage['name'] ?></h1>
            </div>
            <div class="row mb-2" style="position:relative">
                <div class="col-6">
                    <img style="width:-webkit-fill-available;" src="<?php echo $filteredPackage['packageImageUrl'] ?>" alt="primary image" >
                    <p style="font-size: large;font-weight: bolder;background-color: greenyellow;position: absolute;top: 0;padding: 6px 25px;border-radius: 18px;">Package Id : <sapn class="text-white"><?php echo $filteredPackage['packageId'] ?></sapn></p>
                </div>
                <div class="col-6">
                    <h1><?php echo $filteredPackage['name'] ?></h1>
                    <p><?php echo $filteredPackage['description'] ?></p>
                    <p>Duration : <?php echo $filteredPackage['duration'] ?> days</p>
                </div>
            </div>
            <div class="mb-2">
                <h2>Departures</h2>
                <form action="" method="post">
                    <table class="table table-hover">
                        <tr>
                            <th class="text-center">Sr/no</th>
                            <th class="text-center">Start date</th>
                            <th class="text-center">Start destination</th>
                            <th class="text-center">End date</th>
                            <th class="text-center">End destination</th>
                            <th class="text-center">Select dates</th>
                        </tr>
                        <?php $val = 1; foreach($filteredPackage['departures'] as $departure): ?>
                            <tr>
                                <td class="text-center"><?php echo $val ?></td>
                                <td class="text-center"><?php echo $departure['startDate'] ?></td>
                                <td class="text-center"><?php echo $departure['startRegion'] ?></td>
                                <td class="text-center"><?php echo $departure['endDate'] ?></td>
                                <td class="text-center"><?php echo $departure['endRegion'] ?></td>
                                <td class="text-center">
                                    <button class="btn btn-primary" type="submit" name="dates" value="<?php echo $departure['departureId']; ?>" id="dates">Select</button>
                                </td>
                            </tr>
                            <?php $val++; ?>
                        <?php endforeach; ?>
                    </table>
                </form>
            </div>
            
            <?php
            if(isset($_POST['dates'])) {
                // Check if departure ID is set in the POST data
                $departure_id = $_POST['dates'];
                
                // Filter departures based on the selected departure_id
                $filteredDeparture = null;
                foreach($filteredPackage['departures'] as $departure) {
                    if($departure['departureId'] == $departure_id) {
                        $filteredDeparture = $departure;
                        break;
                    }
                }
                
                if($filteredDeparture !== null) {
                    // Display cabins associated with the filtered departure
                    ?>
                    <div class="mb-2">
                    <h2>Cabins</h2>
                    <table class="table table-hover">
                        <tr>
                            <th class="text-center">Sr/no</th>
                            <th>Name</th>
                            <th>Service types</th>
                            <th class="text-center">Price</th>
                            <th class="text-center">Rooms left</th>
                            <th>Pax</th>
                            <th class="text-center">Book</th>
                        </tr>
                        <?php $val = 1; foreach($filteredDeparture['cabins'] as $cabin): ?>
                            <tr>
                                <td class="text-center"><?php echo $val ?></td>
                                <td><?php echo $cabin['name'] ?></td>
                                <td><?php echo $cabin['serviceType'] ?></td>
                                <td class="text-center"><?php echo $cabin['startingPrice'] ?></td>
                                <td class="text-center"><?php echo $cabin['roomsLeft'] ?></td>
                                <td><?php echo $cabin['occupancyId']?></td>
                                <td class="text-center">
                                    <input type="text" name="cabinId" value="<?php echo $cabin['elementId']?>" hidden>
                                    <button class="btn btn-primary toggle-details">Add now</button>
                                </td>
                            </tr>
                            <tr class="additional-details" style="display: none;">
                                <td colspan="7">
                                    <?php 
                                    if($cabinId):
                                    ?>
                                    <table class="table table-hover">
                                        <tr>
                                            <th class="text-center">Sr/no</th>
                                            <th class="text-center">Price Name</th>
                                            <th class="text-center">Adult * 1</th>
                                            <th class="text-center">Sole Adult</th>
                                            <th class="text-center">Child</th>
                                            <th class="text-center">Select</th>
                                            <th class="text-center">Quantity</th>
                                        </tr>
                                        <?php $val = 1; foreach($cabin['priceTypes'] as $pt):?>
                                            <tr>
                                                <td class="text-center"><?php echo $val;?></td>
                                                <td class="text-center"><?php echo $pt['priceTypeName'];?></td>
                                                <td class="text-center"><?php echo $pt['adultPrice'];?></td>
                                                <td class="text-center"><?php echo $pt['soleAdultPrice'];?></td>
                                                <td class="text-center"><?php echo $pt['childPrice'];?></td>
                                                <td class="text-center"><input  type="checkbox" name="priceval[]" value="<?php echo $pt['priceTypeId'];?>"></td>
                                                <?php $_SESSION['priceTypeId'] = $pt['priceTypeId'];?>
                                                <?php $_SESSION['elementId'] = $cabinId ;?>
                                                <?php if($cabin['occupancyId']>1){?>
                                                    <td class="text-center"><input
                                                    maxlength="1" max="3" min="1" value="0" required style="width:50px;"type="number" name="quantity" id="quantity"></td>
                                                <?php }else{?>
                                                    <td class="text-center">
                                                        1
                                                    </td>
                                                <?php }?>
                                            </tr>
                                        <?php $val++;?>
                                        <?php endforeach;?>
                                    </table>
                                    <?php endif;?>
                                </td>
                            </tr>
                            <?php $val++; ?>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="7">
                                <a href="book.php" class="text-center btn btn-primary">Add Cart</a>
                            </td>
                        </tr>
                    </table>
                </div>

                    <?php
                } else {
                    echo "Departure not found";
                }
            }            
        } else {
            echo "Package not found";
        }
    } else {
        echo "No packageId provided in the URL";
    }
    ?>
</body>
<Script>

    $(document).ready(function(){
    var cabinId = null;

    $(".toggle-details").click(function(){
        // Find the closest hidden input field within the parent tr
        cabinId = $(this).closest('tr').find('input[name="cabinId"]').val();
        // Log the cabinId to the console
        console.log("Cabin ID:", cabinId);
        // Toggle the visibility of additional details
        $(this).closest('tr').next('.additional-details').toggle();

        // Make an AJAX request to send the cabinId to the server
        $.ajax({
            url: 'view.php', // Replace 'post_cabin_id.php' with your server-side script URL
            type: 'POST',
            data: { cabinId: cabinId },
            success: function(response) {
                // Handle success
                console.log('Cabin ID posted successfully:', response);
            },
            error: function(xhr, status, error) {
                // Handle errors
                console.error('Error posting Cabin ID:', error);
            }
        });
    });
});

// JavaScript to update session variable without form submission
document.querySelectorAll('.quantity-input').forEach(function(input) {
    input.addEventListener('input', function() {
        var totalQuantity = 0;
        // Iterate over all quantity inputs to calculate the total quantity
        document.querySelectorAll('.quantity-input').forEach(function(input) {
            totalQuantity += parseInt(input.value);
        });

        // Sending the total quantity value to a PHP script using AJAX
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'update_session.php'); // Change 'update_session.php' to your PHP script handling session updates
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                console.log('Session variable updated successfully.');
            } else {
                console.log('Error updating session variable.');
            }
        };
        xhr.send('quantity=' + encodeURIComponent(totalQuantity));
    });
});
</Script>
</html>

