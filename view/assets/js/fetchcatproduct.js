
let offset = 0;
const limit = 8;
let loading = false;
let finished = false;

function loadProducts() {
    if (loading || finished) return;
    loading = true;
    document.getElementById("loader").style.display = "block";

    fetch(`../app/ajax/fetchproductbycat.php?offset=${offset}&limit=${limit}`)
        .then(response => response.text())
        .then(data => {
            if (data.trim() === "") {
                finished = true; // no more products
            } else {
                document.getElementById("product-category-container").insertAdjacentHTML("beforeend", data);
                offset += limit;
            }
        })
        .finally(() => {
            loading = false;
            document.getElementById("loader").style.display = "none";
        });
}

// Initial load
loadProducts();

// Infinite scroll
window.addEventListener("scroll", () => {
    if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 300) {
        loadProducts();
    }
});
