<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Form</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script src="validation.js" defer></script> <!-- Link to the JavaScript validation file -->
    <style>
        .error {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<?php include('partials-front/menu.php'); ?>

<?php 
    // Initialize variables to retain user input
    $fullName = '';
    $contact = '';
    $email = '';
    $address = '';
    $nameError = '';
    $contactError = '';
    $emailError = '';
    $addressError = '';

    // Check whether food id is set or not
    if (isset($_GET['food_id'])) {
        // Get the Food id and details of the selected food
        $food_id = $_GET['food_id'];
        // Get the Details of the Selected Food
        $sql = "SELECT * FROM tbl_food WHERE id=$food_id";
        // Execute the Query
        $res = mysqli_query($conn, $sql);
        // Count the rows
        $count = mysqli_num_rows($res);
        // Check whether the data is available or not
        if ($count == 1) {
            // We Have Data
            $row = mysqli_fetch_assoc($res);
            $title = $row['title'];
            $price = $row['price'];
            $image_name = $row['image_name'];
        } else {
            // Food not Available, Redirect to Home Page
            header('location:' . SITEURL);
            exit; // Exit to stop further execution
        }
    } else {
        // Redirect to homepage
        header('location:' . SITEURL);
        exit; // Exit to stop further execution
    }

    if (isset($_POST['submit'])) {
        // Verify the reCAPTCHA response
        $secret_key = '6LeZPI4qAAAAAOO3DzA5z4dDQZr1NE-N2XlIQbG9';
        $response = $_POST['g-recaptcha-response'];
        $verify_response = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $response);
        $response_data = json_decode($verify_response);
        
        // Check if the reCAPTCHA validation is successful
        if ($response_data->success) {
            // Retrieve and sanitize form inputs
            $fullName = htmlspecialchars(trim($_POST['full-name']));
            $contact = htmlspecialchars(trim($_POST['contact']));
            $email = htmlspecialchars(trim($_POST['email']));
            $address = htmlspecialchars(trim($_POST['address']));
            $food = $_POST['food'];
            $price = $_POST['price'];
            $qty = $_POST['qty'];
            $total = $price * $qty;
            $order_date = date("Y-m-d H:i:s");
            $status = "Ordered";

            // Validate inputs
            if (!preg_match("/^[a-zA-Z\s]+$/", $fullName)) {
                $nameError = 'Name can only contain letters and spaces.';
            } elseif (strlen($fullName) > 50) {
                $nameError = 'Name is too long.';
            }

            if (empty($contact)) {
                $contactError = 'Phone number is required.';
            } elseif (!preg_match("/^[0-9]{10}$/", $contact)) {
                $contactError = 'Please enter a valid phone number.';
            }

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emailError = 'Please enter a valid email address.';
            } else {
                $domain = substr(strrchr($email, "@"), 1); // Get domain part
                if (!checkdnsrr($domain, "MX")) {
                    $emailError = "Invalid email domain.";
                }
            }

            if (empty($address)) {
                $addressError = 'Address is required.';
            }

            // Check for any validation errors
            if (!empty($nameError) || !empty($contactError) || !empty($emailError) || !empty($addressError)) {
                // Show an alert and retain other field values
                $errorMessage = implode("\n", array_filter([$nameError, $contactError, $emailError, $addressError]));
                echo "<script>alert('$errorMessage');</script>";
            } else {
                // Save the order in the database
                $sql2 = "INSERT INTO tbl_order SET 
                    food = '$food',
                    price = $price,
                    qty = $qty,
                    total = $total,
                    order_date = '$order_date',
                    status = '$status',
                    customer_name = '$fullName',
                    customer_contact = '$contact',
                    customer_email = '$email',
                    customer_address = '$address'";

                $res2 = mysqli_query($conn, $sql2);

                if ($res2) {
                    // Alert and redirect on successful order
                    echo "<script>alert('Food Ordered Successfully.'); window.location.href='" . SITEURL . "';</script>";
                    exit; // Exit to stop further execution
                } else {
                    $_SESSION['order'] = "<div class='error text-center'>Failed to Order Food: " . mysqli_error($conn) . "</div>";
                    header('location:' . SITEURL);
                    exit; // Exit to stop further execution
                }
            }
        } else {
            $_SESSION['order'] = "<div class='error text-center'>reCAPTCHA verification failed. Please try again.</div>";
            header('location:' . SITEURL);
            exit; // Exit to stop further execution
        }
    }
?>

<!-- Food Search Section Starts Here -->
<section class="food-search2">
    <div class="container">
        <h2 class="text-center text-white">Fill this form to confirm your order.</h2>
        <form action="" method="POST" class="order">
            <fieldset>
                <legend>Selected Food</legend>
                <div class="food-menu-img">
                    <?php 
                        if ($image_name == "") {
                            echo "<div class='error'>Image not Available.</div>";
                        } else {
                            ?>
                            <img src="<?php echo SITEURL; ?>images/food/<?php echo $image_name; ?>" alt="Food Image" class="img-responsive img-curve">
                            <?php
                        }
                    ?>
                </div>
                <div class="food-menu-desc">
                    <h3><?php echo $title; ?></h3>
                    <input type="hidden" name="food" value="<?php echo $title; ?>">
                    <p class="food-price">Rs<?php echo $price * 100; ?></p>
                    <input type="hidden" name="price" value="<?php echo $price; ?>">
                    <div class="order-label">Quantity</div>
                    <input type="number" name="qty" class="input-responsive" value="1" required>
                </div>
            </fieldset>
            <fieldset>
                <legend>Delivery Details</legend>
                <div class="order-label">Full Name</div>
                <input type="text" name="full-name" placeholder="E.g. William Moore" class="input-responsive" required value="<?php echo $fullName; ?>">
                <div class="error error-name"><?php echo $nameError; ?></div> <!-- Display name error message -->
                <div class="order-label">Phone Number</div>
                <input type="tel" name="contact" placeholder="E.g. 7410000000" class="input-responsive" required value="<?php echo $contact; ?>">
                <div class="error error-contact"><?php echo $contactError; ?></div> <!-- Display contact error message -->
                
                <div class="order-label">Email</div>
                <input type="email" name="email" placeholder="E.g. william@codeastro.com" class="input-responsive" required value="<?php echo $email; ?>">
                <div class="error error-email"><?php echo $emailError; ?></div> <!-- Display email error message -->

                <div class="order-label">Address</div>
                <textarea name="address" rows="10" placeholder="E.g. Street, City, Country" class="input-responsive" required><?php echo $address; ?></textarea>
                <div class="error error-address"><?php echo $addressError; ?></div> <!-- Display address error message -->

                <!-- Add the reCAPTCHA v2 widget here -->
                <div class="g-recaptcha" data-sitekey="6LeZPI4qAAAAAKXlMYh3pWFrvVDJ8CDG6mu5RQcZ"></div>

                <input type="submit" name="submit" value="Confirm Order" class="btn btn-primary">
            </fieldset>
        </form>
    </div>
</section>
<!-- Food Search Section Ends Here -->

<?php include('partials-front/footer.php'); ?>

</body>
</html>
