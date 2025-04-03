    <!-- 
	 
	
	
	-->



<?php
include 'db_connection.php'; // Ensure database connection is established


try {
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    $query = "SELECT * FROM products";
    $result = $conn->query($query);
    $products = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }

    $conn->close();
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>The Pastry Hub</title>
    <link rel="stylesheet" href="./styles_product.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>

    <!-- HEADER LOGO, SEARCH BAR, ACCOUNT-->
    <header>
		<div class="head"><p>The one and only Pastry Hub </p></div>
        <div id="header">
            <div class="container">
                <div class="header-flex">

                    <!-- LOGO -->
                    <div class="header-logo">
                        <a href="#" class="logo">
                            <img src="img\logo.jpg" alt="Logo" style="width: 250px; height: auto;">
							<p><b>Hello, <span id="userName">Guest</span>!</b></p>
                        </a>
                    </div>
                    <!-- /LOGO -->

                    <!-- SEARCH BAR -->
                    <div class="header-search">
                        <form>
                            <select class="input-select"></select>
                            <input class="input" placeholder="What are you looking for?">
                            <button class="search-btn" id = search-btn>Search</button>
                        </form>
                    </div>
                    <!-- /SEARCH BAR -->

                    <!-- ACCOUNT -->
                    <div class="header-ctn">
                       
                        <div class="dropdown">
                            <a  href="./cart.html"class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                <i class="fa fa-shopping-cart"></i>
                                <span>Cart</span>
                            </a>
                        </div>

                        <div class="menu-toggle">
                            <a href="#" class="account-dropdown-toggle">
                                <i class="fa fa-bars"></i>
                                <span>Account</span>
                            </a>
                            <ul class="account-dropdown">
							<li>
							<li><a href="order_check.php">Orders</a></li>

								<li><a href="./dashboard.php" target="_blank">Admin dashboard</a></li>
                                <li><a href="./login.html" >Log In</a></li>
                            </ul>
                        </div>
                        <div>
                            <a href="./signup.html">
                                <i class="fa fa-user-o"></i>
                                <span>Signup</span>
                            </a>
                        </div>
                        <div>
  <!-- /maybe change to the index page
    -->

                            <a href="./login.html"  id="logoutLink">
                                <i class="fa fa-sign-out"></i>
                                <span>Logout</span>
                            </a>
                        </div>
                    </div>
                    <!-- /ACCOUNT -->

                </div>
                <!-- /header-flex -->
            </div>
            <!-- /container -->
        </div>
    </header>
    <!-- /HEADER -->

    <!-- NAVIGATION -->
    <nav id="navigation">
        <div class="container">
            <div id="responsive-nav">
                <ul class="main-nav nav navbar-nav">   
                </ul>
            </div>
        </div>
    </nav>
	<!-- NAVIGATION -->

	<!-- Products -->
	<div class="product-container" id="product-container"></div>
	<!-- Products -->
	
	<!-- footer -->
	<footer class="footer">
		<div class="container">
			<div class="row footer-flex"> 
				<div class="col-md-4">
					<h4 class="footer-title">About Us</h4>
					<p>Welcome to The Pastry Hub, where every child is encouraged to eat and blossom in their own unique way.
						
					</div>
				<div class="col-md-4">
					<h4 class="footer-title">Quick Links</h4>
					<ul class="footer-links">

						<li><a href="./about.html">About</a></li>
						<li><a href="#" onclick="openGmail()">Contact</a></li>
					</ul>
				</div>
				<div class="col-md-4">
					<h4 class="footer-title">Contact Us</h4>
					<p>Email: ThePastryHubja@gmail.com</p>
					<p>Phone: +1 (876) 000-0000</p>
					<div class="footer-social">
						<a href="#"><i class="fa fa-facebook-f"></i></a>
						<a href="#"><i class="fa fa-twitter"></i></a>
						<a href="#"><i class="fa fa-instagram"></i></a>
					</div>
				</div>
			</div>
		</div>
	</footer>
	<!-- footer -->

	<script >
		function store()
		{
		// Always update localStorage with the latest products from the database
		const products = <?php echo json_encode($products); ?>;
        localStorage.setItem("AllProducts", JSON.stringify(products));
		
		
	
		}
					
			// Ensure products are stored first
			store();  

			// Retrieve AllProducts safely
			let allProducts = JSON.parse(localStorage.getItem('AllProducts')) || [];

			// Extract categories safely
			const categories = allProducts.length > 0 
				? [...new Set(allProducts.map(product => product.category))] 
				: []; // Ensure it's not null
			//initialize cart
					let cart = JSON.parse(localStorage.getItem('cart')) || [];
				
			function capitalize(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }
        
		function openGmail() 
		{
			const currentUser = JSON.parse(localStorage.getItem('currentUser')) || {};

			// Set default values if user is not found
			const fullName = currentUser.first_name && currentUser.last_name
			? `${capitalize(currentUser.first_name)} ${capitalize(currentUser.last_name)}`
			: "Guest";

			const email = currentUser.email || "No email provided";
			const trn = currentUser.trn || "No TRN available";

			// Construct email body with user details
			const emailBody = `Hello, \n\nThis is a message from ${fullName}.\nEmail: ${email}\nTRN: ${trn}`;

			// Open mail client
			const mailtoLink= `mailto:ThePastryHub@gmail.com?subject=${encodeURIComponent(fullName)}&body=${encodeURIComponent(emailBody)}`;
			window.open(mailtoLink, '_blank');  
}

		//addToCart
	// Retrieve cart from localStorage
	function getCart() {
				return JSON.parse(localStorage.getItem('cart')) || [];
			}

			// Save cart to localStorage
			function saveCart(cart) {
				localStorage.setItem('cart', JSON.stringify(cart));
			}

			function getProductById(productId) {
				const allProducts = JSON.parse(localStorage.getItem('AllProducts')) || [];
				console.log("Searching for product with ID:", productId);
				console.log("Available Products:", allProducts);

				return allProducts.find(item => String(item.id) === String(productId));
			}

		// Add product to cart
		function addToCart(productId)
		 {
				const product = getProductById(productId);
				if (!product) {
					alert('Product not found.');
					return;
				}


				const quantityInput = document.querySelector(`#qty${productId}`);
				const productQuantity = parseInt(quantityInput.value);

				if (!productQuantity || productQuantity <= 0) {
					alert('Please enter a valid quantity.');
					return;
				}

				let cart = getCart();

				const existingItem = cart.find(item => item.id === productId);
				if (existingItem) {
					existingItem.quantity += productQuantity;
				} else {
					cart.push({ ...product, quantity: productQuantity });
				}

				saveCart(cart);
				alert('Item added to cart successfully!');
				//window.location.href = './cart.html';
}


	
		//Navigation Menu
		function populateNavigationMenu() {
			const navMenu = document.querySelector('.main-nav');
			navMenu.innerHTML = '';
			//defalt nav
			navMenu.innerHTML += `
				<li><a href="./product.php">Categories</a></li>
			`;
			// Adds category
			categories.forEach(category => {
				const listItem = document.createElement('li');
				listItem.innerHTML = `<a href="#" onclick="filterByCategory('${category}')">${category}</a>`;
				navMenu.appendChild(listItem);
			});
		}

		//filter products
		function filterByCategory(category) {
			const allProducts = JSON.parse(localStorage.getItem('AllProducts'));
			const filteredProducts = allProducts.filter(product => product.category === category);
			renderProducts(filteredProducts);
		}

		//render products
		function renderProducts(products) {
			const productContainer = document.getElementById('product-container');
			productContainer.innerHTML = '';

			// generates HTML
			products.forEach(product => {
				const productHTML = `
					<div class="product-card">
						<img src="${product.image}" alt="${product.description}">
						<h3>${product.name}</h3>
						<p>Price: $${product.price.toLocaleString()} <del class="product-old-price">$${product.old_price.toLocaleString()}</del></p>
						<input type="number" value="0" min="0" id="qty${product.id}" placeholder="Qty">
						<button type="button" class="add-to-cart" onclick="addToCart(${product.id})">Add to Cart</button>
						<a href="checkout.html"><button id="checkout">Check Out</button></a>
					</div>
				`;
				productContainer.innerHTML += productHTML;
			});
		}

		//page load all products
		document.addEventListener('DOMContentLoaded', function() {
			const allProducts = JSON.parse(localStorage.getItem('AllProducts'));
			renderProducts(allProducts);  // default
			populateNavigationMenu(); 
		});

		//search dropdown
		function populateSearchDropdown() {
			const dropdown = document.querySelector('.header-search .input-select');
			dropdown.innerHTML = `<option value="0">All Categories</option>`; // Default

			// Adds categories
			categories.forEach(category => {
				const option = document.createElement('option');
				option.value = category;
				option.textContent = category;
				dropdown.appendChild(option);
			});
		}
		document.addEventListener('DOMContentLoaded', populateSearchDropdown);


		function getCurrentUser() {
            const currentUser = JSON.parse(localStorage.getItem('currentUser'));

            if (currentUser) {
                // Capitalize the first letter of each part of the name
                const capitalize = (str) => str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
                const fullName = `${capitalize(currentUser.first_name)} ${capitalize(currentUser.last_name)}`;
                document.getElementById('userName').textContent = fullName; // Update the span with the user's name
            } else {
                document.getElementById('userName').textContent = 'Guest'; // Default to 'Guest' if no user is found
				console.log("error in current user");
            }

			//localStorage.removeItem(currentUser.trn);
			//localStorage.removeItem('All Invoices');
			//localStorage.removeItem('calculation');




        }


	// Search products and toggle to the results
function searchProducts(event) {
    event.preventDefault();

    const category = document.querySelector('.header-search .input-select').value;
    const query = document.querySelector('.header-search .input').value.toLowerCase().trim();

    const allProducts = JSON.parse(localStorage.getItem('AllProducts'));

    // Automatically display all products if the search field is empty
    if (query === "") {
        renderProducts(allProducts); // Show all products
        return;
    }

    // Filter products based on the search query and category
    const filteredProducts = allProducts.filter(product => {
        const matchesCategory = category === "0" || product.category === category;
        const matchesQuery = product.name.toLowerCase().includes(query) || product.description.toLowerCase().includes(query);
        return matchesCategory && matchesQuery;
    });

    // Render filtered products or show all if no results found
    if (filteredProducts.length > 0) {
        renderProducts(filteredProducts);
    } else {
        alert('No products found matching your search. Showing all products.');
        renderProducts(allProducts);
    }
}

// Auto-display all products when the search field is empty
document.querySelector('.header-search .input').addEventListener('input', () => {
    const query = document.querySelector('.header-search .input').value.toLowerCase().trim();
    const allProducts = JSON.parse(localStorage.getItem('AllProducts'));

    if (query === "") {
        renderProducts(allProducts); // Show all products
    }
});
        // Call the function when the page loads

		
// Attach search event to the button
	document.querySelector('.header-search form').addEventListener('submit', searchProducts);


			document.getElementById('logoutLink').addEventListener('click', () => {
		if (confirm('Are you sure? Your cart will be cleared.')) {
			cart = [];
			localStorage.removeItem('cart');
			updateCartDisplay();
		}
		else{
			event.preventDefault();
		}
	});



	document.addEventListener('DOMContentLoaded', function() {
    store(); // Store products in localStorage
    const allProducts = JSON.parse(localStorage.getItem('AllProducts')) || [];
    renderProducts(allProducts); // Render only after storage
    populateNavigationMenu();
	getCurrentUser();
});


	</script>
		
</body>
</html>