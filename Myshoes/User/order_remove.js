function cancelOrder(orderId) {
    if (confirm("Are you sure you want to cancel this order?")) {
        // Create a new XMLHttpRequest object
        var xhr = new XMLHttpRequest();

        // Configure the request
        xhr.open("POST", "cancel_order.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

        // Define the callback function
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // Check the response from the server
                if (xhr.responseText.trim() == "Order cancelled successfully.") {
                    // If the order was cancelled successfully, remove the row from the table
                    var row = document.getElementById("order-row-" + orderId);
                    row.parentNode.removeChild(row);
                } else {
                    // If an error occurred, display an error message
                    alert("Error: " + xhr.responseText);
                }
            }
        };

        // Send the request with the order ID as parameter
        xhr.send("order_id=" + orderId);
    }
}
