// To make sure the script runs only when DOM finished loading
document.addEventListener("DOMContentLoaded", function () {
    // Select the button in (my_bookings.php) with id "cancel-booking" because it has the booking ID
    const cancelButtons = document.querySelectorAll(".cancel-booking");

    // We use AJAX for smoother user experience

    // A foreach loop to check each click event for each button pressed, so the cancellation process triggers
    cancelButtons.forEach(button => {
        button.addEventListener("click", async function () {
            // Get the bookingID from the button that has id "data-booking-id"
            const bookingId = button.getAttribute("data-booking-id");

            // Confirm if you want to cancel or not
            // the "confirm" function displays a confirmation dialog
            if (confirm("Are you sure you want to cancel this booking?")) {
                try {
                    // Fetch using AJAX from (my_bookings.php)
                    const response = await fetch("my_bookings.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded",
                        },
                        body: `booking_id=${bookingId}`
                    });

                    // We use "response.text" to convert the response to plain text string since it contains success or failure messages
                    const data = await response.text();

                    // Remove the booking element from the DOM if response = "successfully cancelled"
                    if (data.includes("successfully cancelled")) {
                        button.closest(".mybookings").remove();
                        alert("Booking successfully cancelled.");
                    } else {
                        alert("Failed to cancel booking: " + data);
                    }
                } catch (error) {
                    // Error Handling
                    console.error("Error:", error);
                    alert("An error occurred while canceling the booking.");
                }
            }
        });
    });
});
