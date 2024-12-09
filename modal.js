// To make sure the script runs only when DOM finished loading
document.addEventListener("DOMContentLoaded", () => {
    // Variables for Modal Elements
    const popupOverlay = document.getElementById("popupOverlay");
    const popupBody = document.getElementById("popupBody");
    const closePopup = document.getElementById("closePopup");
    
    // Handle "View Details" button click to open the modal and fetch room details
    document.addEventListener("click", async (event) => {
        if (event.target.classList.contains("view-details")) {
            const roomId = event.target.getAttribute("data-room-id");

            // Show loading message in modal
            popupBody.innerHTML = "<p>Loading details...</p>";
            popupOverlay.classList.add("active"); // Show modal overlay

            try {
                // Fetch room details from (room_details.php) using async/await

                // Sends a GET request to (room_details.php) with the room_id as a query parameter
                const response = await fetch(`room_details.php?room_id=${roomId}`);
                // Error handling
                if (!response.ok) {
                    throw new Error("Failed to load room details.");
                }

                // Store in constant "data" the result of (room_details.php)
                const data = await response.json();
                
                if (data.error) {
                    throw new Error(data.error);
                }

                // Populate the modal with room details
                // Booking form for schedule
                // (generateScheduleHTML) Helper Function to generate the schedule HTML "Located at the end of the page"
                popupBody.innerHTML = `
                    <article>
                    <h2>${data.room_name}</h2>
                    <p>Capacity: ${data.capacity}</p>
                    <p>Equipment: ${data.equipment}</p>
                    <p>Floor: ${data.floor}</p>
                    <p>Department: ${data.department}</p>
                    </article>
                    <h3>Available Schedules</h3>

                    <form id="booking-form">
                        ${generateScheduleHTML(data.schedule)}
                        <button type="submit">Book Now</button>
                    </form>
                `;

                // Handle booking submission by getting the ID of the booking form in the modal
                const bookingForm = document.getElementById("booking-form");
                // Attaches a submit event listener to booking form
                bookingForm.addEventListener("submit", async function (e) {
                     // Prevent the form from reloading the page when submitting
                    e.preventDefault();
                    
                    // Get selected schedule
                    // Check if a radio button has been checked, if not do an ALERT
                    const selectedSchedule = bookingForm.querySelector('input[name="schedule_id"]:checked');
                    if (!selectedSchedule) {
                        alert("Please select a schedule.");
                        return;
                    }

                    const scheduleId = selectedSchedule.value;

                    // Submit booking request
                    try {
                        // Using async/await HTTP/POST request to send the selected schedule to (rooms_details.php)
                        const bookingResponse = await fetch('room_details.php', {
                            method: 'POST',
                            // Converts the schedule_id key-value pair into a string that conforms to the application/x-www-form-urlencoded format.
                            body: new URLSearchParams({
                                schedule_id: scheduleId
                            }),
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            }
                        });
                        
                        //Error Handling
                        const bookingData = await bookingResponse.json();
                        if (bookingData.success) {
                            alert(bookingData.success);
                            popupOverlay.classList.remove("active");
                        } else {
                            alert(bookingData.error || 'An error occurred during booking.');
                        }
                    } catch (error) {
                        console.error(error);
                        alert('Error while booking the room.');
                    }
                });

            } catch (error) {
                console.error(error);
                popupBody.innerHTML = `<p>Error: ${error.message}</p>`;
            }
        }
    });

    // Close the modal
    closePopup.addEventListener("click", () => {
        popupOverlay.classList.remove("active");
        popupBody.innerHTML = ""; // Clear content
    });

    // Close the modal if the overlay is clicked
    popupOverlay.addEventListener("click", (event) => {
        if (event.target === popupOverlay) {
            popupOverlay.classList.remove("active");
            popupBody.innerHTML = ""; // Clear content
        }
    });
});

// Helper function to generate schedule HTML
function generateScheduleHTML(schedule) {
    if (schedule.length === 0) {
        return "<p>No available schedules.</p>";
    }

    // .map method to create a new array
    return schedule.map(slot => {
        // slot is a single element (object) in the schedule array.
        if (slot.status === 'available') {
            return `
                <div>
                    <input type="radio" id="schedule-${slot.schedule_id}" name="schedule_id" value="${slot.schedule_id}" required>
                    <label for="schedule-${slot.schedule_id}">
                        ${slot.available_from} to ${slot.available_to}
                    </label>
                </div>
            `;
        }
        return '';

    //The .join('') method takes the arrays produced by .map and combines them into a single string.
    }).join('');
}
