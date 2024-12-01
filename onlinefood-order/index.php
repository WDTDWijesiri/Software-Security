<?php include('partials-front/menu.php'); ?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container text-center">
        <h1>Welcome to Our Food Paradise</h1>
        <p>Delicious food at your fingertips</p>
        <a href="#menu" class="btn btn-primary">Explore Menu</a>
    </div>
</section>

<!-- Search Section -->
<section class="food-search text-center">
    <div class="container">
        <form action="<?php echo SITEURL; ?>food-search.php" method="POST">
            <input type="search" name="search" placeholder="Search Foods" required>
            <input type="submit" name="submit" value="Search" class="btn btn-primary">
        </form>
    </div>
</section>

<!-- Categories Section -->
<section class="categories">
    <div class="container">
        <h2 class="text-center">Explore Categories</h2>
        <div class="category-cards">
            <?php 
            $sql = "SELECT * FROM tbl_category WHERE active='Yes' AND featured='Yes' ORDER BY id DESC LIMIT 3";
            $res = mysqli_query($conn, $sql);
            $count = mysqli_num_rows($res);

            if($count>0) {
                while($row=mysqli_fetch_assoc($res)) {
                    $id = $row['id'];
                    $title = $row['title'];
                    $image_name = $row['image_name'];
                    ?>
                    <div class="category-card">
                        <a href="<?php echo SITEURL; ?>category-foods.php?category_id=<?php echo $id; ?>">
                            <?php if($image_name != "") { ?>
                                <img src="<?php echo SITEURL; ?>images/category/<?php echo $image_name; ?>" alt="<?php echo $title; ?>" class="img-responsive img-curve">
                            <?php } else { ?>
                                <div class="error">Image not Available</div>
                            <?php } ?>
                            <h3><?php echo $title; ?></h3>
                        </a>
                    </div>
                    <?php
                }
            } else {
                echo "<div class='error'>Category not Available</div>";
            }
            ?>
        </div>
    </div>
</section>

<!-- Food Menu Section -->
<section class="food-menu" id="menu">
    <div class="container">
        <h2 class="text-center">Our Food Menu</h2>
        <div class="menu-items">
            <?php 
            $sql2 = "SELECT * FROM tbl_food WHERE active='Yes' AND featured='Yes' LIMIT 6";
            $res2 = mysqli_query($conn, $sql2);
            $count2 = mysqli_num_rows($res2);

            if($count2>0) {
                while($row=mysqli_fetch_assoc($res2)) {
                    $id = $row['id'];
                    $title = $row['title'];
                    $price = $row['price'];
                    $description = $row['description'];
                    $image_name = $row['image_name'];
                    ?>
                    <div class="menu-card">
                        <?php if($image_name != "") { ?>
                            <img src="<?php echo SITEURL; ?>images/food/<?php echo $image_name; ?>" alt="<?php echo $title; ?>" class="img-responsive img-curve">
                        <?php } else { ?>
                            <div class="error">Image not Available</div>
                        <?php } ?>
                        <h3><?php echo $title; ?></h3>
                        <p>Rs <?php echo $price*100; ?></p>
                        <p><?php echo $description; ?></p>
                        <a href="<?php echo SITEURL; ?>order.php?food_id=<?php echo $id; ?>" class="btn btn-secondary">Order Now</a>
                    </div>
                    <?php
                }
            } else {
                echo "<div class='error'>Food not available.</div>";
            }
            ?>
        </div>
    </div>
</section>

<?php include('partials-front/footer.php'); ?>
