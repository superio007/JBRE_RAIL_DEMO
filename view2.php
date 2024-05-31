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
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
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
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
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
    }
    $priceTypeId = isset($_POST['priceTypeId']) ? $_POST['priceTypeId'] : null;
    $dates = [];
    foreach($filteredPackage['departures'] as $departure){
        $dates[] = $departure['departureId'];
    }
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
        <!-- departures start -->
        <div class="mb-2">
            <h2>Departures</h2>
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
                        <tr id="departure_<?php echo $departure['departureId']; ?>"> <!-- Assigning unique ID to each departure -->
                            <td class="text-center"><?php echo $val ?></td>
                            <td class="text-center"><?php echo $departure['startDate'] ?></td>
                            <td class="text-center"><?php echo $departure['startRegion'] ?></td>
                            <td class="text-center"><?php echo $departure['endDate'] ?></td>
                            <td class="text-center"><?php echo $departure['endRegion'] ?></td>
                            <td class="text-center">
                                <button class="btn btn-primary select-departure" data-departure-id="<?php echo $departure['departureId']; ?>">Select</button> <!-- Added data attribute to store departure ID -->
                            </td>
                        </tr>
                        <?php $val++; ?>
                    <?php endforeach; ?>
                </table>
        </div>
        <!-- departures end -->
        <!-- cabins start -->
        <?php foreach($dates as $date): ?>
            <div id="cabins_<?php echo $date;?>" class="mb-2" style="display: none;"> <!-- Assigning unique ID to each cabins div -->
                <?php
                // Find departure for this date
                $filteredDeparture = null;
                foreach($filteredPackage['departures'] as $departure) {
                    if($departure['departureId'] == $date) {
                        $filteredDeparture = $departure;
                        break;
                    }
                }
                ?>
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
                                <input type="text" name="departureId" value="<?php echo $filteredDeparture['departureId']?>" hidden>
                                <button class="btn btn-primary toggle-details">Add now</button>
                            </td>
                        </tr>
                        <tr class="additional-details" style="display: none;">
                            <td colspan="7">
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
												
                                            <td class="text-center">
												<input id="priceType_<?php echo $pt['priceTypeId'];?>"  type="checkbox" name="priceval" value="<?php echo $pt['priceTypeId'];?>">
												<input type="text" name="cabinId" id="cabin_<?php echo $cabin['elementId'];?>" hidden  value="<?php echo $cabin['elementId']?>">
												</td>
											<?php if($cabin['occupancyId']>1){?>
												<td class="text-center">
													<input id = "qantity_<?php echo $pt['priceTypeId']; ?>" class="<?php echo $cabin['elementId'];?>_<?php echo $pt['priceTypeId'];?>" maxlength="1" max="3" min="1" value="0" required style="width:50px;" type="number" name="quantity" id="quantity"></td>
                                            <?php }else{?>
                                                <td class="text-center" name="quantity">
                                                    1
                                                </td>
                                            <?php }?>
                                        </tr>
                                    <?php $val++;?>
                                    <?php endforeach;?>
                                </table>
                            </td>
                        </tr>
                        <?php $val++; ?>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="7">
                            <a href="book.php?packageId=<?php echo $packageId;?>" class="text-center btn btn-primary">Add to Cart</a>
                        </td>
                    </tr>
                </table>
            </div>
        <?php endforeach; ?>
        <!-- cabins end -->
<script>
    var departureId = null;
$(document).ready(function(){
    $(".select-departure").click(function(){
        // Get the departure ID from the clicked button's data attribute
        departureId = $(this).data('departure-id');
        
        // Hide all cabin divs
        $("div[id^='cabins_']").hide();
        
        // Show the cabin div corresponding to the selected departure
        $("#cabins_" + departureId).show();
        
        // Remove the selected class and style from all departure buttons
        $(".select-departure").removeClass("selected");
        $(".select-departure").text("Select");
        
        // Add the selected class and style to the clicked departure button
        $(this).addClass("selected");
        $(this).text("Selected");
    });
});

// $(document).ready(function(){
//     // Click event handler for checkboxes with name 'priceval[]'
//     $('input[name="priceval"]').click(function() {
//         // Check if the checkbox is checked
//         if ($(this).is(':checked')) {
//             // Retrieve the value of the checked checkbox
//             var priceTypeId = $(this).val();
//             // Log the priceTypeId to the console
//             console.log("Price Type ID:", priceTypeId);
    
//             // Log the departureId to the console
//             console.log("Departure Id:", departureId);

//             // Retrieve the CabinId from the hidden input field in the same row
//             var cabinId = $('input[name="cabinId"]').val();
// 			// alert(cabinId);
//             // Log the cabinId to the console
//             console.log("Cabin ID:", cabinId);
            
//             // Retrieve the quantity from the checkbox row
//             var quantity = $(this).closest('tr').find('input[name="quantity"]').val();
//             // If quantity is empty, default it to 1
//             if (!quantity) {
//                 quantity = 1;
//             }
//             // Log the quantity to the console
//             console.log("Quantity:", quantity);
            
//             // Create an object to store the selected data
//             var selectedData = {
//                 departureId : departureId,
//                 priceTypeId: priceTypeId,
//                 cabinId: cabinId,
//                 quantity: quantity
//             };
            
//             // Retrieve the existing selected data array from session storage or create a new one if it doesn't exist
//             var selectedDataArray = JSON.parse(sessionStorage.getItem('selectedDataArray')) || [];
//             // Push the selected data object into the array
//             selectedDataArray.push(selectedData);
//             // Store the updated array in session storage
//             sessionStorage.setItem('selectedDataArray', JSON.stringify(selectedDataArray));
            
//             // Log the selected data array to the console
//             console.log("Selected Data Array:", selectedDataArray);
//         } else {
//             console.log("not added");
//         }
//     });
// });
$(document).ready(function(){
    // Click event handler for checkboxes with name 'priceval[]'
    $('input[name="priceval"]').click(function() {
        // Check if the checkbox is checked
        if ($(this).is(':checked')) {
            // Retrieve the value of the checked checkbox
            var priceTypeId = $(this).val();
            // Log the priceTypeId to the console
            console.log("Price Type ID:", priceTypeId);
            
            // Log the departureId to the console
            console.log("Departure Id:", departureId);

            // Retrieve the CabinId from the hidden input field in the same row
            var cabinId = $(this).closest('tr').find('input[name="cabinId"]').val();
			// alert(cabinId);
            // Log the cabinId to the console
            console.log("Cabin ID:", cabinId);
            
            // Retrieve the quantity from the checkbox row
            var quantity = $(this).closest('tr').find('input[name="quantity"]').val();
            // If quantity is empty, default it to 1
            if (!quantity) {
                quantity = 1;
            }
            // Log the quantity to the console
            console.log("Quantity:", quantity);
            
            // Create an object to store the selected data
            var selectedData = {
                departureId : departureId,
                priceTypeId: priceTypeId,
                cabinId: cabinId,
                quantity: quantity
            };
            
            // Retrieve the existing selected data array from session storage or create a new one if it doesn't exist
            var selectedDataArray = JSON.parse(sessionStorage.getItem('selectedDataArray')) || [];
            // Push the selected data object into the array
            selectedDataArray.push(selectedData);
            // Store the updated array in session storage
            sessionStorage.setItem('selectedDataArray', JSON.stringify(selectedDataArray));
            
            // Log the selected data array to the console
            console.log("Selected Data Array:", selectedDataArray);

            // Now, let's send the selected data to PHP using AJAX
            $.ajax({
                url: 'book.php', // Replace 'your_php_script.php' with the actual path to your PHP script
                type: 'POST',
                data: { selectedDataArray: JSON.stringify(selectedDataArray) }, // Send the selectedDataArray as JSON
                success: function(response) {
                    // Handle the response from the PHP script if needed
                    console.log('Data successfully sent to PHP.');
                },
                error: function(xhr, status, error) {
                    // Handle errors if any
                    console.error('Error occurred while sending data to PHP:', error);
                }
            });
        } else {
            console.log("not added");
        }
    });
});


// dropdown
$(".toggle-details").click(function(){
    // Toggle the visibility of additional details
    $(this).closest('tr').next('.additional-details').toggle();
});

</script>
<style>
.selected {
    background-color: grey;
    color: white;
}
</style>
</body>
</html>