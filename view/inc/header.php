<!-- Header start  -->
<header class="ec-header">
    <!--Ec Header Top Start -->
    <div class="header-top">
        <div class="container">
            <div class="row align-items-center">
                <!-- Header Top social Start -->
                <div class="col text-left header-top-left d-none d-lg-block">
                    <div class="header-top-social">
                        <span class="social-text text-upper">Follow us on:</span>
                        <ul class="mb-0">
                            <li class="list-inline-item"><a class="hdr-facebook" href="#"><i class="ecicon eci-facebook"></i></a></li>
                            <li class="list-inline-item"><a class="hdr-twitter" href="#"><i class="ecicon eci-twitter"></i></a></li>
                            <li class="list-inline-item"><a class="hdr-instagram" href="#"><i class="ecicon eci-instagram"></i></a></li>
                            <li class="list-inline-item"><a class="hdr-linkedin" href="#"><i class="ecicon eci-linkedin"></i></a></li>
                        </ul>
                    </div>
                </div>
                <!-- Header Top social End -->
                <!-- Header Top Category Toggle Start -->
                <a href="#ec-mobile-sidebar" class="ec-header-btn ec-sidebar-toggle d-lg-none">
                    <i class="fi fi-rr-apps"></i>
                </a>
                <!-- Header Top Category Toggle End -->
                <!-- Header Top Message Start -->
                <div class="col text-center header-top-center">
                    <div class="header-top-message text-upper">
                        <span>Free Shipping</span>for Single Order above - £50
                    </div>
                </div>
                <!-- Header Top Message End -->

                <!-- Header Top responsive Action -->
                <div class="col d-lg-none ">
                    <div class="ec-header-bottons">
                        <!-- Header User Start -->
                        <div class="ec-header-user dropdown">
                            <button class="dropdown-toggle" data-bs-toggle="dropdown"><i class="fi-rr-user"></i></button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <?php if (!empty($_SESSION['user_id'])): ?>
                                    <li><a href="#"
                                            class="dropdown-item"
                                            data-bs-toggle="modal" data-bs-target="#logoutModal">
                                            Logout
                                        </a></li>
                                    <li><a class="dropdown-item" href="checkout.php">Checkout</a></li>
                                <?php else: ?>
                                    <li><a class="dropdown-item" href="register.php">Register</a></li>
                                    <li><a class="dropdown-item" href="login.php">Login</a></li>
                                    <li><a class="dropdown-item" href="checkout.php">Checkout</a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <!-- Header User End -->

                        <!-- Header Cart Start -->
                        <a href="#ec-side-cart" class="ec-header-btn ec-side-toggle">
                            <div class="header-icon"><i class="fi-rr-shopping-basket"></i></div>
                            <span class="ec-header-count ec-cart-count cart-count-lable">3</span>
                        </a>
                        <!-- Header Cart End -->
                        <!-- Header menu Start -->
                        <a href="#ec-mobile-menu" class="ec-header-btn ec-side-toggle d-lg-none">
                            <i class="fi-rr-menu-burger"></i>
                        </a>
                        <!-- Header menu End -->
                    </div>
                </div>
                <!-- Header Top responsive Action -->
            </div>
        </div>
    </div>
    <!-- Ec Header Top  End -->
    <!-- Ec Header Bottom  Start -->
    <div class="ec-header-bottom d-none d-lg-block">
        <div class="container position-relative">
            <div class="row">
                <div class="ec-flex">
                    <!-- Ec Header Logo Start -->
                    <div class="align-self-center">
                        <div class="header-logo" style="background-color: #FFFF;">
                            <a href="index.html"><img src="assets/images/logo/nlogo.png" alt="Site Logo" /><img
                                    class="dark-logo" src="assets/images/logo/nlogo.png" alt="Site Logo"
                                    style="display: none;" /></a>
                        </div>
                    </div>
                    <!-- Ec Header Logo End -->

                    <!-- Ec Header Search Start -->
                    <div class="align-self-center">
                        <div class="header-search">
                            <form class="ec-btn-group-form" action="search.php" method="get">
                                <input class="form-control" name="q" placeholder="Enter Your Product Name..." type="text">
                                <button class="submit" type="submit"><i class="fi-rr-search"></i></button>
                            </form>
                        </div>
                    </div>
                    <!-- Ec Header Search End -->

                    <!-- Ec Header Button Start -->
                    <div class="align-self-center">
                        <div class="ec-header-bottons">

                            <!-- Header User Start -->
                            <div class="ec-header-user dropdown">
                                <button class="dropdown-toggle" data-bs-toggle="dropdown"><i class="fi-rr-user"></i></button>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <?php if (!empty($_SESSION['user_id'])): ?>
                                        <li><a href="#"
                                                class="dropdown-item"
                                                data-bs-toggle="modal" data-bs-target="#logoutModal">
                                                Logout
                                            </a></li>
                                        <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                                        <li><a class="dropdown-item" href="order.php">Order History</a></li>
                                        <li><a class="dropdown-item" href="checkout.php">Checkout</a></li>
                                    <?php else: ?>
                                        <li><a class="dropdown-item" href="register.php">Register</a></li>
                                        <li><a class="dropdown-item" href="login.php">Login</a></li>
                                        <li><a class="dropdown-item" href="checkout.php">Checkout</a></li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                            <!-- Header User End -->
                            <!-- Header Cart Start -->
                            <a href="#ec-side-cart" class="ec-header-btn ec-side-toggle">
                                <div class="header-icon"><i class="fi-rr-shopping-basket"></i></div>
                                <span id="cart-count" class="ec-header-count ec-cart-count cart-count-lable">0</span>
                            </a>

                            <!-- Header Cart End -->
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- Ec Header Button End -->
    <!-- Header responsive Bottom  Start -->
    <div class="ec-header-bottom d-lg-none">
        <div class="container position-relative">
            <div class="row ">

                <!-- Ec Header Logo Start -->
                <div class="col">
                    <div class="header-logo">
                        <a href="index.html"><img src="assets/images/logo/nlogo.png" alt="Site Logo" /><img
                                class="dark-logo" src="assets/images/logo/nlogo.png" alt="Site Logo"
                                style="display: none;" /></a>
                    </div>
                </div>
                <!-- Ec Header Logo End -->
                <!-- Ec Header Search Start -->
                <div class="col">
                    <div class="header-search">
                        <form class="ec-btn-group-form" action="#">
                            <input class="form-control" placeholder="Enter Your Product Name..." type="text">
                            <button class="submit" type="submit"><i class="fi-rr-search"></i></button>
                        </form>
                    </div>
                </div>
                <!-- Ec Header Search End -->
            </div>
        </div>
    </div>
    <!-- Header responsive Bottom  End -->
    <!-- EC Main Menu Start -->
    <div id="ec-main-menu-desk" class="d-none d-lg-block sticky-nav">
        <div class="container position-relative">
            <div class="row">
                <div class="col-md-12 align-self-center" style="background-color: #8B008B;">
                    <div class="ec-main-menu" style="background-color: #8B008B;">
                        <!-- Sidebar toggle button for mobile -->
                        <a href="#ec-mobile-sidebar" class="ec-header-btn ec-sidebar-toggle" style="color: white;">
                            <i class="fi fi-rr-apps"></i>
                        </a>

                        <ul style="color: white;">
                            <li><a href="index.php" style="color: white;">HOME</a></li>
                            <li class="dropdown"><a href="javascript:void(0)" style="color: white;">Fashion Store</a>
                                <ul class="sub-menu">
                                    <?php
                                    $africanStore = $model->getRows("categories", [
                                        "where" => ["section_id" => 2]
                                    ]);
                                    if ($africanStore) {
                                        foreach ($africanStore as $link) {
                                    ?>
                                            <li><a href="viewcategory.php?id=<?= $link['categoryTbl_id'] ?>"><?= $link['category_name'] ?></a></li>
                                    <?php
                                        }
                                    }
                                    ?>
                                </ul>
                            </li>
                            <li class="dropdown"><a href="javascript:void(0)" style="color: white;">Beauty Store</a>
                                <ul class="sub-menu">
                                    <?php
                                    $africanStore = $model->getRows("categories", [
                                        "where" => ["section_id" => 3]
                                    ]);
                                    if ($africanStore) {
                                        foreach ($africanStore as $link) {
                                    ?>
                                            <li><a href="viewcategory.php?id=<?= $link['categoryTbl_id'] ?>"><?= $link['category_name'] ?></a></li>
                                    <?php
                                        }
                                    }
                                    ?>
                                </ul>
                            </li>
                            <li class="dropdown"><a href="javascript:void(0)" style="color: white;">African Store</a>
                                <ul class="sub-menu">
                                    <?php
                                    $africanStore = $model->getRows("categories", [
                                        "where" => ["section_id" => 4]
                                    ]);
                                    if ($africanStore) {
                                        foreach ($africanStore as $link) {
                                    ?>
                                            <li><a href="viewcategory.php?id=<?= $link['categoryTbl_id'] ?>"><?= $link['category_name'] ?></a></li>
                                    <?php
                                        }
                                    }
                                    ?>
                                </ul>
                            </li>
                            <li class="dropdown"><a href="javascript:void(0)" style="color: white;">Salon Services</a>
                                <ul class="sub-menu">
                                    <?php
                                    $africanStore = $model->getRows("categories", [
                                        "where" => ["section_id" => 1]
                                    ]);
                                    if ($africanStore) {
                                        foreach ($africanStore as $link) {
                                    ?>
                                            <li><a href="viewcategory.php?id=<?= $link['categoryTbl_id'] ?>"><?= $link['category_name'] ?></a></li>
                                    <?php
                                        }
                                    }
                                    ?>
                                </ul>
                            </li>
                            <li><a href="shop.php" style="color: white;">SHOP</a></li>
                            <li><a href="#" style="color: white;">CONTACT</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ec Main Menu End -->
    <!-- ekka Mobile Menu Start -->
    <!-- ec Mobile Menu Start -->
    <div id="ec-mobile-menu" class="ec-side-cart ec-mobile-menu">
        <div class="ec-menu-title">
            <span class="menu_title">Menu</span>
            <button class="ec-close">×</button>
        </div>
        <div class="ec-menu-inner">
            <div class="ec-menu-content">

                <ul>
                    <li><a href="index.php">HOME</a></li>
                    <li class="dropdown"><a href="javascript:void(0)">Fashion Store</a>
                        <ul class="sub-menu">
                            <?php
                            $africanStore = $model->getRows('categories', [
                                'where' => ['section_id' => 2]
                            ]);
                            if ($africanStore) {
                                foreach ($africanStore as $link) {
                            ?>
                                    <li><a href="viewcategory.php?id=<?= $link['categoryTbl_id'] ?>"><?= $link['category_name'] ?></a></li>
                            <?php
                                }
                            }
                            ?>
                        </ul>
                    </li>
                    <li class="dropdown"><a href="javascript:void(0)">Beauty Store</a>
                        <ul class="sub-menu">
                            <?php
                            $africanStore = $model->getRows('categories', [
                                'where' => ['section_id' => 3]
                            ]);
                            if ($africanStore) {
                                foreach ($africanStore as $link) {
                            ?>
                                    <li><a href="viewcategory.php?id=<?= $link['categoryTbl_id'] ?>"><?= $link['category_name'] ?></a></li>
                            <?php
                                }
                            }
                            ?>
                        </ul>
                    </li>
                    <li class="dropdown"><a href="javascript:void(0)">African Store</a>
                        <ul class="sub-menu">
                            <?php
                            $africanStore = $model->getRows('categories', [
                                'where' => ['section_id' => 4]
                            ]);
                            if ($africanStore) {
                                foreach ($africanStore as $link) {
                            ?>
                                    <li><a href="viewcategory.php?id=<?= $link['categoryTbl_id'] ?>"><?= $link['category_name'] ?></a></li>
                            <?php
                                }
                            }
                            ?>
                        </ul>
                    </li>
                    <li class="dropdown"><a href="javascript:void(0)">Salon Services</a>
                        <ul class="sub-menu">
                            <?php
                            $africanStore = $model->getRows('categories', [
                                'where' => ['section_id' => 1]
                            ]);
                            if ($africanStore) {
                                foreach ($africanStore as $link) {
                            ?>
                                    <li><a href="viewcategory.php?id=<?= $link['categoryTbl_id'] ?>"><?= $link['category_name'] ?></a></li>
                            <?php
                                }
                            }
                            ?>
                        </ul>
                    </li>
                    <li><a href="shop.php">SHOP</a></li>
                    <li><a href="#">CONTACT</a></li>

                </ul>
            </div>

            <!-- Optional: Language & Currency -->
            <div class="header-res-lan-curr">
                <!-- Social Links -->
                <div class="header-res-social">
                    <div class="header-top-social">
                        <ul class="mb-0">
                            <li class="list-inline-item"><a class="hdr-facebook" href="#"><i class="ecicon eci-facebook"></i></a></li>
                            <li class="list-inline-item"><a class="hdr-twitter" href="#"><i class="ecicon eci-twitter"></i></a></li>
                            <li class="list-inline-item"><a class="hdr-instagram" href="#"><i class="ecicon eci-instagram"></i></a></li>
                            <li class="list-inline-item"><a class="hdr-linkedin" href="#"><i class="ecicon eci-linkedin"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ec Mobile Menu End -->

    <!-- ekka mobile Menu End -->
</header>
<!-- Header End  -->