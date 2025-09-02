document.addEventListener("DOMContentLoaded", function () {
    const path = window.location.pathname.split("/").pop(); // get the file name only

    let offset = 0;
    const limit = 8;
    let loading = false;
    let finished = false;

    function loadProducts(url, containerId) {
        if (loading || finished) return;
        loading = true;
        document.getElementById("loader").style.display = "block";

        fetch(`${url}?offset=${offset}&limit=${limit}`)
            .then(response => response.text())
            .then(data => {
                if (data.trim() === "") {
                    finished = true; // no more products
                } else {
                    document.getElementById(containerId).insertAdjacentHTML("beforeend", data);
                    offset += limit;
                }
            })
            .finally(() => {
                loading = false;
                document.getElementById("loader").style.display = "none";
            });
    }

    if (path === "shop.php") {
        // Initial load
        loadProducts("../app/ajax/fetchproduct.php", "product-container");

        // Infinite scroll
        window.addEventListener("scroll", () => {
            if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 300) {
                loadProducts("../app/ajax/fetchproduct.php", "product-container");
            }
        });

    } else if (path === "viewcategory.php") {
        // Initial load
        loadProducts("../app/ajax/fetchproductbycat.php", "product-category-container");

        // Infinite scroll
        window.addEventListener("scroll", () => {
            if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 300) {
                loadProducts("../app/ajax/fetchproductbycat.php", "product-category-container");
            }
        });
    }
});
