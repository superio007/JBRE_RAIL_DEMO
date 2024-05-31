<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>JBRE - API</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
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

    
    // // Check if token needs refreshing
    // if ($data === null) {
    //     // Refresh token
    //     $token = getToken();
    // }

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
    if ($data === null) {
        // Output the actual response for debugging
        $token = getToken();
        echo "Error: Kindly check the authentication token";
        exit;
    }

    // Filter data based on search parameters
    // var_dump($data);
    $filteredData = $data['packageResponses'] ;

    // var_dump($filteredData);

    if(isset($_GET['search']) && $_GET['search'] != '' || isset($_GET['options']) && $_GET['options'] != ''){
        $name = isset($_GET['search']) ? $_GET['search'] : '';
        $option = isset($_GET['options']) ? $_GET['options'] : '';
        $filteredData = array_filter($filteredData, function($item) use ($name, $option){
            $searchCondition = empty($name) || (stripos($item['name'], $name) !== false);
            $optionCondition = empty($option) || ($item['type'] === $option);
            return $searchCondition && $optionCondition;
        });
    }

    // var_dump(getToken());
    ?>

    <div>
        <h1 class="text-center my-3">
            Welcome to <span class="text-primary">Australian's</span> Rail
        </h1>
        <div class="mb-3">
            <form action="" method="get">
                <table class="table" style="display: flex; justify-content: center;">
                    <tr>
                        <td class="" style="border:none">
                            <label for="search" style="color: black; font-weight: 600">Enter Trip Name :</label><br>
                            <input id="search" type="text" value="<?=isset($_GET['search'])==true ? $_GET['search'] : ''?>" name="search" placeholder="Enter Trip Name" />
                        </td>
                        <td style="border:none;">
                            <label for="select" style="color: black; font-weight: 600">Select Trip Code :</label><br>
                            <select id="select" name="options"  style="height: 30px">
                                <option value="none" selected disabled hidden>Select from dropdown</option>
                                <option value="GHROW">GHROW</option>
                                <option value="ROW">ROW</option>
                                <option value="GHAN">GHAN</option>
                            </select> 
                        </td>
                    </tr>
                    <tr>
                        <td colspan="9" style="text-align: center; border:none;">
                            <button style="font-size: 18px;width: 30%;" type="submit" class="btn btn-primary">Search</button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
        <div>
            <form action="" method="get">
                <table class="table table-hover">
                    <tr>
                        <th>Sr/no</th>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Duration</th>
                        <th>Details</th>
                    </tr>
                    <?php $val=1; foreach($filteredData as $item):?>
                        <tr>
                            <td class="text-center"><?php echo $val; ?></td>
                            <td><?php echo $item['name']; ?></td>
                            <td class="text-center"><?php echo $item['code']; ?></td>
                            <td class="text-center"><?php echo $item['duration']; ?></td>
                            <td class="text-center"><a href="view2.php?packageId=<?php echo $item['packageId']; ?>" class="btn btn-primary">View details</a></td>
                        </tr>
                    <?php $val++; ?>
                    <?php endforeach; ?>
                </table>
            </form>
        </div>
    </div>
</body>
</html>
<style>
  th {
    text-align: center;
  }
</style>
